<?php
declare(strict_types=1);

/**
 * Modabber - Login Controller
 */

require_once __DIR__ . '/../app/core/bootstrap.php';

session_start_lazy();

// If already logged in as admin, redirect directly to profile
if (is_authenticated() && is_admin()) {
    redirect(url('/panel/profile'), 303);
}

// POST: Handle Login Attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است. لطفاً صفحه را بارگذاری مجدد نمایید.');
        redirect(url('/login'), 303);
    }

    $identifier = (string)($_POST['identifier'] ?? '');
    $password   = (string)($_POST['password'] ?? '');

    $result = attempt_login($identifier, $password);

    if ($result['success']) {
        // Successful login: redirect only to fixed admin route
        set_flash('success', 'با موفقیت وارد شدید.');
        redirect(url('/panel/profile'), 303);
    } else {
        set_flash('error', $result['message']);
        redirect(url('/login'), 303);
    }
}

// GET: Render Login Page
$title = 'ورود به پنل کاربری | ' . app_config('SHORT_NAME', 'مدبرنت');

render_view('login', [
    'title' => $title,
], 'public');
