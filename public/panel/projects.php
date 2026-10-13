<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - Projects Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();

$repo = new ProjectRepository();

// Handle Delete Action via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'delete') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrfToken)) {
        set_flash('error', 'نشست شما منقضی شده است. عملیات حذف لغو شد.');
        redirect(url('/panel/projects'), 303);
    }

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $project = $repo->getById($id);
        if ($project) {
            if (!empty($project['image'])) {
                delete_uploaded_file($project['image'], 'projects');
            }
            $repo->delete($id);
            set_flash('success', 'پروژه مورد نظر با موفقیت حذف شد.');
        } else {
            set_flash('error', 'پروژه یافت نشد.');
        }
    }

    redirect(url('/panel/projects'), 303);
}

// GET: Display Paginated Projects List
$perPage = 15;
$totalProjects = $repo->countAll();
$totalPages = max(1, (int)ceil($totalProjects / $perPage));

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalProjects > 0) $page = $totalPages;

$projects = $repo->getAll($page, $perPage);

render_view('panel/projects', [
    'title'        => 'مدیریت پروژه‌ها',
    'active_tab'   => 'projects',
    'projects'     => $projects,
    'current_page' => $page,
    'total_pages'  => $totalPages,
], 'panel');
