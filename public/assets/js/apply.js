/**
 * Modabber Project Application Form Script
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#apply-form');
  const statusArea = document.querySelector('#apply-status');

  // Convert digits to Persian
  const toFaDigits = (val) => {
    const fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return String(val).replace(/[0-9]/g, (w) => fa[+w]);
  };

  // Format bytes to Persian MB/KB
  const formatFileSize = (bytes) => {
    if (bytes < 1024 * 1024) {
      return `${toFaDigits(Math.round(bytes / 1024))} کیلوبایت`;
    }
    return `${toFaDigits((bytes / (1024 * 1024)).toFixed(1))} مگابایت`;
  };

  // Check if file is valid (PDF, DOC, DOCX)
  const isAllowedFile = (file) => {
    const name = file.name.toLowerCase();
    const isDoc = name.endsWith('.doc') || name.endsWith('.docx') || file.type.includes('word') || file.type.includes('officedocument');
    const isPdf = name.endsWith('.pdf') || file.type === 'application/pdf';
    return isPdf || isDoc;
  };

  // 1. Focus on status area if it contains alerts
  if (statusArea && statusArea.children.length > 0) {
    statusArea.focus();
  }

  // 2. Character counter for message textarea
  const messageInput = document.querySelector('#message');
  const counterDisplay = document.querySelector('#message-counter');
  if (messageInput && counterDisplay) {
    const updateCounter = () => {
      const len = messageInput.value.length;
      counterDisplay.textContent = `${toFaDigits(len)} / ۲۰۰۰`;
    };
    messageInput.addEventListener('input', updateCounter);
    updateCounter();
  }

  // 3. File Dropzone & Input Display
  const fileInput = document.querySelector('#resume-file');
  const dropzone = document.querySelector('#file-dropzone');
  const fileNameDisplay = document.querySelector('#file-name-display');
  const fileBadge = document.querySelector('#file-selected-badge');
  const fileErrorDisplay = document.querySelector('#resume-error');

  const handleFileSelection = (file) => {
    if (!file) {
      if (fileBadge) fileBadge.style.display = 'none';
      if (fileNameDisplay) fileNameDisplay.textContent = 'فایلی انتخاب نشده';
      return;
    }

    const validFormat = isAllowedFile(file);
    const isSmallEnough = file.size <= 5 * 1024 * 1024;

    if (!validFormat) {
      if (fileBadge) fileBadge.style.display = 'inline-flex';
      if (fileNameDisplay) fileNameDisplay.textContent = 'فرمت نامعتبر است';
      if (fileErrorDisplay) fileErrorDisplay.textContent = 'فرمت مجاز نمی‌باشد. تنها فایل‌های PDF و Word (doc, docx) مجاز هستند.';
      fileInput.setAttribute('aria-invalid', 'true');
    } else if (!isSmallEnough) {
      if (fileBadge) fileBadge.style.display = 'inline-flex';
      if (fileNameDisplay) fileNameDisplay.textContent = 'حجم فایل زیاد است';
      if (fileErrorDisplay) fileErrorDisplay.textContent = 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.';
      fileInput.setAttribute('aria-invalid', 'true');
    } else {
      if (fileBadge) fileBadge.style.display = 'inline-flex';
      if (fileNameDisplay) fileNameDisplay.textContent = `${file.name} (${formatFileSize(file.size)})`;
      if (fileErrorDisplay) fileErrorDisplay.textContent = '';
      fileInput.removeAttribute('aria-invalid');
    }
  };

  if (fileInput) {
    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files.length > 0) {
        handleFileSelection(fileInput.files[0]);
      } else {
        handleFileSelection(null);
      }
    });
  }

  // Drag and drop handlers on dropzone
  if (dropzone && fileInput) {
    ['dragenter', 'dragover'].forEach(evt => {
      dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('drag-active');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('drag-active');
      });
    });

    dropzone.addEventListener('drop', (e) => {
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        handleFileSelection(e.dataTransfer.files[0]);
      }
    });
  }

  // 4. Client-side Form Validation & Double-Submit Protection
  if (form) {
    form.addEventListener('submit', (e) => {
      let isValid = true;
      let firstInvalid = null;

      // Full Name Validation
      const nameInput = document.querySelector('#full_name');
      const nameError = document.querySelector('#full_name-error');
      if (nameInput) {
        const val = nameInput.value.trim();
        if (val.length === 0) {
          isValid = false;
          if (nameError) nameError.textContent = 'نام و نام خانوادگی را وارد کنید.';
          nameInput.setAttribute('aria-invalid', 'true');
          firstInvalid = firstInvalid || nameInput;
        } else if (val.length < 2 || val.length > 100) {
          isValid = false;
          if (nameError) nameError.textContent = 'نام باید بین ۲ تا ۱۰۰ نویسه باشد.';
          nameInput.setAttribute('aria-invalid', 'true');
          firstInvalid = firstInvalid || nameInput;
        } else {
          if (nameError) nameError.textContent = '';
          nameInput.removeAttribute('aria-invalid');
        }
      }

      // Phone Validation
      const phoneInput = document.querySelector('#phone');
      const phoneError = document.querySelector('#phone-error');
      if (phoneInput) {
        let pVal = phoneInput.value.trim();
        // Normalize digits to English
        const fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        for (let i = 0; i < 10; i++) {
          pVal = pVal.replaceAll(fa[i], String(i)).replaceAll(ar[i], String(i));
        }
        pVal = pVal.replace(/[\s\-\.\(\)\+]/g, '');
        if (pVal.startsWith('0098') && pVal.length === 14) pVal = '0' + pVal.substring(4);
        if (pVal.startsWith('98') && pVal.length === 12) pVal = '0' + pVal.substring(2);

        const phoneRegex = /^0[1-9][0-9]{9}$/;
        if (!phoneRegex.test(pVal)) {
          isValid = false;
          if (phoneError) phoneError.textContent = 'شماره تماس معتبر وارد کنید (مثال: ۰۹۱۲۱۲۳۴۵۶۷).';
          phoneInput.setAttribute('aria-invalid', 'true');
          firstInvalid = firstInvalid || phoneInput;
        } else {
          if (phoneError) phoneError.textContent = '';
          phoneInput.removeAttribute('aria-invalid');
        }
      }

      // Resume Validation
      if (fileInput) {
        if (!fileInput.files || fileInput.files.length === 0) {
          isValid = false;
          if (fileErrorDisplay) fileErrorDisplay.textContent = 'فایل رزومه را انتخاب کنید.';
          fileInput.setAttribute('aria-invalid', 'true');
          firstInvalid = firstInvalid || fileInput;
        } else {
          const file = fileInput.files[0];
          if (!isAllowedFile(file)) {
            isValid = false;
            if (fileErrorDisplay) fileErrorDisplay.textContent = 'تنها فایل‌های PDF و Word (doc, docx) مجاز هستند.';
            fileInput.setAttribute('aria-invalid', 'true');
            firstInvalid = firstInvalid || fileInput;
          } else if (file.size > 5 * 1024 * 1024) {
            isValid = false;
            if (fileErrorDisplay) fileErrorDisplay.textContent = 'حجم فایل نباید بیشتر از ۵ مگابایت باشد.';
            fileInput.setAttribute('aria-invalid', 'true');
            firstInvalid = firstInvalid || fileInput;
          }
        }
      }

      if (!isValid) {
        e.preventDefault();
        if (firstInvalid) {
          firstInvalid.focus();
        }
        return;
      }

      // Valid: Disable submit button & show loading text
      const submitBtn = form.querySelector('#apply-submit-btn');
      if (submitBtn) {
        submitBtn.setAttribute('disabled', 'true');
        submitBtn.textContent = 'در حال ارسال...';
      }
    });
  }
});
