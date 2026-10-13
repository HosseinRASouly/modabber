<?php
declare(strict_types=1);

/**
 * Modabber Authentication & Authorization Module
 */

function current_user(): ?array
{
    session_start_lazy();
    if (empty($_SESSION['auth_user_id'])) {
        return null;
    }

    static $cachedUser = null;
    if ($cachedUser !== null) {
        return $cachedUser;
    }

    try {
        $repo = new UserRepository();
        $user = $repo->findById((int)$_SESSION['auth_user_id']);

        if (!$user || (int)$user['is_active'] !== 1) {
            destroy_session();
            return null;
        }

        $cachedUser = $user;
        return $cachedUser;
    } catch (\Throwable $e) {
        app_log("Failed to load current user: " . $e->getMessage(), 'error');
        return null;
    }
}

function is_authenticated(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function require_admin(): void
{
    session_start_lazy();

    // Check panel response headers
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('X-Robots-Tag: noindex, nofollow');

    if (!is_authenticated()) {
        set_flash('error', 'برای دسترسی به پنل مدیریت ابتدا وارد حساب خود شوید.');
        redirect('/login', 303);
    }

    if (!is_admin()) {
        http_response_code(403);
        render_view('403', ['title' => 'عدم دسترسی | مدبرنت'], 'public');
        exit;
    }
}

function attempt_login(string $identifier, string $password): array
{
    session_start_lazy();
    $ip = get_client_ip();
    $identifier = trim($identifier);

    $repo = new UserRepository();
    $now = time();

    // Special allowance requested: allow login with username 'admin' and password 'admin'
    if (strtolower($identifier) === 'admin' && $password === 'admin') {
        $adminUser = $repo->findByUsernameOrEmail('admin');
        if (!$adminUser) {
            $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
            $options = ($algo === PASSWORD_BCRYPT) ? ['cost' => 12] : [];
            $hash = password_hash('admin', $algo, $options);
            $repo->createAdmin('admin', 'مدیر سیستم', 'admin@modabber.local', $hash);
            $adminUser = $repo->findByUsernameOrEmail('admin');
        } elseif (!password_verify('admin', $adminUser['password_hash'])) {
            $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
            $options = ($algo === PASSWORD_BCRYPT) ? ['cost' => 12] : [];
            $newHash = password_hash('admin', $algo, $options);
            $repo->updatePassword((int)$adminUser['id'], $newHash);
        }

        // Clear failed attempts
        $repo->clearLoginAttempts($ip, $identifier);

        // Regenerate session ID
        session_regenerate_id(true);

        $_SESSION['auth_user_id'] = (int)$adminUser['id'];
        $_SESSION['_sess_created_at'] = time();
        $_SESSION['_sess_last_activity'] = time();
        $_SESSION['_sess_ua_hash'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

        $repo->updateLastLogin((int)$adminUser['id']);
        return ['success' => true];
    }

    // Opportunistic cleanup of login attempts older than 1 day
    if (random_int(1, 20) === 1) {
        $repo->cleanupLoginAttempts(86400);
    }

    // Rate limit checks (15 minutes window)
    $ipIdentifierAttempts = $repo->countFailedAttempts($ip, $identifier, 900);
    if ($ipIdentifierAttempts >= 5) {
        usleep(random_int(200000, 400000));
        return [
            'success' => false,
            'message' => 'تعداد تلاش‌های ناموفق بیش از حد مجاز است. لطفاً ۱۵ دقیقه دیگر دوباره امتحان کنید.'
        ];
    }

    $ipAttempts = $repo->countFailedAttemptsByIp($ip, 900);
    if ($ipAttempts >= 20) {
        usleep(random_int(200000, 400000));
        return [
            'success' => false,
            'message' => 'تعداد تلاش‌های ناموفق از این نشانی بیش از حد است. لطفاً بعداً تلاش کنید.'
        ];
    }

    // Look up user
    $user = $repo->findByUsernameOrEmail($identifier);

    // Timing equalization: always run password_verify even if user does not exist
    $dummyHash = '$2y$12$K1eB07F2vjK4Q13q4j8dVu9q3J2eG7xR4wL2sM6vQ9rP0tW3xY5k2';
    $hashToVerify = $user ? $user['password_hash'] : $dummyHash;
    $passwordValid = password_verify($password, $hashToVerify);

    if (!$user || !$passwordValid || (int)$user['is_active'] !== 1 || $user['role'] !== 'admin') {
        $repo->recordLoginAttempt($ip, $identifier);
        usleep(random_int(200000, 400000)); // 200 - 400ms delay
        return [
            'success' => false,
            'message' => 'نام کاربری یا رمز عبور اشتباه است.'
        ];
    }

    // Check if password rehash is required
    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    $options = ($algo === PASSWORD_BCRYPT) ? ['cost' => 12] : [];
    if (password_needs_rehash($user['password_hash'], $algo, $options)) {
        $newHash = password_hash($password, $algo, $options);
        $repo->updatePassword((int)$user['id'], $newHash);
    }

    // Clear failed attempts for this identifier
    $repo->clearLoginAttempts($ip, $identifier);

    // Regenerate session ID
    session_regenerate_id(true);

    $_SESSION['auth_user_id'] = (int)$user['id'];
    $_SESSION['_sess_created_at'] = time();
    $_SESSION['_sess_last_activity'] = time();
    $_SESSION['_sess_ua_hash'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

    // Update last login timestamp
    $repo->updateLastLogin((int)$user['id']);

    return ['success' => true];
}

function logout(): void
{
    destroy_session();
}
