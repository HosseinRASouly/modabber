<?php
/**
 * Modabber Admin Panel - User Profile View
 *
 * @var array $user
 */
$fullName  = $user['full_name'] ?? '';
$initial   = mb_substr($fullName, 0, 1, 'UTF-8');
$username  = $user['username'] ?? '';
$email     = !empty($user['email']) ? $user['email'] : 'ثبت نشده';
$phone     = !empty($user['phone']) ? $user['phone'] : 'ثبت نشده';
$roleLabel = ($user['role'] ?? '') === 'admin' ? 'مدیر ارشد' : 'کاربر';

$createdAt = !empty($user['created_at']) ? jdate('Y/m/d - H:i', strtotime($user['created_at'])) : '—';
$lastLogin = !empty($user['last_login_at']) ? jdate('Y/m/d - H:i', strtotime($user['last_login_at'])) : 'اولین ورود';
?>
<div class="panel-card" style="max-width:860px;">
    <!-- User Profile Header Summary -->
    <div style="display:flex;align-items:center;gap:20px;margin-block-end:28px;padding-block-end:22px;border-bottom:1px solid var(--color-border);flex-wrap:wrap;">
        <div style="width:68px;height:68px;border-radius:50%;background:var(--color-primary);color:var(--color-on-primary);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:900;font-family:var(--font-family-title);" aria-hidden="true">
            <?= e($initial) ?>
        </div>
        <div>
            <h2 style="font-size:1.4rem;font-weight:900;color:var(--color-ink);margin-block-end:4px;font-family:var(--font-family-title);">
                <?= e($fullName) ?>
            </h2>
            <span class="status-badge status-active">
                <?= e($roleLabel) ?> سامانه
            </span>
        </div>
    </div>

    <!-- User Information Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:24px;">
        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">نام کاربری</div>
            <div style="font-size:1.05rem;font-weight:800;color:var(--color-ink);" dir="ltr" style="unicode-bidi:isolate;text-align:right;">
                <?= e($username) ?>
            </div>
        </div>

        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">پست الکترونیکی</div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--color-ink);" dir="ltr" style="unicode-bidi:isolate;text-align:right;">
                <?= e($email) ?>
            </div>
        </div>

        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">شماره تماس</div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--color-ink);" dir="ltr" style="unicode-bidi:isolate;text-align:right;">
                <?= e($phone) ?>
            </div>
        </div>

        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">سطح دسترسی</div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--color-ink);">
                <?= e($roleLabel) ?>
            </div>
        </div>

        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">تاریخ ایجاد حساب</div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--color-ink);">
                <?= e($createdAt) ?>
            </div>
        </div>

        <div>
            <div style="font-size:0.85rem;color:var(--color-ink-muted);font-weight:700;margin-block-end:4px;">آخرین ورود به سامانه</div>
            <div style="font-size:1.05rem;font-weight:700;color:var(--color-ink);">
                <?= e($lastLogin) ?>
            </div>
        </div>
    </div>
</div>
