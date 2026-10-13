<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - Logout Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

session_start_lazy();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است.');
        redirect(url('/panel/profile'), 303);
    }

    logout();
    session_start_lazy();
    set_flash('success', 'با موفقیت خارج شدید.');
    redirect(url('/login'), 303);
}

// Fallback for unexpected GET
redirect(url('/panel/profile'), 303);
