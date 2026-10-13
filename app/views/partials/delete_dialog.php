<?php
/**
 * Modabber Delete Confirmation Dialog Partial
 */
?>
<dialog id="delete-dialog" aria-labelledby="dialog-title">
    <h3 id="dialog-title" style="font-size:1.25rem;font-weight:800;color:var(--color-ink);margin-block-end:12px;">
        تأیید عملیات حذف
    </h3>
    <p style="font-size:0.95rem;color:var(--color-ink-muted);line-height:1.8;">
        آیا از حذف این مورد مطمئن هستید؟ این عمل قابل بازگشت نیست.
    </p>

    <form id="delete-dialog-form" method="POST" action="">
        <?= csrf_field() ?>
        <div class="dialog-actions">
            <button type="button" id="dialog-cancel-btn" class="btn btn-outline btn-sm">
                انصراف
            </button>
            <button type="submit" class="btn btn-danger btn-sm">
                حذف نهایی
            </button>
        </div>
    </form>
</dialog>
