<?php
/**
 * Modabbernet Hero Section Partial
 *
 * @var array $logos
 */
$heroTitle = setting('hero_title', 'مدبرنت؛ اتصال هوشمند صنعت به متخصصان');
$heroDesc  = setting('hero_description', 'ما عارضه‌ها را دقیق می‌شناسیم، تیم‌ها را با دقت ارزیابی می‌کنیم و مطمئن‌ترین متخصصان را برای اجرای پروژه‌ها معرفی می‌کنیم.');
?>
<section class="hero-section">
    <!-- Decorative Spheres and Thick Ring -->
    <?php render_partial('spheres', ['with_ring' => true]); ?>

    <div class="container hero-content">
        <h1 class="hero-title"><?= e($heroTitle) ?></h1>
        <p class="hero-desc"><?= e($heroDesc) ?></p>
    </div>

    <!-- Straddling Logo Marquee -->
    <?php render_partial('marquee', ['logos' => $logos ?? []]); ?>
</section>
