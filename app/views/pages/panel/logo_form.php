<?php
/**
 * Modabber Admin Panel - Logo Create/Edit Form View
 *
 * @var bool $is_edit
 * @var array $logo
 * @var array $errors
 */
$isEdit = $is_edit ?? false;
$l = $logo ?? [];
$err = $errors ?? [];

$pageTitle = $isEdit ? 'ویرایش لوگوی شرکت' : 'افزودن لوگوی شرکت';
$actionUrl = $isEdit ? url('/panel/logos/edit?id=' . (int)$l['id']) : url('/panel/logos/create');
?>
<div class="panel-page-header">
    <h1 class="panel-page-title"><?= e($pageTitle) ?></h1>
    <a href="<?= url('/panel/logos') ?>" class="btn btn-outline btn-sm">
        بازگشت به لیست
    </a>
</div>

<div class="panel-card" style="max-width:760px;">
    <form method="POST" action="<?= $actionUrl ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <div class="form-grid">
            <!-- Company Name -->
            <div class="form-group">
                <label for="name" class="form-label">
                    نام شرکت <span class="required-star">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="نام برند یا سازمان (جهت متن جایگزین تصویر)"
                    value="<?= e($l['name'] ?? '') ?>"
                    required
                    minlength="2"
                    maxlength="120"
                    <?= isset($err['name']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($err['name'])): ?>
                    <span class="form-error"><?= e($err['name']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Website URL -->
            <div class="form-group">
                <label for="website_url" class="form-label">آدرس وب‌سایت (اختیاری)</label>
                <input
                    type="url"
                    id="website_url"
                    name="website_url"
                    class="form-control"
                    placeholder="https://example.com"
                    value="<?= e($l['website_url'] ?? '') ?>"
                    dir="ltr"
                    style="text-align:right;"
                    <?= isset($err['website_url']) ? 'aria-invalid="true"' : '' ?>
                >
                <span class="form-hint">آدرس باید با http:// یا https:// شروع شود.</span>
                <?php if (isset($err['website_url'])): ?>
                    <span class="form-error"><?= e($err['website_url']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Logo Image Upload -->
            <div class="form-group">
                <label for="image-upload-input" class="form-label">
                    فایل لوگو <?= $isEdit ? '(اختیاری در ویرایش)' : '<span class="required-star">*</span>' ?>
                </label>
                <input
                    type="file"
                    id="image-upload-input"
                    name="image"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                    <?= !$isEdit ? 'required' : '' ?>
                >
                <span class="form-hint">فرمت‌های مجاز: JPG، PNG، WebP (حداکثر ۲ مگابایت، ترجیحاً پس‌زمینه شفاف)</span>

                <?php
                $existingImage = !empty($l['image']) && file_exists(PUBLIC_PATH . '/uploads/logos/' . $l['image'])
                    ? url('/uploads/logos/' . $l['image'])
                    : null;
                ?>
                <div id="image-preview-container" style="margin-block-start:12px;display:<?= $existingImage ? 'block' : 'none' ?>;">
                    <div style="width:120px;height:70px;border-radius:16px;background:var(--color-primary-soft);display:flex;align-items:center;justify-content:center;padding:8px;">
                        <img id="image-preview-elem" src="<?= $existingImage ? e($existingImage) : '' ?>" alt="پیش‌نمایش لوگو" style="max-height:50px;max-width:100px;object-fit:contain;">
                    </div>
                </div>

                <?php if (isset($err['image'])): ?>
                    <span class="form-error"><?= e($err['image']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Sort Order & Active Status -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:center;">
                <div class="form-group">
                    <label for="sort_order" class="form-label">ترتیب نمایش</label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        class="form-control"
                        value="<?= (int)($l['sort_order'] ?? 0) ?>"
                    >
                </div>

                <div class="form-group" style="padding-block-start:24px;">
                    <label style="display:inline-flex;align-items:center;gap:10px;cursor:pointer;font-weight:700;">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            style="width:20px;height:20px;accent-color:var(--color-primary);"
                            <?= (!isset($l['is_active']) || (int)$l['is_active'] === 1) ? 'checked' : '' ?>
                        >
                        <span>فعال (نمایش در چرخاننده لوگوها)</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex;gap:12px;margin-block-start:20px;">
                <button type="submit" class="btn btn-primary">
                    ذخیره
                </button>
                <a href="<?= url('/panel/logos') ?>" class="btn btn-outline">
                    انصراف
                </a>
            </div>
        </div>
    </form>
</div>
