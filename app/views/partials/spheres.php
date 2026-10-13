<?php
/**
 * Modabber Decorative Spheres Partial
 *
 * @var bool $with_ring
 */
$withRing = $with_ring ?? false;
?>
<div class="sphere-wrapper" aria-hidden="true">
    <div class="sphere sphere-1"></div>
    <div class="sphere sphere-2"></div>
    <div class="sphere sphere-3"></div>
    <div class="sphere sphere-4"></div>
    <div class="sphere sphere-5"></div>
    <?php if ($withRing): ?>
        <div class="hero-ring sphere-ring-hero"></div>
    <?php endif; ?>
</div>
