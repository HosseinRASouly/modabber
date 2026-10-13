<?php
declare(strict_types=1);

/**
 * Modabber Error Pages Controller
 */

require_once __DIR__ . '/../app/core/bootstrap.php';

$code = isset($_GET['code']) ? (int)$_GET['code'] : 404;

switch ($code) {
    case 403:
        http_response_code(403);
        render_view('403', ['title' => 'عدم دسترسی (۴۰۳) | ' . app_config('SHORT_NAME', 'مدبرنت')], 'public');
        break;
    case 500:
        http_response_code(500);
        render_view('500', ['title' => 'خطای سرور (۵۰۰) | ' . app_config('SHORT_NAME', 'مدبرنت')], 'public');
        break;
    default:
        http_response_code(404);
        render_view('404', ['title' => 'صفحه پیدا نشد (۴۰۴) | ' . app_config('SHORT_NAME', 'مدبرنت')], 'public');
        break;
}
