<?php
declare(strict_types=1);

/**
 * Modabber Secure Image Upload Module
 *
 * Implements strict type checking, GD re-encoding to strip malicious payloads,
 * dimension verification, and safe random naming.
 */

function handle_image_upload(
    array $file,
    string $folder = 'projects',
    int $maxBytes = 4194304, // 4 MB default for projects
    int $maxWidth = 1600,
    int $maxHeight = 1600
): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'خطا در دریافت فایل تصویر.'];
    }

    // Check PHP upload error
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'هیچ تصویری انتخاب نشده است.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'حجم تصویر بیش از حد مجاز است.'];
        default:
            return ['success' => false, 'error' => 'خطا در بارگذاری فایل. لطفاً دوباره تلاش کنید.'];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'فایل معتبر نمی‌باشد.'];
    }

    $tmpPath = $file['tmp_name'];
    $fileSize = filesize($tmpPath);

    if ($fileSize === false || $fileSize <= 0 || $fileSize > $maxBytes) {
        $mb = round($maxBytes / 1048576);
        return ['success' => false, 'error' => "حجم تصویر نباید بیشتر از {$mb} مگابایت باشد."];
    }

    // Check extension
    $originalName = (string)($file['name'] ?? '');
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowedExts, true)) {
        return ['success' => false, 'error' => 'تنها پسوندهای JPG، PNG و WebP مجاز هستند.'];
    }

    // Strict MIME type verification via finfo (never trust client-supplied header)
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpPath);
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedMimes[$mime])) {
        return ['success' => false, 'error' => 'فرمت فایل تصویر نامعتبر است.'];
    }

    // Check dimensions via getimagesize
    $imageInfo = @getimagesize($tmpPath);
    if ($imageInfo === false) {
        return ['success' => false, 'error' => 'فایل انتخاب‌شده تصویر معتبری نیست.'];
    }

    [$srcWidth, $srcHeight] = $imageInfo;
    if ($srcWidth > 6000 || $srcHeight > 6000) {
        return ['success' => false, 'error' => 'ابعاد تصویر بیش از حد بزرگ است (حداکثر ۶۰۰۰ پیکسل).'];
    }

    if (!extension_loaded('gd')) {
        return ['success' => false, 'error' => 'ماژول GD برای پردازش تصویر در سرور فعال نیست.'];
    }

    // Load image through GD
    $srcImg = null;
    switch ($mime) {
        case 'image/jpeg':
            $srcImg = @imagecreatefromjpeg($tmpPath);
            break;
        case 'image/png':
            $srcImg = @imagecreatefrompng($tmpPath);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) {
                return ['success' => false, 'error' => 'سرور از فرمت WebP پشتیبانی نمی‌کند.'];
            }
            $srcImg = @imagecreatefromwebp($tmpPath);
            break;
    }

    if (!$srcImg) {
        return ['success' => false, 'error' => 'تصویر مخدوش است و پردازش نشد.'];
    }

    // Calculate dimensions
    $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1.0);
    $targetWidth  = (int)round($srcWidth * $ratio);
    $targetHeight = (int)round($srcHeight * $ratio);

    $dstImg = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preserve transparency for PNG and WebP
    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
        imagefilledrectangle($dstImg, 0, 0, $targetWidth, $targetHeight, $transparent);
    }

    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);

    // Target filename: 32 hex chars + extension
    $targetExt = ($mime === 'image/jpeg') ? 'jpg' : ($mime === 'image/png' ? 'png' : 'webp');
    $filename = bin2hex(random_bytes(16)) . '.' . $targetExt;

    // Validate safe path
    $uploadDir = PUBLIC_PATH . '/uploads/' . ($folder === 'logos' ? 'logos' : 'projects');
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $destPath = $uploadDir . '/' . $filename;

    // Save and re-encode to strip metadata
    $saved = false;
    switch ($targetExt) {
        case 'jpg':
            $saved = imagejpeg($dstImg, $destPath, 85);
            break;
        case 'png':
            $saved = imagepng($dstImg, $destPath, 8);
            break;
        case 'webp':
            $saved = imagewebp($dstImg, $destPath, 85);
            break;
    }

    imagedestroy($srcImg);
    imagedestroy($dstImg);

    if (!$saved) {
        return ['success' => false, 'error' => 'خطا در ذخیره‌سازی فایل پردازش شده.'];
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Safely delete an uploaded file by filename.
 */
function delete_uploaded_file(string $filename, string $folder = 'projects'): bool
{
    if (empty($filename)) {
        return false;
    }

    // Validate filename strictly against ^[a-f0-9]{32}\.(jpg|png|webp)$
    if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $filename)) {
        return false;
    }

    $uploadDir = PUBLIC_PATH . '/uploads/' . ($folder === 'logos' ? 'logos' : 'projects');
    $filePath = $uploadDir . '/' . $filename;

    if (file_exists($filePath) && is_file($filePath)) {
        return @unlink($filePath);
    }

    return false;
}
