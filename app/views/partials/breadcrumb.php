<?php
/**
 * Modabber Breadcrumb Partial
 *
 * @var string $title
 */
?>
<nav class="breadcrumb-nav" aria-label="مسیر راهنما">
    <a href="<?= url('/') ?>">خانه</a>
    <span class="breadcrumb-chevron" aria-hidden="true"><?= render_icon('chevron-left', '', 16) ?></span>
    <span aria-current="page"><?= e($title) ?></span>
</nav>
