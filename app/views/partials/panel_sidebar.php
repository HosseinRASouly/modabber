<?php
/**
 * Modabber Admin Panel Sidebar Partial
 *
 * @var string $active_tab
 */
$activeTab = $active_tab ?? 'profile';
?>
<aside class="panel-sidebar" aria-label="منوی اصلی مدیریت">
    <div class="sidebar-brand">
        <?php render_partial('brand', ['show_mark' => true]); ?>
    </div>

    <nav class="sidebar-nav">
        <!-- Tab 1: Profile (مشخصات کاربری) -->
        <a href="<?= url('/panel/profile') ?>" class="nav-item <?= $activeTab === 'profile' ? 'active' : '' ?>">
            <span class="nav-icon"><?= render_icon('user', '', 20) ?></span>
            <span>مشخصات کاربری</span>
        </a>

        <!-- Tab 2: Projects (پروژه‌ها) -->
        <a href="<?= url('/panel/projects') ?>" class="nav-item <?= $activeTab === 'projects' ? 'active' : '' ?>">
            <span class="nav-icon"><?= render_icon('folder', '', 20) ?></span>
            <span>پروژه‌ها</span>
        </a>

        <!-- Tab 3: Company Logos (لوگوی شرکت‌ها) -->
        <a href="<?= url('/panel/logos') ?>" class="nav-item <?= $activeTab === 'logos' ? 'active' : '' ?>">
            <span class="nav-icon"><?= render_icon('image', '', 20) ?></span>
            <span>لوگوی شرکت‌ها</span>
        </a>

        <!-- Tab 4: Logout (خروج از حساب کاربری) - Always Last, POST Form with CSRF -->
        <form method="POST" action="<?= url('/panel/logout') ?>" class="nav-item-logout-wrapper" style="margin-block-start:auto;">
            <?= csrf_field() ?>
            <button type="submit" class="nav-item nav-item-logout">
                <span class="nav-icon"><?= render_icon('log-out', '', 20) ?></span>
                <span>خروج از حساب کاربری</span>
            </button>
        </form>
    </nav>
</aside>
<div class="sidebar-overlay" aria-hidden="true"></div>
