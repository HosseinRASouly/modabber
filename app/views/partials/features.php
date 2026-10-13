<?php
/**
 * Modabbernet Features & Why Us Partial
 */
$title = setting('why_title', 'چرا مدبرنت؟');
$desc  = setting('why_description', 'ما فاصله میان چالش‌های صنعتی و راهکارهای تخصصی را با اعتبار، مهندسی و نظارت دقیق پر می‌کنیم.');

$f1Title = 'عارضه‌یابی تخصصی';
$f1Text  = 'استخراج و تعریف استاندارد نیازهای واقعی صنایع با همراهی نخبگان دانشگاهی و متخصصان مهندسی صنایع';

$f2Title = 'امنیت و شفافیت قراردادها';
$f2Text  = 'صیانت از بودجه کارفرما، ضابطه‌مند کردن توافق‌ها و نظارت بر حسن انجام کار تا تحویل نهایی پروژه';

$f3Title = 'ارزیابی هوشمند متخصصان';
$f3Text  = 'سنجش دقیق اهلیت و توان فنی تیم‌های اجرا پیش از معرفی به صنعت برای دستیابی به بالاترین کیفیت اجرای پروژه';

// Dynamic stats based on admin panel entries
$projectRepo = new ProjectRepository();
$projectsTotalCount = $projectRepo->countAll();

$logoRepo = new LogoRepository();
$logosTotalCount = $logoRepo->countAll();

$s1Val   = fa_digits($projectsTotalCount);
$s1Label = 'پروژه‌های انجام‌شده';

$s2Val   = fa_digits($logosTotalCount);
$s2Label = 'مشتری‌های راضی';

$s3Val   = '۱ سال';
$s3Label = 'تجربه کاری';
?>
<section class="why-section features-section" id="about">
    <div class="container">
        <!-- Centered Intro -->
        <div class="section-intro">
            <h2 class="section-title"><?= e($title) ?></h2>
            <p class="section-desc"><?= e($desc) ?></p>
        </div>

        <!-- Large Tinted Panel with 3 Features -->
        <div class="why-panel">
            <div class="features-grid">
                <!-- Feature 1 (Physical Right in RTL): عارضه‌یابی تخصصی -->
                <div class="feature-card feature-item text-center">
                    <div class="feature-icon-wrapper feature-icon-bubble">
                        <?= render_icon('search', '', 32) ?>
                    </div>
                    <h3 class="feature-title"><?= e($f1Title) ?></h3>
                    <p class="feature-desc"><?= e($f1Text) ?></p>
                </div>

                <!-- Feature 2 (Center): امنیت و شفافیت قراردادها -->
                <div class="feature-card feature-item text-center">
                    <div class="feature-icon-wrapper feature-icon-bubble">
                        <?= render_icon('shield', '', 32) ?>
                    </div>
                    <h3 class="feature-title"><?= e($f2Title) ?></h3>
                    <p class="feature-desc"><?= e($f2Text) ?></p>
                </div>

                <!-- Feature 3 (Physical Left in RTL): ارزیابی هوشمند متخصصان -->
                <div class="feature-card feature-item text-center">
                    <div class="feature-icon-wrapper feature-icon-bubble">
                        <?= render_icon('cpu', '', 32) ?>
                    </div>
                    <h3 class="feature-title"><?= e($f3Title) ?></h3>
                    <p class="feature-desc"><?= e($f3Text) ?></p>
                </div>
            </div>
        </div>

        <!-- 3 Statistic Cards with clear spacing from section above -->
        <div class="stats-row">
            <!-- Stat 1: Total Admin Projects -->
            <div class="stat-card">
                <div class="stat-icon"><?= render_icon('briefcase', '', 24) ?></div>
                <div class="stat-number"><?= e($s1Val) ?></div>
                <div class="stat-label"><?= e($s1Label) ?></div>
            </div>

            <!-- Stat 2: Total Company Logos -->
            <div class="stat-card">
                <div class="stat-icon"><?= render_icon('users', '', 24) ?></div>
                <div class="stat-number"><?= e($s2Val) ?></div>
                <div class="stat-label"><?= e($s2Label) ?></div>
            </div>

            <!-- Stat 3: 1 Year Experience -->
            <div class="stat-card">
                <div class="stat-icon"><?= render_icon('award', '', 24) ?></div>
                <div class="stat-number"><?= e($s3Val) ?></div>
                <div class="stat-label"><?= e($s3Label) ?></div>
            </div>
        </div>
    </div>
</section>
