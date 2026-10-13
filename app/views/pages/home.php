<?php
/**
 * Modabber Home Page View
 *
 * @var array $logos
 * @var array $projects
 * @var int $current_page
 * @var int $total_pages
 */
?>
<!-- 1. Hero Section (straddling logo marquee) -->
<?php render_partial('hero', ['logos' => $logos ?? []]); ?>

<!-- 2. Features / Why Us Section -->
<?php render_partial('features'); ?>

<!-- 3. Projects Section with Pagination -->
<?php render_partial('projects_section', [
    'projects'     => $projects ?? [],
    'current_page' => $current_page ?? 1,
    'total_pages'  => $total_pages ?? 1,
]); ?>
