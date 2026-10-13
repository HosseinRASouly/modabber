<?php
/**
 * Modabber Application Form Partial
 *
 * @var array $project
 */
$projectId = (int)$project['id'];

// Retrieve or generate one-time nonce for this project form
session_start_lazy();
if (!isset($_SESSION['apply_nonces'])) {
    $_SESSION['apply_nonces'] = [];
}

$now = time();
if (
    empty($_SESSION['apply_nonces'][$projectId]) ||
    !is_array($_SESSION['apply_nonces'][$projectId]) ||
    ($now - ($_SESSION['apply_nonces'][$projectId]['issued_at'] ?? 0) > 7200)
) {
    $_SESSION['apply_nonces'][$projectId] = [
        'nonce'     => bin2hex(random_bytes(16)),
        'issued_at' => $now,
    ];
}
$formNonce = $_SESSION['apply_nonces'][$projectId]['nonce'];

$applyTitle    = setting('apply_title', 'ارسال درخواست همکاری');
$applySubtitle = setting('apply_subtitle', 'برای همکاری در این پروژه، مشخصات و رزومه خود را برای ما ارسال کنید.');

$oldInput   = get_flash('apply_old', []);
$formErrors = get_flash('apply_errors', []);
$hasErrors  = !empty($formErrors);
?>
<section id="apply" class="apply-section" aria-labelledby="apply-title">
    <div class="container">
        <div class="section-intro">
            <h2 id="apply-title" class="section-title"><?= e($applyTitle) ?></h2>
            <?php if (!empty($applySubtitle)): ?>
                <p class="section-desc"><?= e($applySubtitle) ?></p>
            <?php endif; ?>
        </div>

        <div class="apply-card">
            <!-- Status & Alert Area -->
            <div id="apply-status" tabindex="-1" role="alert">
                <?php render_partial('flash'); ?>
                <?php if ($hasErrors && empty($formErrors['resume'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <span>به دلیل محدودیت‌های امنیتی مرورگر، لطفاً فایل رزومه را دوباره انتخاب کنید.</span>
                    </div>
                <?php endif; ?>
            </div>

            <form id="apply-form" method="POST" action="<?= url('/project/' . $projectId . '/apply') ?>" enctype="multipart/form-data" novalidate>
                <!-- CSRF Token -->
                <?= csrf_field() ?>

                <!-- Form Nonce -->
                <input type="hidden" name="form_nonce" value="<?= e($formNonce) ?>">

                <!-- Honeypot Field -->
                <div class="hp-field" aria-hidden="true">
                    <label>
                        لطفاً این فیلد را خالی بگذارید
                        <input type="text" name="hp_url" tabindex="-1" autocomplete="off">
                    </label>
                </div>

                <div class="form-grid">
                    <!-- Row 1: Two Columns from 640px (Full Name & Phone) -->
                    <div class="form-grid form-grid-2">
                        <!-- Field 1: Full Name -->
                        <div class="form-group">
                            <label for="full_name" class="form-label">
                                نام و نام خانوادگی
                                <span class="required-star" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                class="form-control"
                                placeholder="نام خود یا تیم را وارد کنید"
                                value="<?= e($oldInput['full_name'] ?? '') ?>"
                                required
                                maxlength="100"
                                autocomplete="name"
                                <?= isset($formErrors['full_name']) ? 'aria-invalid="true" aria-describedby="full_name-error"' : '' ?>
                            >
                            <span id="full_name-error" class="form-error" role="alert">
                                <?= e($formErrors['full_name'] ?? '') ?>
                            </span>
                        </div>

                        <!-- Field 2: Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                شماره تماس
                                <span class="required-star" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-control"
                                inputmode="tel"
                                placeholder="شماره تماس در دسترس را وارد کنید"
                                value="<?= e($oldInput['phone'] ?? '') ?>"
                                required
                                maxlength="20"
                                autocomplete="tel"
                                <?= isset($formErrors['phone']) ? 'aria-invalid="true" aria-describedby="phone-error"' : '' ?>
                            >
                            <span id="phone-error" class="form-error" role="alert">
                                <?= e($formErrors['phone'] ?? '') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Field 3: Resume Upload (Word & PDF) -->
                    <div class="form-group">
                        <label for="resume-file" class="form-label">
                            فایل رزومه
                            <span class="required-star" aria-hidden="true">*</span>
                        </label>
                        <!-- Native file input -->
                        <input
                            type="file"
                            id="resume-file"
                            name="resume"
                            class="visually-hidden"
                            accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            required
                            <?= isset($formErrors['resume']) ? 'aria-invalid="true" aria-describedby="resume-error"' : '' ?>
                        >
                        <!-- Modern Persian Drag & Drop Upload Zone -->
                        <label for="resume-file" class="file-dropzone" id="file-dropzone">
                            <div class="dropzone-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </div>
                            <span class="dropzone-title">برای بارگذاری فایل کلیک کنید یا فایل را به اینجا بکشید</span>
                            <span class="dropzone-subtitle">فرمت‌های مجاز: PDF، DOC، DOCX (فایل‌های متنی Word و PDF) — حداکثر حجم: ۵ مگابایت</span>
                            <div id="file-selected-badge" class="file-selected-badge" style="display:none;">
                                <span class="badge-icon">✓</span>
                                <span id="file-name-display" class="file-name-display">فایلی انتخاب نشده</span>
                            </div>
                        </label>
                        <span id="resume-error" class="form-error" role="alert">
                            <?= e($formErrors['resume'] ?? '') ?>
                        </span>
                    </div>

                    <!-- Field 4: Message / Description -->
                    <div class="form-group">
                        <label for="message" class="form-label">
                            توضیحات
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            class="form-control"
                            rows="5"
                            maxlength="2000"
                            placeholder="توضیحات تکمیلی خود را بنویسید (اختیاری)"
                            <?= isset($formErrors['message']) ? 'aria-invalid="true" aria-describedby="message-error"' : '' ?>
                        ><?= e($oldInput['message'] ?? '') ?></textarea>
                        <div class="char-counter" id="message-counter">۰ / ۲۰۰۰</div>
                        <span id="message-error" class="form-error" role="alert">
                            <?= e($formErrors['message'] ?? '') ?>
                        </span>
                    </div>

                    <!-- Bottom Action Row: Submit Button and Disclaimer Centered -->
                    <div class="apply-submit-wrapper">
                        <button type="submit" id="apply-submit-btn" class="btn btn-primary btn-submit-apply">
                            ارسال درخواست
                        </button>
                        <p class="form-hint apply-disclaimer-text">
                            اطلاعات شما فقط برای بررسی درخواست همکاری استفاده می‌شود.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
