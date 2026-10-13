<?php
declare(strict_types=1);

/**
 * Modabber Mailer Module
 *
 * Implements clean, self-contained SMTP and mail() transports without third-party dependencies.
 * Prevents header injection, enforces RFC 2047 encoding, strict multipart MIME structure,
 * and secure credential handling.
 */

function send_mail(
    string $to,
    string $subject,
    string $htmlBody,
    string $textBody,
    ?array $attachment = null
): bool {
    $fromAddress = app_config('MAIL_FROM_ADDRESS', 'no-reply@example.com');
    $fromName    = app_config('MAIL_FROM_NAME', 'مدبر');

    // Strict email address validation
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
        app_log("Invalid mail recipient or sender address.", 'error');
        return false;
    }

    // Sanitize subject: remove CR, LF, NUL; truncate to 150 chars
    $cleanSubject = str_replace(["\r", "\n", "\0"], '', $subject);
    $cleanSubject = mb_substr($cleanSubject, 0, 150, 'UTF-8');
    $encodedSubject = mb_encode_mimeheader($cleanSubject, 'UTF-8', 'B', "\r\n");

    // Clean from name
    $cleanFromName = str_replace(["\r", "\n", "\0"], '', $fromName);
    $encodedFromName = mb_encode_mimeheader($cleanFromName, 'UTF-8', 'B', "\r\n");

    $mixedBoundary = '----=_Part_Mixed_' . bin2hex(random_bytes(16));
    $altBoundary   = '----=_Part_Alt_' . bin2hex(random_bytes(16));

    // Message-ID host
    $baseUrlHost = parse_url((string)app_config('BASE_URL', ''), PHP_URL_HOST);
    $host = $baseUrlHost ?: ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $msgId = sprintf('<%s.%s@%s>', bin2hex(random_bytes(8)), time(), $host);

    $dateHeader = date('r');

    // Construct headers
    $headers = [
        "From: {$encodedFromName} <{$fromAddress}>",
        "To: <{$to}>",
        "Date: {$dateHeader}",
        "Message-ID: {$msgId}",
        "MIME-Version: 1.0",
        "Content-Type: multipart/mixed; boundary=\"{$mixedBoundary}\"",
    ];

    // Build MIME message body
    $eol = "\r\n";
    $body = "--{$mixedBoundary}" . $eol;
    $body .= "Content-Type: multipart/alternative; boundary=\"{$altBoundary}\"" . $eol . $eol;

    // Plain text part
    $body .= "--{$altBoundary}" . $eol;
    $body .= "Content-Type: text/plain; charset=UTF-8" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol . $eol;
    $body .= chunk_split(base64_encode($textBody), 76, $eol);

    // HTML part
    $body .= "--{$altBoundary}" . $eol;
    $body .= "Content-Type: text/html; charset=UTF-8" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol . $eol;
    $body .= chunk_split(base64_encode($htmlBody), 76, $eol);

    $body .= "--{$altBoundary}--" . $eol;

    // Attachment (PDF resume)
    if ($attachment !== null && !empty($attachment['content'])) {
        $body .= $eol . "--{$mixedBoundary}" . $eol;
        $body .= "Content-Type: application/pdf; name=\"resume.pdf\"" . $eol;
        $body .= "Content-Disposition: attachment; filename=\"resume.pdf\"" . $eol;
        $body .= "Content-Transfer-Encoding: base64" . $eol . $eol;
        $body .= chunk_split(base64_encode($attachment['content']), 76, $eol);
    }

    $body .= $eol . "--{$mixedBoundary}--" . $eol;

    $driver = app_config('MAIL_DRIVER', 'mail');

    if ($driver === 'smtp') {
        return send_smtp($to, $fromAddress, $encodedSubject, $headers, $body);
    }

    // Default driver: PHP mail()
    return send_php_mail($to, $encodedSubject, $headers, $body, $fromAddress);
}

/**
 * Native PHP mail() transport.
 * Note: Less reliable than SMTP on many shared hosts without proper postfix configuration.
 */
function send_php_mail(
    string $to,
    string $encodedSubject,
    array $headers,
    string $body,
    string $fromAddress
): bool {
    // Remove To, Subject from additional headers when passing to mail()
    $extraHeaders = [];
    foreach ($headers as $h) {
        if (!str_starts_with($h, 'To:') && !str_starts_with($h, 'Subject:')) {
            $extraHeaders[] = $h;
        }
    }

    $headerStr = implode("\r\n", $extraHeaders);
    $extraParam = '-f' . escapeshellarg($fromAddress);

    $ok = @mail($to, $encodedSubject, $body, $headerStr, $extraParam);
    if (!$ok) {
        app_log("mail() transport returned false.", 'error');
    }
    return $ok;
}

/**
 * Socket-based SMTP transport implementation.
 */
function send_smtp(
    string $to,
    string $fromAddress,
    string $encodedSubject,
    array $headers,
    string $body
): bool {
    $host       = (string)app_config('MAIL_HOST', 'localhost');
    $port       = (int)app_config('MAIL_PORT', 587);
    $encryption = strtolower((string)app_config('MAIL_ENCRYPTION', 'tls'));
    $username   = (string)app_config('MAIL_USERNAME', '');
    $password   = (string)app_config('MAIL_PASSWORD', '');
    $verifyTls  = (bool)app_config('MAIL_VERIFY_TLS', true);

    $remoteHost = ($encryption === 'ssl') ? "ssl://{$host}" : $host;
    $context = stream_context_create([
        'ssl' => [
            'verify_peer'      => $verifyTls,
            'verify_peer_name' => $verifyTls,
            'peer_name'        => $host,
            'allow_self_signed'=> !$verifyTls,
        ]
    ]);

    $socket = @stream_socket_client(
        "{$remoteHost}:{$port}",
        $errno,
        $errstr,
        15,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$socket) {
        app_log("SMTP connection failed: [{$errno}] {$errstr}", 'error');
        return false;
    }

    stream_set_timeout($socket, 15);

    $readResponse = function () use ($socket): array {
        $data = '';
        while (!feof($socket)) {
            $line = fgets($socket, 512);
            if ($line === false) {
                break;
            }
            $data .= $line;
            // SMTP response format: 3 digits, followed by space or hyphen
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $code = (int)substr($data, 0, 3);
        return ['code' => $code, 'text' => trim($data)];
    };

    $sendCommand = function (string $cmd) use ($socket, $readResponse): array {
        fwrite($socket, $cmd . "\r\n");
        return $readResponse();
    };

    try {
        // Initial greeting: expect 220
        $res = $readResponse();
        if ($res['code'] !== 220) {
            throw new \RuntimeException("Greeting failed: {$res['code']}");
        }

        $ehloName = parse_url((string)app_config('BASE_URL', ''), PHP_URL_HOST) ?: 'localhost';

        // EHLO
        $res = $sendCommand("EHLO {$ehloName}");
        if ($res['code'] !== 250) {
            throw new \RuntimeException("EHLO failed: {$res['code']}");
        }

        // STARTTLS if configured
        if ($encryption === 'tls') {
            $res = $sendCommand("STARTTLS");
            if ($res['code'] !== 220) {
                throw new \RuntimeException("STARTTLS failed: {$res['code']}");
            }

            $cryptoMethod = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            $cryptoOk = stream_socket_enable_crypto($socket, true, $cryptoMethod);
            if (!$cryptoOk) {
                throw new \RuntimeException("TLS negotiation failed.");
            }

            // EHLO again after TLS
            $res = $sendCommand("EHLO {$ehloName}");
            if ($res['code'] !== 250) {
                throw new \RuntimeException("EHLO after TLS failed: {$res['code']}");
            }
        }

        // AUTH LOGIN if credentials provided
        if (!empty($username)) {
            $res = $sendCommand("AUTH LOGIN");
            if ($res['code'] !== 334) {
                throw new \RuntimeException("AUTH LOGIN start failed: {$res['code']}");
            }

            $res = $sendCommand(base64_encode($username));
            if ($res['code'] !== 334) {
                throw new \RuntimeException("AUTH username failed: {$res['code']}");
            }

            $res = $sendCommand(base64_encode($password));
            if ($res['code'] !== 235) {
                throw new \RuntimeException("AUTH password failed: {$res['code']}");
            }
        }

        // MAIL FROM
        $res = $sendCommand("MAIL FROM:<{$fromAddress}>");
        if ($res['code'] !== 250) {
            throw new \RuntimeException("MAIL FROM failed: {$res['code']}");
        }

        // RCPT TO
        $res = $sendCommand("RCPT TO:<{$to}>");
        if ($res['code'] !== 250 && $res['code'] !== 251) {
            throw new \RuntimeException("RCPT TO failed: {$res['code']}");
        }

        // DATA
        $res = $sendCommand("DATA");
        if ($res['code'] !== 354) {
            throw new \RuntimeException("DATA command failed: {$res['code']}");
        }

        // Build full message with dot-stuffing
        // Add Subject header to the message block
        $fullHeaders = array_merge(["Subject: {$encodedSubject}"], $headers);
        $headerBlock = implode("\r\n", $fullHeaders);
        $fullPayload = $headerBlock . "\r\n\r\n" . $body;

        // SMTP dot-stuffing: leading dot on any line must be doubled
        $stuffed = preg_replace('/(\r?\n)\./', '$1..', $fullPayload) ?? $fullPayload;
        if (str_starts_with($stuffed, '.')) {
            $stuffed = '.' . $stuffed;
        }

        fwrite($socket, $stuffed . "\r\n.\r\n");
        $res = $readResponse();
        if ($res['code'] !== 250) {
            throw new \RuntimeException("Message sending failed: {$res['code']}");
        }

        // QUIT
        $sendCommand("QUIT");
        fclose($socket);
        return true;
    } catch (\Throwable $e) {
        app_log("SMTP Error: " . $e->getMessage(), 'error');
        @fclose($socket);
        return false;
    }
}
