<?php
/**
 * Modabber Pagination Partial
 *
 * @var int $current_page
 * @var int $total_pages
 * @var string $base_url
 * @var string $anchor
 */
$currentPage = (int)($current_page ?? 1);
$totalPages  = (int)($total_pages ?? 1);
$anchorStr   = !empty($anchor) ? '#' . ltrim($anchor, '#') : '';

if ($totalPages <= 1) {
    return;
}
?>
<nav class="pagination-wrapper" aria-label="ناوبری صفحات">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i === $currentPage): ?>
            <span class="page-num active" aria-current="page"><?= fa_digits($i) ?></span>
        <?php else: ?>
            <a href="?page=<?= $i ?><?= $anchorStr ?>" class="page-num" aria-label="صفحه <?= fa_digits($i) ?>">
                <?= fa_digits($i) ?>
            </a>
        <?php endif; ?>
    <?php endfor; ?>
</nav>
