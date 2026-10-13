<?php
/**
 * Modabber Admin Panel - Project Create/Edit Form View
 *
 * @var bool $is_edit
 * @var array $project
 * @var array $errors
 */
$isEdit = $is_edit ?? false;
$p = $project ?? [];
$err = $errors ?? [];

$pageTitle = $isEdit ? 'ویرایش پروژه' : 'افزودن پروژه';
$actionUrl = $isEdit ? url('/panel/projects/edit?id=' . (int)$p['id']) : url('/panel/projects/create');
?>
<div class="panel-page-header">
    <h1 class="panel-page-title"><?= e($pageTitle) ?></h1>
    <a href="<?= url('/panel/projects') ?>" class="btn btn-outline btn-sm">
        بازگشت به لیست
    </a>
</div>

<div class="panel-card" style="max-width:860px;">
    <form method="POST" action="<?= $actionUrl ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <div class="form-grid">
            <!-- Project Name -->
            <div class="form-group">
                <label for="name" class="form-label">
                    نام پروژه <span class="required-star">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="عنوان کامل پروژه را وارد کنید"
                    value="<?= e($p['name'] ?? '') ?>"
                    required
                    minlength="3"
                    maxlength="120"
                    <?= isset($err['name']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($err['name'])): ?>
                    <span class="form-error"><?= e($err['name']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Short Description with Live Counter -->
            <div class="form-group">
                <label for="short-desc-input" class="form-label">
                    توضیحات کوتاه <span class="required-star">*</span>
                </label>
                <input
                    type="text"
                    id="short-desc-input"
                    name="short_description"
                    class="form-control"
                    placeholder="خلاصه‌ای از پروژه جهت نمایش در کارت (۱۰ الی ۲۰۰ نویسه)"
                    value="<?= e($p['short_description'] ?? '') ?>"
                    required
                    minlength="10"
                    maxlength="200"
                    <?= isset($err['short_description']) ? 'aria-invalid="true"' : '' ?>
                >
                <div class="char-counter" id="short-desc-counter">۰ / ۲۰۰</div>
                <?php if (isset($err['short_description'])): ?>
                    <span class="form-error"><?= e($err['short_description']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Full Description (Plain Text Only) -->
            <div class="form-group">
                <label for="full_description" class="form-label">
                    توضیحات کامل <span class="required-star">*</span>
                </label>
                <textarea
                    id="full_description"
                    name="full_description"
                    class="form-control"
                    rows="8"
                    maxlength="20000"
                    placeholder="توضیحات جامع پروژه (متن ساده، پاراگراف‌ها با سطر خالی جدا شوند)"
                    required
                    <?= isset($err['full_description']) ? 'aria-invalid="true"' : '' ?>
                ><?= e($p['full_description'] ?? '') ?></textarea>
                <?php if (isset($err['full_description'])): ?>
                    <span class="form-error"><?= e($err['full_description']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Budget (Toman) with Live Separator -->
            <div class="form-group">
                <label for="budget-input" class="form-label">
                    بودجه (تومان) <span class="required-star">*</span>
                </label>
                <input
                    type="text"
                    id="budget-input"
                    name="budget"
                    class="form-control"
                    placeholder="مبلغ به تومان (مثال: ۱۲٬۰۰۰٬۰۰۰)"
                    value="<?= isset($p['budget']) ? e(format_money($p['budget'])) : '' ?>"
                    required
                    <?= isset($err['budget']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($err['budget'])): ?>
                    <span class="form-error"><?= e($err['budget']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Project Image Upload & Instant Preview -->
            <div class="form-group">
                <label for="image-upload-input" class="form-label">
                    تصویر پروژه <?= $isEdit ? '(اختیاری، برای جایگزینی تصویر فعلی)' : '(اختیاری)' ?>
                </label>
                <input
                    type="file"
                    id="image-upload-input"
                    name="image"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                >
                <span class="form-hint">فرمت‌های مجاز: JPG، PNG، WebP (حداکثر ۴ مگابایت)</span>

                <!-- Current / Preview Image Display -->
                <?php
                $existingImage = !empty($p['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $p['image'])
                    ? url('/uploads/projects/' . $p['image'])
                    : null;
                ?>
                <div id="image-preview-container" style="margin-block-start:12px;display:<?= $existingImage ? 'block' : 'none' ?>;">
                    <img id="image-preview-elem" src="<?= $existingImage ? e($existingImage) : '' ?>" alt="پیش‌نمایش تصویر" style="max-width:240px;border-radius:16px;border:1px solid var(--color-border);box-shadow:var(--shadow-card);">
                </div>

                <?php if (isset($err['image'])): ?>
                    <span class="form-error"><?= e($err['image']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Sort Order & Publish & Expired Status -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:20px;align-items:center;">
                <div class="form-group">
                    <label for="sort_order" class="form-label">ترتیب نمایش</label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        class="form-control"
                        value="<?= (int)($p['sort_order'] ?? 0) ?>"
                    >
                    <span class="form-hint">اعداد کمتر در ابتدای لیست نمایش داده می‌شوند.</span>
                </div>

                <div class="form-group" style="padding-block-start:24px;">
                    <label style="display:inline-flex;align-items:center;gap:10px;cursor:pointer;font-weight:700;">
                        <input
                            type="checkbox"
                            name="is_published"
                            value="1"
                            style="width:20px;height:20px;accent-color:var(--color-primary);"
                            <?= (!isset($p['is_published']) || (int)$p['is_published'] === 1) ? 'checked' : '' ?>
                        >
                        <span>منتشر شود (نمایش در سایت)</span>
                    </label>
                </div>

                <div class="form-group" style="padding-block-start:24px;">
                    <label style="display:inline-flex;align-items:center;gap:10px;cursor:pointer;font-weight:700;color:var(--color-danger);">
                        <input
                            type="checkbox"
                            name="is_expired"
                            value="1"
                            style="width:20px;height:20px;accent-color:var(--color-danger);"
                            <?= (!empty($p['is_expired']) && (int)$p['is_expired'] === 1) ? 'checked' : '' ?>
                        >
                        <span>قرارداد منقضی شده</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div style="display:flex;gap:12px;margin-block-start:20px;">
                <button type="submit" class="btn btn-primary">
                    ذخیره
                </button>
                <a href="<?= url('/panel/projects') ?>" class="btn btn-outline">
                    انصراف
                </a>
            </div>
        </div>
    </form>
</div>
