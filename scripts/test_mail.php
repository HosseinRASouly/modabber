<?php
declare(strict_types=1);

/**
 * Modabber CLI Script - Test Mail Configuration
 *
 * Usage: php scripts/test_mail.php
 */

if (PHP_SAPI !== 'cli') {
    header('HTTP/1.1 403 Forbidden');
    echo "This script must only be run via the CLI.\n";
    exit(1);
}

require_once __DIR__ . '/../app/core/bootstrap.php';

$to = (string)app_config('MAIL_TO_ADDRESS', 'info@example.com');
$driver = app_config('MAIL_DRIVER', 'smtp');
$host = app_config('MAIL_HOST', '');
$port = app_config('MAIL_PORT', 587);
$encryption = app_config('MAIL_ENCRYPTION', 'tls');

echo "==========================================\n";
echo "   " . app_config('SITE_NAME', 'مدبر') . " - آزمون ارسال ایمیل\n";
echo "==========================================\n";
echo "درایور: {$driver}\n";
if ($driver === 'smtp') {
    echo "میزبان SMTP: {$host}:{$port} ({$encryption})\n";
}
echo "گیرنده آزمایشی: {$to}\n";
echo "در حال ارسال ایمیل آزمایشی...\n\n";

$subject = 'ایمیل آزمایشی سامانه مدبر';
$htmlBody = '<div style="font-family:Tahoma,sans-serif;direction:rtl;padding:20px;background:#EEF9EE;color:#0B1711;"><h3>آزمون موفقیت‌آمیز ایمیل مدبر</h3><p>پیکربندی ارسال ایمیل سامانه با موفقیت کار می‌کند.</p></div>';
$textBody = "آزمون موفقیت‌آمیز ایمیل مدبر\nپیکربندی ارسال ایمیل سامانه با موفقیت کار می‌کند.";

$sent = send_mail($to, $subject, $htmlBody, $textBody, null);

if ($sent) {
    echo "نتیجه: [موفقیت‌آمیز] ایمیل آزمایشی با موفقیت به {$to} ارسال شد!\n";
} else {
    echo "نتیجه: [خطا] ارسال ایمیل با شکست مواجه شد. لطفاً فایل لاگ (app/storage/logs/app.log) و تنظیمات config.php را بررسی فرمایید.\n";
    exit(1);
}
