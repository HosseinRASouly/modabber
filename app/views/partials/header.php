<?php
/**
 * Modabber Public Header Partial
 */
?>
<header class="site-header">
    <div class="container header-inner">
        <!-- Physical Right: Brand Wordmark (Name only) -->
        <div class="header-brand">
            <?php render_partial('brand', ['show_mark' => false]); ?>
        </div>

        <!-- Physical Left: Single Pill Button "حساب کاربری" -->
        <div class="header-action">
            <?php $targetUrl = is_authenticated() ? url('/panel/profile') : url('/login'); ?>
            <a href="<?= $targetUrl ?>" class="btn btn-primary">
                حساب کاربری
            </a>
        </div>
    </div>
</header>
