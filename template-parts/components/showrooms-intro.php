<?php
/**
 * Showrooms Intro Component
 */

if (!function_exists('get_field')) return;

$enabled    = (bool) (get_field('showrooms_intro_enabled') ?? true);
$eyebrow    = trim((string) get_field('showrooms_intro_eyebrow'));
$heading    = trim((string) get_field('showrooms_intro_heading'));
$subheading = trim((string) get_field('showrooms_intro_subheading'));
$body       = trim((string) get_field('showrooms_intro_body'));

if (!$enabled) return;
if ($eyebrow === '' && $heading === '' && $subheading === '' && $body === '') return;
?>

<div class="container-medium text-center prose-section">
    <?php if ($eyebrow !== ''): ?>
        <h2 class="font-heading text-xl text-brand tracking-wider uppercase">
            <?php echo esc_html($eyebrow); ?>
        </h2>
    <?php endif; ?>

    <?php if ($heading !== '' || $subheading !== ''): ?>
        <h1 class="font-heading text-2xl font-semibold">
            <?php if ($heading !== ''): ?>
                <?php echo esc_html($heading); ?>
            <?php endif; ?>
            <?php if ($subheading !== ''): ?>
                <?php if ($heading !== ''): ?><br><?php endif; ?>
                <span class="font-semibold"><?php echo esc_html($subheading); ?></span>
            <?php endif; ?>
        </h1>
    <?php endif; ?>

    <?php if ($body !== ''): ?>
        <p class="font-body">
            <?php echo esc_html($body); ?>
        </p>
    <?php endif; ?>
</div>
