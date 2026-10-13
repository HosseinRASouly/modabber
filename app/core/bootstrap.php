<?php
declare(strict_types=1);

/**
 * Modabber Application Bootstrap
 */

// 1. PHP Version Check
if (PHP_VERSION_ID < 80100) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'PHP 8.1 or higher is required to run Modabber.';
    exit(1);
}

// 2. Define Core Path Constants
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', dirname(__DIR__, 2) . '/public');
}

// 3. Timezone
date_default_timezone_set('Asia/Tehran');

// 4. Load Configuration
$config = require APP_PATH . '/config/config.php';

// 5. Check Required PHP Extensions
$requiredExtensions = ['pdo', 'mbstring', 'fileinfo', 'gd', 'openssl'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    header('HTTP/1.1 500 Internal Server Error');
    if ($config['APP_DEBUG']) {
        echo 'Required PHP extensions are missing: ' . htmlspecialchars(implode(', ', $missingExtensions));
    } else {
        echo 'خطای پیکربندی سرور رخ داده است. لطفاً با مدیر سیستم تماس بگیرید.';
    }
    exit(1);
}

// 6. Error & Exception Handling
ini_set('display_errors', $config['APP_DEBUG'] ? '1' : '0');
ini_set('display_startup_errors', $config['APP_DEBUG'] ? '1' : '0');
error_reporting($config['APP_DEBUG'] ? E_ALL : 0);

// Ensure storage directories exist
$dirsToEnsure = [
    APP_PATH . '/storage/logs',
    APP_PATH . '/storage/sessions',
    PUBLIC_PATH . '/uploads/projects',
    PUBLIC_PATH . '/uploads/logos',
];
foreach ($dirsToEnsure as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0755, true);
    }
}

// 7. Load Core Helpers & Classes
require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/jalali.php';
require_once APP_PATH . '/core/security.php';
require_once APP_PATH . '/core/session.php';
require_once APP_PATH . '/core/flash.php';
require_once APP_PATH . '/core/csrf.php';
require_once APP_PATH . '/core/db.php';
require_once APP_PATH . '/core/auth.php';
require_once APP_PATH . '/core/ratelimit.php';
require_once APP_PATH . '/core/upload.php';
require_once APP_PATH . '/core/pdf_upload.php';
require_once APP_PATH . '/core/mailer.php';
require_once APP_PATH . '/views/partials/icons.php';

// Load Repositories
require_once APP_PATH . '/repositories/UserRepository.php';
require_once APP_PATH . '/repositories/ProjectRepository.php';
require_once APP_PATH . '/repositories/LogoRepository.php';
require_once APP_PATH . '/repositories/SettingRepository.php';
require_once APP_PATH . '/repositories/FormSubmissionRepository.php';

// Global Exception Handler
set_exception_handler(function (\Throwable $e): void {
    app_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine(), 'critical');

    if (PHP_SAPI === 'cli') {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }

    if (!headers_sent()) {
        http_response_code(500);
        send_security_headers();
    }

    if (app_config('APP_DEBUG')) {
        echo "<pre dir='ltr' style='padding:20px;background:#fff;color:#c00;'>";
        echo "<strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
        echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "\n";
        echo "<strong>Trace:</strong>\n" . htmlspecialchars($e->getTraceAsString());
        echo "</pre>";
    } else {
        try {
            render_view('500', ['title' => 'خطای سرور | مدبرنت'], 'public');
        } catch (\Throwable) {
            echo '<!DOCTYPE html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><title>خطای سرور</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h2>خطای داخلی سرور (۵۰۰)</h2><p>متأسفانه در پردازش درخواست شما خطایی رخ داد.</p></body></html>';
        }
    }
    exit(1);
});

// Send security headers for all web requests
if (PHP_SAPI !== 'cli') {
    send_security_headers();
}
