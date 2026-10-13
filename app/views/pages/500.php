<?php
/**
 * Modabber 500 Internal Server Error Page View
 */
?>
<div class="container" style="text-align:center;padding-block:120px 80px;position:relative;">
    <?php render_partial('spheres', ['with_ring' => false]); ?>

    <div style="position:relative;z-index:2;max-width:540px;margin-inline:auto;">
        <div style="font-size:clamp(4.5rem,10vw,7rem);font-weight:800;color:var(--color-ink-muted);line-height:1;">
            ۵۰۰
        </div>
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--color-ink);margin-block:16px 12px;">
            خطای داخلی سرور
        </h1>
        <p style="color:var(--color-ink-muted);font-size:1.05rem;line-height:1.9;margin-block-end:32px;">
            در پردازش درخواست شما مشکلی در سرور رخ داد. تیم فنی در حال بررسی موضوع است.
        </p>
        <a href="<?= url('/') ?>" class="btn btn-primary">
            بازگشت به صفحه اصلی
        </a>
    </div>
</div>
