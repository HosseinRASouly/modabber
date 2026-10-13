<?php
/**
 * Modabber Flash Alerts Partial
 */
$successMsg = get_flash('success');
$errorMsg   = get_flash('error');
?>
<?php if (!empty($successMsg)): ?>
    <div class="alert alert-success" role="status" aria-live="polite">
        <?= render_icon('check', '', 20) ?>
        <span><?= e($successMsg) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger" role="alert" aria-live="assertive">
        <?= render_icon('x', '', 20) ?>
        <span><?= e($errorMsg) ?></span>
    </div>
<?php endif; ?>
