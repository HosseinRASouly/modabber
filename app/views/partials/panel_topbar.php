<?php
/**
 * Modabber Admin Panel Topbar Partial
 *
 * @var string $active_tab
 */
$user = current_user();
$fullName = $user['full_name'] ?? 'مدیر سیستم';
$initial = mb_substr($fullName, 0, 1, 'UTF-8');
$currentTab = $active_tab ?? 'profile';

$titles = [
    'profile'  => 'مشخصات کاربری',
    'projects' => 'مدیریت پروژه‌ها',
    'logos'    => 'لوگوی شرکت‌ها',
];
$tabTitle = $titles[$currentTab] ?? 'پنل مدیریت';
?>
<div class="panel-topbar">
    <div class="topbar-right">
        <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="باز کردن منو" aria-expanded="false" aria-controls="panel-sidebar">
            <?= render_icon('menu', '', 24) ?>
        </button>
        <div class="topbar-page-badge">
            <span class="topbar-badge-dot" aria-hidden="true"></span>
            <span class="topbar-page-name"><?= e($tabTitle) ?></span>
        </div>
    </div>

    <div class="topbar-user">
        <div class="user-info-text">
            <span class="user-name"><?= e($fullName) ?></span>
            <span class="user-role-badge">مدیر سامانه</span>
        </div>
        <div class="user-avatar-circle" aria-hidden="true">
            <?= e($initial) ?>
        </div>
        <!-- Desktop only link to view website; removed on mobile to prevent overlapping -->
        <a href="<?= url('/') ?>" class="btn-outline topbar-site-link" target="_blank" rel="noopener" title="مشاهده وب‌سایت">
            <?= render_icon('arrow-up-right', '', 14) ?>
            <span>مشاهده سایت</span>
        </a>
    </div>
</div>
