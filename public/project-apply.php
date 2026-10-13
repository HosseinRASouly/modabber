<?php
declare(strict_types=1);

/**
 * Modabber - Application Form Submission Handler
 *
 * Implements strict security ordering, memory-only PDF processing,
 * rate limiting, honeypot protection, and email delivery.
 */

require_once __DIR__ . '/../app/core/bootstrap.php';

set_time_limit(60);
session_start_lazy();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$redirectTarget = url('/project/' . $id . '#apply');

// Step 1: Method check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('/project/' . $id), 303);
}

// Step 2: Oversized request check BEFORE CSRF check
$contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($contentLength > 0 && empty($_POST) && empty($_FILES)) {
    set_flash('error', 'حجم فایل ارسالی بیش از حد مجاز است. فایل کوچک‌تری انتخاب کنید.');
    redirect($redirectTarget, 303);
}

// Step 3: CSRF verification
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    set_flash('error', 'نشست شما منقضی شده است. صفحه را دوباره بارگذاری کنید و مجدداً تلاش کنید.');
    redirect($redirectTarget, 303);
}

// Step 4: Load published project
if ($id <= 0) {
    http_response_code(404);
    render_view('404', ['title' => 'پروژه پیدا نشد | مدبرنت'], 'public');
    exit;
}

$projectRepo = new ProjectRepository();
$project = $projectRepo->getPublishedById($id);
if (!$project) {
    http_response_code(404);
    render_view('404', ['title' => 'پروژه پیدا نشد | مدبرنت'], 'public');
    exit;
}

$ip = get_client_ip();
$submissionRepo = new FormSubmissionRepository();

// Step 5: Honeypot check
$hpUrl = trim((string)($_POST['hp_url'] ?? ''));
if (!empty($hpUrl)) {
    // Silently pretend success
    $submissionRepo->log($ip, $id, 'rejected');
    set_flash('success', 'درخواست شما با موفقیت ارسال شد. در اسرع وقت با شما تماس می‌گیریم.');
    redirect($redirectTarget, 303);
}

// Step 6: Nonce & Timing verification
$formNonce = trim((string)($_POST['form_nonce'] ?? ''));
$storedNonceData = $_SESSION['apply_nonces'][$id] ?? null;

if (
    empty($storedNonceData) ||
    !is_array($storedNonceData) ||
    empty($storedNonceData['nonce']) ||
    !hash_equals($storedNonceData['nonce'], $formNonce)
) {
    set_flash('error', 'این فرم قبلاً ارسال شده یا منقضی شده است. صفحه را دوباره بارگذاری کنید.');
    redirect($redirectTarget, 303);
}

$minSeconds = (int)app_config('APPLY_MIN_FILL_SECONDS', 3);
$issuedAt = (int)($storedNonceData['issued_at'] ?? 0);
if (time() - $issuedAt < $minSeconds) {
    // Silently treat as bot
    set_flash('error', 'نشست شما منقضی شده است. صفحه را دوباره بارگذاری کنید و مجدداً تلاش کنید.');
    redirect($redirectTarget, 303);
}

// Step 7: Field validation
$errors = [];
$oldInput = [];

// Full Name Validation
$rawName = (string)($_POST['full_name'] ?? '');
$name = normalize_fa_text($rawName);
$oldInput['full_name'] = $name;

// Reject control characters category Cc ONLY (keep category Cf for ZWNJ U+200C!)
if (preg_match('/[\p{Cc}]/u', $name)) {
    $errors['full_name'] = 'نام شامل نویسه‌های غیرمجاز است.';
} elseif (mb_strlen($name, 'UTF-8') === 0) {
    $errors['full_name'] = 'نام و نام خانوادگی را وارد کنید.';
} elseif (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
    $errors['full_name'] = 'نام باید بین ۲ تا ۱۰۰ نویسه باشد.';
}

// Phone Validation
$rawPhone = (string)($_POST['phone'] ?? '');
$normalizedPhone = normalize_phone($rawPhone);
$oldInput['phone'] = $rawPhone;

if (empty($normalizedPhone) || !preg_match('/^0[1-9][0-9]{9}$/', $normalizedPhone)) {
    $errors['phone'] = 'شماره تماس معتبر وارد کنید (مثال: ۰۹۱۲۱۲۳۴۵۶۷).';
}

// Message Validation (Optional, max 2000)
$rawMsg = (string)($_POST['message'] ?? '');
$msg = normalize_fa_text($rawMsg);
// Allow \n and \t, strip other control chars
$msg = preg_replace('/[^\P{Cc}\n\t]+/u', '', $msg) ?? $msg;
$oldInput['message'] = $msg;

if (mb_strlen($msg, 'UTF-8') > 2000) {
    $errors['message'] = 'توضیحات نباید بیشتر از ۲۰۰۰ نویسه باشد.';
}

// Resume PDF Validation
$resumeResult = validate_resume_upload($_FILES['resume'] ?? []);
if (!$resumeResult['success']) {
    $errors['resume'] = $resumeResult['error'];
}

// If validation failed, preserve input & errors and redirect back
if (!empty($errors)) {
    set_flash('apply_errors', $errors);
    set_flash('apply_old', $oldInput);
    redirect($redirectTarget, 303);
}

// Step 8: Rate Limit Check
$rateLimit = check_apply_rate_limit($ip);
if (!$rateLimit['allowed']) {
    set_flash('error', $rateLimit['message']);
    redirect($redirectTarget, 303);
}

// Step 9: Consume Nonce & Send Email
unset($_SESSION['apply_nonces'][$id]);

$submissionId = $submissionRepo->log($ip, $id, 'pending');

// Build email templates
$projectUrl = url('/project/' . $id);
$sentAt = jdate('Y/m/d - H:i:s');
$emailVars = [
    'project'         => $project,
    'full_name'       => $name,
    'phone'           => $normalizedPhone,
    'message'         => $msg,
    'resume_filename' => $resumeResult['filename'],
    'sent_at'         => $sentAt,
    'ip_address'      => $ip,
    'project_url'     => $projectUrl,
];

// Render HTML email body
ob_start();
extract($emailVars, EXTR_SKIP);
require APP_PATH . '/views/emails/application_html.php';
$htmlBody = ob_get_clean();

// Render plain text email body
ob_start();
extract($emailVars, EXTR_SKIP);
require APP_PATH . '/views/emails/application_text.php';
$textBody = ob_get_clean();

// Subject line
$subject = "درخواست همکاری جدید در پروژه «{$project['name']}» – {$name}";
$toAddress = (string)app_config('MAIL_TO_ADDRESS', 'info@example.com');

$sent = send_mail(
    $toAddress,
    $subject,
    $htmlBody,
    $textBody,
    [
        'content'  => $resumeResult['content'],
        'filename' => 'resume.pdf',
    ]
);

// Free memory
unset($resumeResult['content']);

if ($sent) {
    $submissionRepo->updateStatus($submissionId, 'sent');
    set_flash('success', 'درخواست شما با موفقیت ارسال شد. در اسرع وقت با شما تماس می‌گیریم.');
} else {
    $submissionRepo->updateStatus($submissionId, 'failed');
    app_log("Failed to deliver application email for project ID {$id}", 'error');
    set_flash('error', 'ارسال درخواست با مشکل روبه‌رو شد. لطفاً کمی بعد دوباره تلاش کنید.');
}

redirect($redirectTarget, 303);
