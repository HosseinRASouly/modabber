<?php
/**
 * Modabbernet Brand Partial
 */
$shortName = app_config('SHORT_NAME', 'مدبرنت');
?>
<a href="<?= url('/') ?>" class="brand-wrapper" aria-label="<?= e($shortName) ?>">
    <img src="<?= asset('/assets/img/M_logo.png') ?>" alt="<?= e($shortName) ?>" class="brand-logo" width="72" height="72">
    <span class="brand-text"><?= e($shortName) ?></span>
</a>
