<?php
declare(strict_types=1);

/**
 * Modabber Security & Headers Module
 */

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    // Remove identifying header
    header_remove('X-Powered-By');

    // Content Security Policy: strictly no inline scripts/styles in public HTML
    $csp = "default-src 'self'; "
        . "img-src 'self' data:; "
        . "style-src 'self'; "
        . "script-src 'self'; "
        . "font-src 'self'; "
        . "connect-src 'self'; "
        . "object-src 'none'; "
        . "base-uri 'self'; "
        . "form-action 'self'; "
        . "frame-ancestors 'none'";

    header('Content-Security-Policy: ' . $csp);
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

    // HSTS in production HTTPS environments
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    if ($isHttps && app_config('APP_ENV') === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Force HTTPS redirect if configured
    if (!$isHttps && app_config('FORCE_HTTPS')) {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        header('Location: https://' . $host . $uri, true, 301);
        exit;
    }
}

/**
 * Get Client IP safely. Only trusts X-Forwarded-For if TRUSTED_PROXY is enabled.
 */
function get_client_ip(): string
{
    if (app_config('TRUSTED_PROXY') && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($ips[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
}
