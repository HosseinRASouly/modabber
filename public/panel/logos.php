<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - Company Logos Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();

$repo = new LogoRepository();

// Handle Delete Action via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است. عملیات حذف لغو شد.');
        redirect(url('/panel/logos'), 303);
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $logo = $repo->getById($id);
        if ($logo) {
            if (!empty($logo['image'])) {
                delete_uploaded_file($logo['image'], 'logos');
            }
            $repo->delete($id);
            set_flash('success', 'لوگوی شرکت با موفقیت حذف شد.');
        } else {
            set_flash('error', 'لوگو یافت نشد.');
        }
    }

    redirect(url('/panel/logos'), 303);
}

// GET: Display Logos List
$logos = $repo->getAll();

render_view('panel/logos', [
    'title'      => 'مدیریت لوگوی شرکت‌ها',
    'active_tab' => 'logos',
    'logos'      => $logos,
], 'panel');
