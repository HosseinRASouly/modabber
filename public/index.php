<?php
declare(strict_types=1);

/**
 * Modabber - Home Page Controller
 */

require_once __DIR__ . '/../app/core/bootstrap.php';

// Home page does NOT need a session (sessions start lazily)
$logoRepo = new LogoRepository();
$logos = $logoRepo->getActive();

$projectRepo = new ProjectRepository();
$perPage = 12;
$totalProjects = $projectRepo->countPublished();
$totalPages = max(1, (int)ceil($totalProjects / $perPage));

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1 || ($page > $totalPages && $totalProjects > 0)) {
    redirect(url('/?page=1#projects'));
}

$projects = $projectRepo->getPublished($page, $perPage);

$title = app_config('SITE_NAME', 'مدبرنت | اتصال هوشمند صنعت به متخصصان');
$metaDesc = setting('hero_description', 'ما عارضه‌ها را دقیق می‌شناسیم، تیم‌ها را با دقت ارزیابی می‌کنیم و مطمئن‌ترین متخصصان را برای اجرای پروژه‌ها معرفی می‌کنیم.');

render_view('home', [
    'title'        => $title,
    'meta_desc'    => $metaDesc,
    'logos'        => $logos,
    'projects'     => $projects,
    'current_page' => $page,
    'total_pages'  => $totalPages,
], 'public');
