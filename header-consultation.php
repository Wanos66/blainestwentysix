<?php
/**
 * Minimal header for Design Consultation page
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('design-consultation-page'); ?>>
<?php wp_body_open(); ?>

<header class="border-b border-neutral-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-center px-6 py-4 md:py-5">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center justify-center" aria-label="<?php bloginfo('name'); ?>">
            <?php
            if (function_exists('the_custom_logo') && has_custom_logo()) {
                the_custom_logo();
            } else {
                ?>
                <span class="text-lg font-semibold uppercase tracking-[0.12em] text-neutral-900">
                    <?php bloginfo('name'); ?>
                </span>
                <?php
            }
            ?>
        </a>
    </div>
</header>
