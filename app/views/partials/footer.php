<?php
/**
 * Modabbernet Public Footer Partial
 */
$phone1 = '09231373855';
$phone2 = '09991642278';
$email  = 'info@modabbernet.ir';

$address1 = 'تبریز، دانشگاه سراسری تبریز، دانشکده مکانیک، طبقه اول';
$address2 = 'مرند، چهارراه شیخ رفیعیان، خانه خلاق و نوآوری ماندگار، طبقه دوم';

$siteName = 'مدبرنت';
?>
<footer class="site-footer">
    <div class="container footer-container">
        <!-- Row 1: Brand & Contact Info Centered -->
        <div class="footer-row-brand-contact">
            <div class="footer-brand-box">
                <?php render_partial('brand'); ?>
            </div>

            <div class="footer-contact-items">
                <!-- Phone 1 -->
                <a href="tel:<?= e($phone1) ?>" class="contact-pill-item">
                    <span class="contact-icon"><?= render_icon('phone', '', 18) ?></span>
                    <span dir="ltr" class="contact-val"><?= e($phone1) ?></span>
                </a>

                <!-- Phone 2 -->
                <a href="tel:<?= e($phone2) ?>" class="contact-pill-item">
                    <span class="contact-icon"><?= render_icon('phone', '', 18) ?></span>
                    <span dir="ltr" class="contact-val"><?= e($phone2) ?></span>
                </a>

                <!-- Email -->
                <a href="mailto:<?= e($email) ?>" class="contact-pill-item">
                    <span class="contact-icon"><?= render_icon('mail', '', 18) ?></span>
                    <span dir="ltr" class="contact-val"><?= e($email) ?></span>
                </a>
            </div>
        </div>

        <!-- Row 2: Addresses (آدرس) with compact, elegant layout -->
        <div class="footer-row-addresses">
            <span class="footer-addresses-title">آدرس‌ها:</span>
            <div class="footer-addresses-grid">
                <div class="address-pill-item">
                    <span class="address-icon"><?= render_icon('map-pin', '', 18) ?></span>
                    <span class="address-text"><strong>آدرس اول:</strong> <?= e($address1) ?></span>
                </div>
                <div class="address-pill-item">
                    <span class="address-icon"><?= render_icon('map-pin', '', 18) ?></span>
                    <span class="address-text"><strong>آدرس دوم:</strong> <?= e($address2) ?></span>
                </div>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-copyright">
            تمامی حقوق مدبرنت محفوظ است.
        </div>
    </div>
</footer>
