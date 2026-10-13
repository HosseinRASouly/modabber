<?php
/**
 * Modabber Logo Marquee Partial
 *
 * @var array $logos
 */
$caption = setting('logos_caption', 'مورد اعتماد شرکت‌ها و سازمان‌ها');
$logoCount = count($logos);

if ($logoCount === 0) {
    return;
}

// Calculate duration: ~5s per logo, minimum 24s
$duration = max(24, $logoCount * 5);

// If fewer than 8 logos, repeat the set in each group so it spans wider than viewport
$displayLogos = $logos;
while (count($displayLogos) < 8) {
    $displayLogos = array_merge($displayLogos, $logos);
}
?>
<div class="marquee-wrapper">
    <?php if (!empty($caption)): ?>
        <p class="marquee-caption"><?= e($caption) ?></p>
    <?php endif; ?>

    <div class="marquee-container" aria-label="لوگوی شرکت‌های همکار">
        <div class="marquee-track" data-duration="<?= (int)$duration ?>">
            <!-- Group 1 -->
            <div class="marquee-group">
                <?php foreach ($displayLogos as $logo): ?>
                    <div class="logo-card">
                        <?php
                        $imgSrc = !empty($logo['image']) && file_exists(PUBLIC_PATH . '/uploads/logos/' . $logo['image'])
                            ? url('/uploads/logos/' . $logo['image'])
                            : asset('/assets/img/project-placeholder.svg');
                        ?>
                        <?php if (!empty($logo['website_url']) && is_valid_url($logo['website_url'])): ?>
                            <a href="<?= e($logo['website_url']) ?>" target="_blank" rel="noopener noreferrer nofollow" title="<?= e($logo['name']) ?>">
                                <img src="<?= e($imgSrc) ?>" alt="<?= e($logo['name']) ?>" class="logo-img" loading="lazy">
                            </a>
                        <?php else: ?>
                            <img src="<?= e($imgSrc) ?>" alt="<?= e($logo['name']) ?>" class="logo-img" loading="lazy">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Group 2 (Identical for seamless infinite scroll) -->
            <div class="marquee-group" aria-hidden="true">
                <?php foreach ($displayLogos as $logo): ?>
                    <div class="logo-card">
                        <?php
                        $imgSrc = !empty($logo['image']) && file_exists(PUBLIC_PATH . '/uploads/logos/' . $logo['image'])
                            ? url('/uploads/logos/' . $logo['image'])
                            : asset('/assets/img/project-placeholder.svg');
                        ?>
                        <?php if (!empty($logo['website_url']) && is_valid_url($logo['website_url'])): ?>
                            <a href="<?= e($logo['website_url']) ?>" tabindex="-1" target="_blank" rel="noopener noreferrer nofollow" title="<?= e($logo['name']) ?>">
                                <img src="<?= e($imgSrc) ?>" alt="<?= e($logo['name']) ?>" class="logo-img" loading="lazy">
                            </a>
                        <?php else: ?>
                            <img src="<?= e($imgSrc) ?>" alt="<?= e($logo['name']) ?>" class="logo-img" loading="lazy">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
