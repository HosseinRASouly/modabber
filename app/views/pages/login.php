<?php
/**
 * Modabber Login Page View
 *
 * @var string|null $error
 */
$errorMsg = get_flash('error');
$successMsg = get_flash('success');
?>
<div class="login-page-body">
    <!-- Decorative Spheres -->
    <?php render_partial('spheres', ['with_ring' => false]); ?>

    <div class="login-card">
        <!-- Site Logo Provided -->
        <div class="login-brand-header">
            <a href="<?= url('/') ?>" class="login-logo-link" aria-label="صفحه نخست مدبرنت">
                <img src="<?= asset('/assets/img/M_logo.png') ?>" alt="لوگوی مدبرنت" class="login-logo-img" width="54" height="54">
                <span class="login-brand-name">مدبرنت</span>
            </a>
        </div>

        <h1 class="login-title">ورود به پنل مدیریت</h1>

        <!-- Error / Success Alert Area -->
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger" role="alert">
                <?= render_icon('x', '', 18) ?>
                <span><?= e($errorMsg) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success" role="status">
                <?= render_icon('check', '', 18) ?>
                <span><?= e($successMsg) ?></span>
            </div>
        <?php endif; ?>

        <form id="login-form" method="POST" action="<?= url('/login') ?>" novalidate>
            <?= csrf_field() ?>

            <div class="form-group" style="text-align:start;margin-block-end:20px;">
                <label for="identifier" class="form-label">نام کاربری</label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    class="form-control"
                    placeholder="نام کاربری خود را وارد کنید"
                    required
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="form-group" style="text-align:start;margin-block-end:28px;">
                <label for="password-input" class="form-label">رمز عبور</label>
                <div class="password-input-group">
                    <input
                        type="password"
                        id="password-input"
                        name="password"
                        class="form-control"
                        placeholder="رمز عبور خود را وارد کنید"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle-btn" aria-label="نمایش رمز عبور">
                        <?= render_icon('eye', '', 20) ?>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;">
                ورود
            </button>
        </form>

        <!-- Generous spacing between submit button and back link -->
        <div class="login-links-wrapper">
            <a href="<?= url('/') ?>" class="btn-back-home">
                <?= render_icon('arrow-right', '', 16) ?>
                <span>بازگشت به صفحه اول</span>
            </a>
        </div>
    </div>
</div>
