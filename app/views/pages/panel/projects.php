<?php
/**
 * Modabber Admin Panel - Projects List View
 *
 * @var array $projects
 * @var int $current_page
 * @var int $total_pages
 */
$currency = app_config('CURRENCY_LABEL', 'تومان');
?>
<div class="panel-page-header">
    <h1 class="panel-page-title">پروژه‌ها</h1>
    <a href="<?= url('/panel/projects/create') ?>" class="btn btn-primary">
        <?= render_icon('plus', '', 18) ?>
        افزودن پروژه
    </a>
</div>

<div class="panel-card">
    <?php if (!empty($projects)): ?>
        <!-- Desktop Table View -->
        <div class="table-responsive panel-table-desktop">
            <table class="panel-table">
                <thead>
                    <tr>
                        <th style="width:70px;">تصویر</th>
                        <th>نام پروژه</th>
                        <th>بودجه</th>
                        <th>وضعیت</th>
                        <th>ترتیب</th>
                        <th style="width:160px;text-align:end;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <?php
                        $thumb = !empty($p['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $p['image'])
                            ? url('/uploads/projects/' . $p['image'])
                            : asset('/assets/img/project-placeholder.svg');
                        ?>
                        <tr>
                            <td>
                                <img src="<?= e($thumb) ?>" alt="<?= e($p['name']) ?>" class="table-thumb" width="56" height="56" loading="lazy">
                            </td>
                            <td>
                                <strong style="color:var(--color-ink);"><?= e($p['name']) ?></strong>
                            </td>
                            <td>
                                <span style="font-weight:700;color:var(--color-primary);">
                                    <?= format_money($p['budget']) ?> <?= e($currency) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ((int)$p['is_published'] === 1): ?>
                                    <span class="status-badge status-active">منتشر شده</span>
                                <?php else: ?>
                                    <span class="status-badge status-draft">پیش‌نویس</span>
                                <?php endif; ?>
                                <?php if (!empty($p['is_expired']) && (int)$p['is_expired'] === 1): ?>
                                    <span class="status-badge" style="background:#FEE2E2;color:#DC2626;margin-inline-start:4px;">منقضی</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= fa_digits($p['sort_order']) ?>
                            </td>
                            <td style="text-align:end;">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <a href="<?= url('/panel/projects/edit?id=' . (int)$p['id']) ?>" class="btn btn-outline btn-sm" title="ویرایش">
                                        <?= render_icon('edit', '', 16) ?>
                                        ویرایش
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm btn-delete-trigger"
                                        data-action="<?= url('/panel/projects?action=delete&id=' . (int)$p['id']) ?>"
                                        title="حذف"
                                    >
                                        <?= render_icon('trash', '', 16) ?>
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards List -->
        <div class="panel-card-mobile-list">
            <?php foreach ($projects as $p): ?>
                <?php
                $thumb = !empty($p['image']) && file_exists(PUBLIC_PATH . '/uploads/projects/' . $p['image'])
                    ? url('/uploads/projects/' . $p['image'])
                    : asset('/assets/img/project-placeholder.svg');
                ?>
                <div class="mobile-item-card">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <img src="<?= e($thumb) ?>" alt="<?= e($p['name']) ?>" class="table-thumb" width="56" height="56">
                        <div>
                            <strong style="color:var(--color-ink);display:block;margin-block-end:4px;"><?= e($p['name']) ?></strong>
                            <span style="font-weight:700;color:var(--color-primary);font-size:0.9rem;">
                                <?= format_money($p['budget']) ?> <?= e($currency) ?>
                            </span>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-block-start:8px;border-top:1px solid var(--color-border);">
                        <div>
                            <?php if ((int)$p['is_published'] === 1): ?>
                                <span class="status-badge status-active">منتشر شده</span>
                            <?php else: ?>
                                <span class="status-badge status-draft">پیش‌نویس</span>
                            <?php endif; ?>
                            <span style="font-size:0.85rem;color:var(--color-ink-muted);margin-inline-start:8px;">
                                ترتیب: <?= fa_digits($p['sort_order']) ?>
                            </span>
                        </div>

                        <div class="table-actions">
                            <a href="<?= url('/panel/projects/edit?id=' . (int)$p['id']) ?>" class="btn btn-outline btn-sm">
                                ویرایش
                            </a>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-delete-trigger"
                                data-action="<?= url('/panel/projects?action=delete&id=' . (int)$p['id']) ?>"
                            >
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php render_partial('pagination', [
            'current_page' => $current_page ?? 1,
            'total_pages'  => $total_pages ?? 1,
        ]); ?>
    <?php else: ?>
        <div class="empty-state">
            <p class="empty-state-title">هنوز هیچ پروژه‌ای ثبت نشده است.</p>
            <a href="<?= url('/panel/projects/create') ?>" class="btn btn-primary">
                ثبت اولین پروژه
            </a>
        </div>
    <?php endif; ?>
</div>
