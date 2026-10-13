<?php
declare(strict_types=1);

/**
 * Modabber Helper Functions
 */

/**
 * HTML Escaping for XSS prevention.
 */
function e(?string $val): string
{
    if ($val === null) {
        return '';
    }
    return htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Convert English and Arabic digits to Persian digits.
 */
function fa_digits(string|int|float $str): string
{
    $str = (string)$str;
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $converted = str_replace($en, $fa, $str);
    return str_replace($ar, $fa, $converted);
}

/**
 * Convert Persian and Arabic digits to English digits.
 */
function to_en_digits(string $str): string
{
    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $converted = str_replace($fa, $en, $str);
    return str_replace($ar, $en, $converted);
}

/**
 * Normalize number input: strip Persian comma separators, convert to English digits, parse to int.
 */
function normalize_number(string|int|float $str): int
{
    $clean = to_en_digits((string)$str);
    $clean = str_replace(['٬', ',', ' '], '', $clean);
    return (int)$clean;
}

/**
 * Format currency / money with Persian thousands separator "٬" and Persian digits.
 */
function format_money(int|float $amount): string
{
    $formatted = number_format((float)$amount, 0, '.', '٬');
    return fa_digits($formatted);
}

/**
 * Normalize Iranian phone numbers.
 * Converts digits to English, strips spaces/dashes/dots/parens, converts +98/0098/98 to 0.
 * Returns normalized 11-digit format starting with 0, or empty string if invalid.
 */
function normalize_phone(string $phone): string
{
    $clean = to_en_digits(trim($phone));
    $clean = preg_replace('/[\s\-\.\(\)\+]+/', '', $clean) ?? '';

    // Handle leading 0098 or 98
    if (str_starts_with($clean, '0098') && strlen($clean) === 14) {
        $clean = '0' . substr($clean, 4);
    } elseif (str_starts_with($clean, '98') && strlen($clean) === 12) {
        $clean = '0' . substr($clean, 2);
    }

    return $clean;
}

/**
 * Normalize Persian text:
 * Converts Arabic ي and ك to Persian ی and ک, collapses repeated spaces, trims,
 * and preserves zero-width non-joiner (ZWNJ U+200C).
 */
function normalize_fa_text(string $text): string
{
    // Arabic Yeh and Keheh to Persian
    $text = str_replace(
        ['ي', 'ك', 'ى', 'ئ', 'ؤ'],
        ['ی', 'ک', 'ی', 'ئ', 'ؤ'],
        $text
    );

    // Normalize multiple spaces except newlines
    $text = preg_replace('/[^\S\r\n]+/u', ' ', $text) ?? $text;
    return trim($text);
}

/**
 * Fetch a configuration value.
 */
function app_config(?string $key = null, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $configFile = APP_PATH . '/config/config.php';
        $config = file_exists($configFile) ? require $configFile : [];
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

/**
 * Cached setting helper.
 */
function setting(string $key, string $default = ''): string
{
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    try {
        $repo = new SettingRepository();
        $val = $repo->get($key, $default);
        $cache[$key] = $val;
        return $val;
    } catch (\Throwable $e) {
        return $default;
    }
}

/**
 * Generate full URL from relative path.
 */
function url(string $path = ''): string
{
    $baseUrl = rtrim((string)app_config('BASE_URL', ''), '/');
    $path = '/' . ltrim($path, '/');
    if ($path === '/') {
        return $baseUrl !== '' ? $baseUrl : '/';
    }
    return $baseUrl . $path;
}

/**
 * Asset URL with cache-busting (?v=filemtime).
 */
function asset(string $path): string
{
    $cleanPath = '/' . ltrim($path, '/');
    $filePath = PUBLIC_PATH . $cleanPath;
    $v = file_exists($filePath) ? (string)filemtime($filePath) : '1';
    return url($cleanPath) . '?v=' . $v;
}

/**
 * Validate that an external URL begins with http:// or https://.
 */
function is_valid_url(?string $url): bool
{
    if (empty($url)) {
        return false;
    }
    $scheme = parse_url($url, PHP_URL_SCHEME);
    return in_array($scheme, ['http', 'https'], true);
}

/**
 * Safe HTTP 303 Redirect.
 */
function redirect(string $url, int $statusCode = 303): never
{
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Log message to app/storage/logs/app.log without credentials or personal data.
 */
function app_log(string $message, string $level = 'info'): void
{
    $logFile = APP_PATH . '/storage/logs/app.log';
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    $entry = sprintf("[%s] [%s] %s%s", $timestamp, strtoupper($level), $message, PHP_EOL);
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

/**
 * Render a view page inside a layout.
 */
function render_view(string $page, array $vars = [], string $layout = 'public'): void
{
    extract($vars, EXTR_SKIP);
    $viewPath = APP_PATH . '/views/pages/' . $page . '.php';

    if (!file_exists($viewPath)) {
        throw new \RuntimeException("View file not found: {$page}");
    }

    // Capture content
    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    // Render inside layout
    $layoutPath = APP_PATH . '/views/layouts/' . $layout . '.php';
    if (!file_exists($layoutPath)) {
        throw new \RuntimeException("Layout file not found: {$layout}");
    }

    require $layoutPath;
}

/**
 * Render a reusable partial view.
 */
function render_partial(string $partial, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    $partialPath = APP_PATH . '/views/partials/' . $partial . '.php';

    if (!file_exists($partialPath)) {
        throw new \RuntimeException("Partial file not found: {$partial}");
    }

    require $partialPath;
}
