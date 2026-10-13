<?php
/**
 * Modabbernet Project Card Partial
 *
 * @var array $project
 */
$currency = app_config('CURRENCY_LABEL', 'تومان');
$projectUrl = url('/project/' . (int)$project['id']);
$imgSrc = !empty($project['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $project['image'])
    ? url('/uploads/projects/' . $project['image'])
    : asset('/assets/img/project-placeholder.svg');

$isExpired = !empty($project['is_expired']) && (int)$project['is_expired'] === 1;
?>
<article class="project-card <?= $isExpired ? 'is-expired-card' : '' ?>">
    <div class="project-thumb-wrapper">
        <a href="<?= $projectUrl ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e($imgSrc) ?>" alt="<?= e($project['name']) ?>" class="project-thumb" width="600" height="375" loading="lazy">
        </a>
        <?php if ($isExpired): ?>
            <!-- Top-left pill badge with red background -->
            <span class="project-expired-badge">منقضی شده</span>
        <?php endif; ?>
    </div>

    <div class="project-card-body">
        <h3 class="project-card-title">
            <a href="<?= $projectUrl ?>"><?= e($project['name']) ?></a>
        </h3>

        <p class="project-card-desc">
            <?= e($project['short_description']) ?>
        </p>

        <div class="project-card-footer">
            <!-- Physical Right in RTL: Budget Block OR Expired Text in Red -->
            <div class="budget-block">
                <?php if ($isExpired): ?>
                    <span class="expired-card-text">منقضی شده</span>
                <?php else: ?>
                    <span class="budget-label">بودجه</span>
                    <span class="budget-val">
                        <?= format_money($project['budget']) ?> <?= e($currency) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Physical Left in RTL: Pill Button -->
            <div class="card-action">
                <a href="<?= $projectUrl ?>" class="btn btn-primary btn-sm">
                    توضیحات بیشتر
                </a>
            </div>
        </div>
    </div>
</article>
