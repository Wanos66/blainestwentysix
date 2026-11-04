<?php
/**
 * Home Intro Component (front page)
 */

if (!function_exists('get_field')) {
    return;
}

$enabled   = (bool) (get_field('home_intro_enabled') ?? true);
$eyebrow   = trim((string) get_field('home_intro_eyebrow'));
$heading   = trim((string) get_field('home_intro_heading'));
$subheading= trim((string) get_field('home_intro_subheading'));
$body      = trim((string) get_field('home_intro_body'));

if (!$enabled) return;

// If everything is empty, don't render.
if ($eyebrow === '' && $heading === '' && $subheading === '' && $body === '') return;
?>

<div class="container-medium py-16 text-center lg:py-20">
    <?php if ($eyebrow !== ''): ?>
        <p class="font-body text-base font-semibold uppercase tracking-[0.16em] text-brand md:text-lg">
            <?php echo esc_html($eyebrow); ?>
        </p>
    <?php endif; ?>

    <?php if ($heading !== '' || $subheading !== ''): ?>
    <h2 class="mt-3 font-heading text-4xl font-medium leading-tight tracking-wide text-gray-900 md:text-4xl">
            <?php if ($heading !== ''): ?>
                <?php echo esc_html($heading); ?>
            <?php endif; ?>

            <?php if ($subheading !== ''): ?>
                <?php if ($heading !== ''): ?><br><?php endif; ?>
                <span class="font-body font-medium text-3xl tracking wide text-gray-700"><?php echo esc_html($subheading); ?></span>
            <?php endif; ?>
        </h1>
    <?php endif; ?>

    <?php if ($body !== ''): ?>
        <div class="font-body font-light tracking-wide text-lg mx-auto mt-5 max-w-4xl text-center text-gray-700">
            <?php echo wp_kses_post(wpautop($body)); ?>
        </div>
    <?php endif; ?>
</div>
