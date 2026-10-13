<?php
/**
 * Modabber Public Layout
 *
 * @var string $title
 * @var string $meta_desc
 * @var string $og_image
 * @var string $content
 * @var string|null $extra_js
 */
$pageTitle = !empty($title) ? $title : app_config('SITE_NAME', 'مدبرنت | اتصال هوشمند صنعت به متخصصان');
$metaDescription = !empty($meta_desc) ? $meta_desc : 'مدبرنت — اتصال هوشمند صنعت به متخصصان، عارضه‌یابی تخصصی و نظارت بر پروژه‌ها';
$ogImg = !empty($og_image) ? $og_image : url('/assets/img/favicon.svg');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#1F8B59">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url($_SERVER['REQUEST_URI'] ?? '/')) ?>">
    <meta property="og:image" content="<?= e($ogImg) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('/assets/img/M_icon.png') ?>">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
</head>
<body>
    <?php render_partial('header'); ?>

    <main id="main-content">
        <?= $content ?>
    </main>

    <?php render_partial('footer'); ?>

    <!-- Main JavaScript -->
    <script src="<?= asset('/assets/js/app.js') ?>"></script>
    <?php if (!empty($extra_js)): ?>
        <script src="<?= asset($extra_js) ?>" defer></script>
    <?php endif; ?>
</body>
</html>
