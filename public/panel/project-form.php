<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - Project Create/Edit Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();

$repo = new ProjectRepository();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$project = null;

if ($isEdit) {
    $project = $repo->getById($id);
    if (!$project) {
        set_flash('error', 'پروژه مورد نظر یافت نشد.');
        redirect(url('/panel/projects'), 303);
    }
}

$errors = [];
$formData = $project ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است. لطفاً دوباره تلاش کنید.');
        redirect($isEdit ? url('/panel/projects/edit?id=' . $id) : url('/panel/projects/create'), 303);
    }

    $name = normalize_fa_text((string)($_POST['name'] ?? ''));
    $shortDesc = normalize_fa_text((string)($_POST['short_description'] ?? ''));
    $fullDesc = trim((string)($_POST['full_description'] ?? ''));
    $rawBudget = (string)($_POST['budget'] ?? '0');
    $budget = normalize_number($rawBudget);
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $isExpired = isset($_POST['is_expired']) ? 1 : 0;

    $formData = [
        'id'                => $id,
        'name'              => $name,
        'short_description' => $shortDesc,
        'full_description'  => $fullDesc,
        'budget'            => $budget,
        'sort_order'        => $sortOrder,
        'is_published'      => $isPublished,
        'is_expired'        => $isExpired,
        'image'             => $project['image'] ?? null,
    ];

    // Validation
    if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 120) {
        $errors['name'] = 'نام پروژه باید بین ۳ تا ۱۲۰ نویسه باشد.';
    }

    if (mb_strlen($shortDesc, 'UTF-8') < 10 || mb_strlen($shortDesc, 'UTF-8') > 200) {
        $errors['short_description'] = 'توضیحات کوتاه باید بین ۱۰ تا ۲۰۰ نویسه باشد.';
    }

    if (mb_strlen($fullDesc, 'UTF-8') < 10 || mb_strlen($fullDesc, 'UTF-8') > 20000) {
        $errors['full_description'] = 'توضیحات کامل باید بین ۱۰ تا ۲۰۰۰۰ نویسه باشد.';
    }

    if ($budget < 0 || $budget > 999999999999) {
        $errors['budget'] = 'مبلغ بودجه نامعتبر است.';
    }

    // Image Upload Handling
    $newImageFilename = null;
    if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $uploadRes = handle_image_upload($_FILES['image'], 'projects', 4194304, 1600, 1600);
        if ($uploadRes['success']) {
            $newImageFilename = $uploadRes['filename'];
        } else {
            $errors['image'] = $uploadRes['error'];
        }
    }

    if (empty($errors)) {
        if ($newImageFilename !== null) {
            // Delete old file on edit
            if ($isEdit && !empty($project['image'])) {
                delete_uploaded_file($project['image'], 'projects');
            }
            $formData['image'] = $newImageFilename;
        }

        if ($isEdit) {
            $repo->update($id, $formData);
            set_flash('success', 'پروژه با موفقیت ویرایش شد.');
        } else {
            $repo->create($formData);
            set_flash('success', 'پروژه جدید با موفقیت ثبت شد.');
        }

        redirect(url('/panel/projects'), 303);
    }
}

$pageTitle = $isEdit ? 'ویرایش پروژه' : 'افزودن پروژه جدید';

render_view('panel/project_form', [
    'title'      => $pageTitle,
    'active_tab' => 'projects',
    'is_edit'    => $isEdit,
    'project'    => $formData,
    'errors'     => $errors,
], 'panel');
