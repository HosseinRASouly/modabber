<?php
/**
 * Modabber Projects Section Partial
 *
 * @var array $projects
 * @var int $current_page
 * @var int $total_pages
 */
$subtitle = setting('projects_subtitle', 'نمونه‌ای از پروژه‌هایی که با افتخار انجام داده‌ایم.');
?>
<section class="projects-section" id="projects">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">پروژه‌ها</h2>
            <?php if (!empty($subtitle)): ?>
                <p class="section-desc"><?= e($subtitle) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($projects)): ?>
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <?php render_partial('project_card', ['project' => $project]); ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php render_partial('pagination', [
                'current_page' => $current_page ?? 1,
                'total_pages'  => $total_pages ?? 1,
                'anchor'       => 'projects',
            ]); ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="sphere sphere-2"></div>
                <p class="empty-state-title">هنوز پروژه‌ای ثبت نشده است.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
