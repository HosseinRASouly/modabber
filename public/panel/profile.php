<?php
declare(strict_types=1);

/**
 * Modabber Admin Panel - User Profile Controller
 */

require_once __DIR__ . '/../../app/core/bootstrap.php';

require_admin();

$user = current_user();

render_view('panel/profile', [
    'title'      => 'مشخصات کاربری',
    'active_tab' => 'profile',
    'user'       => $user,
], 'panel');
