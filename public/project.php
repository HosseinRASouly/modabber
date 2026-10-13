<?php
declare(strict_types=1);

/**
 * Modabber - Project Detail Controller
 */

require_once __DIR__ . '/../app/core/bootstrap.php';

// Lazy session start (required for CSRF and application form nonce)
session_start_lazy();

// Project page embeds CSRF token: must not be cached by proxies
header('Cache-Control: private, no-cache, no-store, must-revalidate');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    render_view('404', ['title' => 'صفحه پیدا نشد | مدبر'], 'public');
    exit;
}

$repo = new ProjectRepository();
$project = $repo->getPublishedById($id);

if (!$project) {
    http_response_code(404);
    render_view('404', ['title' => 'پروژه پیدا نشد | مدبرنت'], 'public');
    exit;
}

$siteName = app_config('SHORT_NAME', 'مدبرنت');
$title = $project['name'] . ' | ' . $siteName;
$metaDesc = $project['short_description'];

$ogImage = !empty($project['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $project['image'])
    ? url('/uploads/projects/' . $project['image'])
    : url('/assets/img/project-placeholder.svg');

render_view('project', [
    'title'     => $title,
    'meta_desc' => $metaDesc,
    'og_image'  => $ogImage,
    'project'   => $project,
    'extra_js'  => '/assets/js/apply.js',
], 'public');
