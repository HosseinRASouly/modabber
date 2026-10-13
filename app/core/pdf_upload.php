<?php
declare(strict_types=1);

/**
 * Modabber Secure PDF Resume Upload Validator
 *
 * Validates PDF files entirely in memory without ever writing them to disk.
 * Applies size limits, magic bytes check, strict MIME validation, and active-content heuristics.
 */

function validate_resume_upload(array $file): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'فایل رزومه را انتخاب کنید.'];
    }

    // Step a & b: Check PHP upload error code
    switch ($file['error']) {
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'فایل رزومه را انتخاب کنید.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'حجم فایل بیش از حد مجاز است.'];
        case UPLOAD_ERR_OK:
            break;
        default:
            return ['success' => false, 'error' => 'بارگذاری فایل انجام نشد. دوباره تلاش کنید.'];
    }

    // Step c: Verify file was uploaded via HTTP POST
    $tmpPath = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmpPath)) {
        return ['success' => false, 'error' => 'فایل معتبر نمی‌باشد.'];
    }

    // Step d: Size verification
    $maxBytes = (int)app_config('APPLY_MAX_PDF_BYTES', 5242880);
    $size = filesize($tmpPath);
    if ($size === false || $size <= 0 || $size > $maxBytes) {
        return ['success' => false, 'error' => 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.'];
    }

    // Step e: Original extension check and strict finfo MIME validation
    $origName = (string)($file['name'] ?? 'resume.pdf');
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'docx', 'doc'];
    if (!in_array($ext, $allowedExts, true)) {
        return ['success' => false, 'error' => 'تنها فایل‌های PDF و Word (doc, docx) مجاز هستند.'];
    }

    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpPath);
    $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/x-zip'
    ];
    if (!in_array($mime, $allowedMimes, true)) {
        return ['success' => false, 'error' => 'فرمت فایل معتبر نیست. لطفاً فایل Word یا PDF معتبر انتخاب کنید.'];
    }

    // Read content into memory
    $content = file_get_contents($tmpPath);
    if ($content === false || strlen($content) === 0) {
        return ['success' => false, 'error' => 'خطا در خواندن محتوای فایل رزومه.'];
    }

    // Magic bytes verification
    if ($ext === 'pdf') {
        if (!str_starts_with($content, '%PDF-')) {
            return ['success' => false, 'error' => 'فایل PDF انتخاب‌شده ساختار استانداردی ندارد.'];
        }
        if (preg_match('#/(JavaScript|JS|Launch|EmbeddedFile|RichMedia)\b#i', $content)) {
            return [
                'success' => false,
                'error' => 'این فایل PDF قابل قبول نیست. لطفاً یک نسخه‌ی ساده از رزومه‌تان ارسال فرمایید.'
            ];
        }
    } elseif ($ext === 'docx') {
        // DOCX is a zip archive (magic bytes PK)
        if (!str_starts_with($content, "PK\x03\x04") && !str_starts_with($content, "PK\x05\x06")) {
            return ['success' => false, 'error' => 'فایل DOCX انتخاب‌شده معتبر نمی‌باشد.'];
        }
    } elseif ($ext === 'doc') {
        // Classic OLE compound document magic bytes (\xD0\xCF\x11\xE0)
        if (!str_starts_with($content, "\xD0\xCF\x11\xE0")) {
            return ['success' => false, 'error' => 'فایل DOC انتخاب‌شده معتبر نمی‌باشد.'];
        }
    }

    // Step i: Sanitize original filename for email body display only (strip control chars, truncate to 120)
    $cleanName = preg_replace('/[\p{Cc}]+/u', '', $origName) ?? 'resume.pdf';
    $cleanName = mb_substr($cleanName, 0, 120, 'UTF-8');

    return [
        'success'  => true,
        'content'  => $content,
        'filename' => $cleanName,
        'size'     => $size,
    ];
}
