<?php
/**
 * Sidebar for standard pages
 *
 * @package blainestwentysix
 */

if (! is_active_sidebar('page-sidebar')) {
    return;
}
?>

<aside class="space-y-6 lg:sticky lg:top-32" aria-label="<?php esc_attr_e('Page sidebar', 'blainestwentysix'); ?>">
    <?php dynamic_sidebar('page-sidebar'); ?>
</aside>
