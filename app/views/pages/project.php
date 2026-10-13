<?php
/**
 * Modabber Project Detail Page View
 *
 * @var array $project
 */
$currency = app_config('CURRENCY_LABEL', 'تومان');
$imgSrc = !empty($project['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $project['image'])
    ? url('/uploads/projects/' . $project['image'])
    : asset('/assets/img/project-placeholder.svg');

// Split full description into paragraphs on blank lines
$paragraphs = preg_split('/\R{2,}/', trim($project['full_description']));
?>
<div class="container project-detail-container">
    <!-- Sphere decorations in background behind text -->
    <div class="project-detail-spheres-wrapper">
        <?php render_partial('spheres', ['with_ring' => false]); ?>
    </div>

    <!-- Breadcrumb -->
    <?php render_partial('breadcrumb', ['title' => $project['name']]); ?>

    <!-- Project Hero -->
    <div class="project-hero-grid">
        <!-- Physical RIGHT in RTL: Text & Budget Information -->
        <div class="project-hero-info-col">
            <h1 class="project-hero-title"><?= e($project['name']) ?></h1>
            <p class="project-hero-desc"><?= e($project['short_description']) ?></p>

            <!-- Budget or Expired Status -->
            <div class="project-budget-display">
                <?php if (!empty($project['is_expired']) && (int)$project['is_expired'] === 1): ?>
                    <span class="budget-value-text expired-detail-text">
                        قرارداد منقضی شده
                    </span>
                <?php else: ?>
                    <span class="budget-label-text">بودجه برآورد شده:</span>
                    <span class="budget-value-text">
                        <?= format_money($project['budget']) ?> <?= e($currency) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Physical LEFT in RTL: Project Image Column -->
        <div class="project-hero-img-col">
            <img src="<?= e($imgSrc) ?>" alt="<?= e($project['name']) ?>" class="project-hero-img" width="800" height="600">
        </div>
    </div>

    <!-- Full Description Centered - Not inside a card, no card background -->
    <div class="project-full-desc-container">
        <h2 class="project-full-desc-title">توضیحات تکمیلی</h2>
        <div class="project-full-desc-body">
            <?php foreach ($paragraphs as $para): ?>
                <?php if (trim($para) !== ''): ?>
                    <p class="project-desc-paragraph"><?= nl2br(e(trim($para))) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Application Form Section -->
<?php render_partial('application_form', ['project' => $project]); ?>
