/**
 * Modabber Public JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Sticky Header scroll effect
  const header = document.querySelector('.site-header');
  if (header) {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // 2. Marquee dynamic duration & smooth deceleration on hover
  const marqueeTrack = document.querySelector('.marquee-track');
  const marqueeContainer = document.querySelector('.marquee-container');

  if (marqueeTrack && marqueeContainer) {
    const duration = marqueeTrack.getAttribute('data-duration') || '35';
    marqueeTrack.style.setProperty('animation-duration', `${duration}s`);

    let currentRate = 1.0;
    let targetRate = 1.0;
    let rafId = null;

    const animateRate = () => {
      const anims = marqueeTrack.getAnimations ? marqueeTrack.getAnimations() : [];
      if (!anims || anims.length === 0) {
        rafId = null;
        return;
      }

      const diff = targetRate - currentRate;
      if (Math.abs(diff) > 0.01) {
        currentRate += diff * 0.06; // Smooth deceleration factor
        anims.forEach(anim => {
          try { anim.playbackRate = Math.max(0, currentRate); } catch (_) {}
        });
        rafId = requestAnimationFrame(animateRate);
      } else {
        currentRate = targetRate;
        anims.forEach(anim => {
          try { anim.playbackRate = currentRate; } catch (_) {}
        });
        rafId = null;
      }
    };

    marqueeContainer.addEventListener('mouseenter', () => {
      targetRate = 0;
      if (!rafId) rafId = requestAnimationFrame(animateRate);
    });

    marqueeContainer.addEventListener('mouseleave', () => {
      targetRate = 1.0;
      if (!rafId) rafId = requestAnimationFrame(animateRate);
    });
  }

  // 3. Password visibility toggle (Login page)
  const toggleBtn = document.querySelector('.password-toggle-btn');
  const passwordInput = document.querySelector('#password-input');
  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener('click', () => {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      toggleBtn.setAttribute('aria-label', isPassword ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
    });
  }

  // 4. Prevent double-submission on login form
  const loginForm = document.querySelector('#login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', () => {
      const submitBtn = loginForm.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.setAttribute('disabled', 'true');
        submitBtn.textContent = 'در حال بررسی اطلاعات...';
      }
    });
  }
});
