/**
 * Modabber Admin Panel JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Drawer Toggle
  const menuBtn = document.querySelector('#mobile-menu-btn');
  const sidebar = document.querySelector('.panel-sidebar');
  const overlay = document.querySelector('.sidebar-overlay');

  const openDrawer = () => {
    if (sidebar && overlay) {
      sidebar.classList.add('open');
      overlay.classList.add('active');
      document.body.style.setProperty('overflow', 'hidden');
      if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
    }
  };

  const closeDrawer = () => {
    if (sidebar && overlay) {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
      document.body.style.removeProperty('overflow');
      if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
    }
  };

  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      const isOpen = sidebar?.classList.contains('open');
      if (isOpen) closeDrawer();
      else openDrawer();
    });
  }

  if (overlay) {
    overlay.addEventListener('click', closeDrawer);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar?.classList.contains('open')) {
      closeDrawer();
    }
  });

  // 2. Budget Input Live Thousands Separators
  const budgetInput = document.querySelector('#budget-input');
  if (budgetInput) {
    budgetInput.addEventListener('input', (e) => {
      let val = e.target.value;
      // Convert Persian/Arabic digits to English for clean numeric processing
      const fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
      const ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
      for (let i = 0; i < 10; i++) {
        val = val.replaceAll(fa[i], String(i)).replaceAll(ar[i], String(i));
      }
      val = val.replace(/[^0-9]/g, '');
      if (val === '') {
        e.target.value = '';
        return;
      }
      // Format with Persian thousands separator
      const num = parseInt(val, 10);
      const formatted = num.toLocaleString('fa-IR');
      e.target.value = formatted;
    });
  }

  // 3. Short description character counter
  const shortDescInput = document.querySelector('#short-desc-input');
  const shortDescCounter = document.querySelector('#short-desc-counter');
  if (shortDescInput && shortDescCounter) {
    const toFaDigits = (val) => {
      const fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
      return String(val).replace(/[0-9]/g, (w) => fa[+w]);
    };
    const updateCount = () => {
      shortDescCounter.textContent = `${toFaDigits(shortDescInput.value.length)} / ۲۰۰`;
    };
    shortDescInput.addEventListener('input', updateCount);
    updateCount();
  }

  // 4. Image Upload Instant Preview
  const imageUploadInput = document.querySelector('#image-upload-input');
  const imagePreviewContainer = document.querySelector('#image-preview-container');
  const imagePreviewElement = document.querySelector('#image-preview-elem');

  if (imageUploadInput && imagePreviewContainer && imagePreviewElement) {
    imageUploadInput.addEventListener('change', () => {
      if (imageUploadInput.files && imageUploadInput.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
          imagePreviewElement.src = e.target.result;
          imagePreviewContainer.style.setProperty('display', 'block');
        };
        reader.readAsDataURL(imageUploadInput.files[0]);
      }
    });
  }

  // 5. Accessible Delete Confirmation Dialog
  const deleteDialog = document.querySelector('#delete-dialog');
  const deleteForm = document.querySelector('#delete-dialog-form');
  const deleteButtons = document.querySelectorAll('.btn-delete-trigger');
  const cancelDialogBtn = document.querySelector('#dialog-cancel-btn');

  if (deleteDialog && deleteForm) {
    deleteButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const actionUrl = btn.getAttribute('data-action');
        if (actionUrl) {
          deleteForm.setAttribute('action', actionUrl);
          deleteDialog.showModal();
        }
      });
    });

    if (cancelDialogBtn) {
      cancelDialogBtn.addEventListener('click', () => {
        deleteDialog.close();
      });
    }

    deleteDialog.addEventListener('click', (e) => {
      const rect = deleteDialog.getBoundingClientRect();
      const isInDialog = (
        rect.top <= e.clientY &&
        e.clientY <= rect.top + rect.height &&
        rect.left <= e.clientX &&
        e.clientX <= rect.left + rect.width
      );
      if (!isInDialog) {
        deleteDialog.close();
      }
    });
  }
});
