<?php
/**
 * Modabber Admin Panel Layout
 *
 * @var string $title
 * @var string $active_tab
 * @var string $content
 */
$pageTitle = (!empty($title) ? $title . ' | ' : '') . app_config('SHORT_NAME', 'مدبرنت');
$activeTab = $active_tab ?? 'profile';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle) ?></title>
    <meta name="theme-color" content="#1F8B59">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('/assets/img/M_icon.png') ?>">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= asset('/assets/css/panel.css') ?>">
</head>
<body class="panel-layout">
    <!-- Sidebar on Physical Right in RTL -->
    <?php render_partial('panel_sidebar', ['active_tab' => $activeTab]); ?>

    <!-- Main Content Area -->
    <main class="panel-main">
        <?php render_partial('panel_topbar', ['active_tab' => $activeTab]); ?>

        <!-- Dismissible Flash Alerts -->
        <?php render_partial('flash'); ?>

        <?= $content ?>
    </main>

    <!-- Delete Confirmation Modal Dialog -->
    <?php render_partial('delete_dialog'); ?>

    <!-- Panel JavaScript -->
    <script src="<?= asset('/assets/js/panel.js') ?>"></script>
</body>
</html>
