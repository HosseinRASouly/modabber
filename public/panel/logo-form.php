<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - Logo Create/Edit Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();

$repo = new LogoRepository();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$logo = null;

if ($isEdit) {
    $logo = $repo->getById($id);
    if (!$logo) {
        set_flash('error', 'لوگوی مورد نظر یافت نشد.');
        redirect(url('/panel/logos'), 303);
    }
}

$errors = [];
$formData = $logo ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است. لطفاً دوباره تلاش کنید.');
        redirect($isEdit ? url('/panel/logos/edit?id=' . $id) : url('/panel/logos/create'), 303);
    }

    $name = normalize_fa_text((string)($_POST['name'] ?? ''));
    $websiteUrl = trim((string)($_POST['website_url'] ?? ''));
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $formData = [
        'id'          => $id,
        'name'        => $name,
        'website_url' => $websiteUrl,
        'sort_order'  => $sortOrder,
        'is_active'   => $isActive,
        'image'       => $logo['image'] ?? null,
    ];

    // Validation
    if (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 120) {
        $errors['name'] = 'نام شرکت باید بین ۲ تا ۱۲۰ نویسه باشد.';
    }

    if (!empty($websiteUrl) && !is_valid_url($websiteUrl)) {
        $errors['website_url'] = 'آدرس وب‌سایت باید با http:// یا https:// شروع شود.';
    }

    // Image Upload Handling
    $hasUpload = isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

    if (!$isEdit && !$hasUpload) {
        $errors['image'] = 'انتخاب فایل لوگو الزامی است.';
    }

    $newImageFilename = null;
    if ($hasUpload) {
        $uploadRes = handle_image_upload($_FILES['image'], 'logos', 2097152, 600, 200);
        if ($uploadRes['success']) {
            $newImageFilename = $uploadRes['filename'];
        } else {
            $errors['image'] = $uploadRes['error'];
        }
    }

    if (empty($errors)) {
        if ($newImageFilename !== null) {
            // Delete old file on edit
            if ($isEdit && !empty($logo['image'])) {
                delete_uploaded_file($logo['image'], 'logos');
            }
            $formData['image'] = $newImageFilename;
        }

        if ($isEdit) {
            $repo->update($id, $formData);
            set_flash('success', 'لوگوی شرکت با موفقیت ویرایش شد.');
        } else {
            $repo->create($formData);
            set_flash('success', 'لوگوی جدید با موفقیت اضافه شد.');
        }

        redirect(url('/panel/logos'), 303);
    }
}

$pageTitle = $isEdit ? 'ویرایش لوگوی شرکت' : 'افزودن لوگوی شرکت';

render_view('panel/logo_form', [
    'title'      => $pageTitle,
    'active_tab' => 'logos',
    'is_edit'    => $isEdit,
    'logo'       => $formData,
    'errors'     => $errors,
], 'panel');
