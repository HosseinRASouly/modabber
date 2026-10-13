<?php
/**
 * Modabber Admin Panel - Company Logos List View
 *
 * @var array $logos
 */
?>
<div class="panel-page-header">
    <h1 class="panel-page-title">لوگوی شرکت‌ها</h1>
    <a href="<?= url('/panel/logos/create') ?>" class="btn btn-primary">
        <?= render_icon('plus', '', 18) ?>
        افزودن لوگو
    </a>
</div>

<div class="panel-card">
    <?php if (!empty($logos)): ?>
        <div class="table-responsive panel-table-desktop">
            <table class="panel-table">
                <thead>
                    <tr>
                        <th style="width:90px;">لوگو</th>
                        <th>نام شرکت</th>
                        <th>وب‌سایت</th>
                        <th>وضعیت</th>
                        <th>ترتیب</th>
                        <th style="width:160px;text-align:end;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logos as $l): ?>
                        <?php
                        $thumb = !empty($l['image']) && file_exists(PUBLIC_PATH . '/uploads/logos/' . $l['image'])
                            ? url('/uploads/logos/' . $l['image'])
                            : asset('/assets/img/project-placeholder.svg');
                        ?>
                        <tr>
                            <td>
                                <div style="width:68px;height:48px;border-radius:12px;background:var(--color-primary-soft);display:flex;align-items:center;justify-content:center;padding:4px;">
                                    <img src="<?= e($thumb) ?>" alt="<?= e($l['name']) ?>" style="max-height:36px;max-width:56px;object-fit:contain;">
                                </div>
                            </td>
                            <td>
                                <strong style="color:var(--color-ink);"><?= e($l['name']) ?></strong>
                            </td>
                            <td>
                                <?php if (!empty($l['website_url'])): ?>
                                    <a href="<?= e($l['website_url']) ?>" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary);text-decoration:underline;" dir="ltr">
                                        <?= e($l['website_url']) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:var(--color-ink-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$l['is_active'] === 1): ?>
                                    <span class="status-badge status-active">فعال</span>
                                <?php else: ?>
                                    <span class="status-badge status-draft">غیرفعال</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= fa_digits($l['sort_order']) ?>
                            </td>
                            <td style="text-align:end;">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <a href="<?= url('/panel/logos/edit?id=' . (int)$l['id']) ?>" class="btn btn-outline btn-sm">
                                        <?= render_icon('edit', '', 16) ?>
                                        ویرایش
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm btn-delete-trigger"
                                        data-action="<?= url('/panel/logos?action=delete&id=' . (int)$l['id']) ?>"
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

        <!-- Mobile Stacked View -->
        <div class="panel-card-mobile-list">
            <?php foreach ($logos as $l): ?>
                <?php
                $thumb = !empty($l['image']) && file_exists(PUBLIC_PATH . '/uploads/logos/' . $l['image'])
                    ? url('/uploads/logos/' . $l['image'])
                    : asset('/assets/img/project-placeholder.svg');
                ?>
                <div class="mobile-item-card">
                    <div style="display:flex;gap:12px;align-items:center;">
                        <div style="width:68px;height:48px;border-radius:12px;background:var(--color-primary-soft);display:flex;align-items:center;justify-content:center;padding:4px;">
                            <img src="<?= e($thumb) ?>" alt="<?= e($l['name']) ?>" style="max-height:36px;max-width:56px;object-fit:contain;">
                        </div>
                        <div>
                            <strong style="color:var(--color-ink);display:block;"><?= e($l['name']) ?></strong>
                            <?php if ((int)$l['is_active'] === 1): ?>
                                <span class="status-badge status-active">فعال</span>
                            <?php else: ?>
                                <span class="status-badge status-draft">غیرفعال</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-block-start:8px;border-top:1px solid var(--color-border);">
                        <span style="font-size:0.85rem;color:var(--color-ink-muted);">
                            ترتیب: <?= fa_digits($l['sort_order']) ?>
                        </span>
                        <div class="table-actions">
                            <a href="<?= url('/panel/logos/edit?id=' . (int)$l['id']) ?>" class="btn btn-outline btn-sm">
                                ویرایش
                            </a>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-delete-trigger"
                                data-action="<?= url('/panel/logos?action=delete&id=' . (int)$l['id']) ?>"
                            >
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p class="empty-state-title">هنوز لوگویی برای شرکت‌ها ثبت نشده است.</p>
            <a href="<?= url('/panel/logos/create') ?>" class="btn btn-primary">
                ثبت اولین لوگو
            </a>
        </div>
    <?php endif; ?>
</div>
