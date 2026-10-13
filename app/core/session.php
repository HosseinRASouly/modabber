<?php
declare(strict_types=1);

/**
 * Modabber Secure Session Management
 */

function session_start_lazy(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $sessionSavePath = APP_PATH . '/storage/sessions';
    if (!is_dir($sessionSavePath)) {
        @mkdir($sessionSavePath, 0700, true);
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.save_path', $sessionSavePath);
    ini_set('session.gc_maxlifetime', '28800'); // 8 hours

    session_name('modabber_sess');
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie expires when browser closes
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    // Check Idle Timeout (30 minutes) & Absolute Lifetime (8 hours)
    $now = time();
    $idleTimeout = 1800; // 30 minutes
    $absoluteLifetime = 28800; // 8 hours

    if (isset($_SESSION['_sess_created_at']) && ($now - $_SESSION['_sess_created_at'] > $absoluteLifetime)) {
        destroy_session();
        return;
    }

    if (isset($_SESSION['_sess_last_activity']) && ($now - $_SESSION['_sess_last_activity'] > $idleTimeout)) {
        destroy_session();
        return;
    }

    $_SESSION['_sess_last_activity'] = $now;
    if (!isset($_SESSION['_sess_created_at'])) {
        $_SESSION['_sess_created_at'] = $now;
    }

    // Verify User-Agent hash to protect against session hijacking
    $uaHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
    if (!isset($_SESSION['_sess_ua_hash'])) {
        $_SESSION['_sess_ua_hash'] = $uaHash;
    } elseif (!hash_equals($_SESSION['_sess_ua_hash'], $uaHash)) {
        destroy_session();
        return;
    }
}

/**
 * Destroy current session completely.
 */
function destroy_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    @session_destroy();
}
