<?php
/**
 * Accessible Kitchens page template scaffold
 *
 * First-pass structure only:
 * - Reuses existing page-header pattern
 * - Uses dedicated partials for each section
 * - Keeps content mostly hard-coded for now
 * - Ready for Tailwind styling and polish sweeps
 */

/* -------------------------------------------------------------------------- */
/* File: page-accessible-kitchens.php                                          */
/* -------------------------------------------------------------------------- */

/*
Template Name: Accessible Kitchens
*/

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

    $hero      = get_field('accessible_archive_hero_image', 'option');
    $intro_t   = get_field('accessible_archive_hero_title', 'option');
    $intro_txt = get_field('accessible_archive_hero_desc', 'option');
    $title     = $intro_t ?: post_type_archive_title('', false);
?>

<?php
get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero,
        'title'       => $title,
        'intro'       => $intro_txt,
        'breadcrumbs' => true,
        'hero_style'  => 'editorial',
        'panel_side'  => 'right',
    ]
);
?>

            <div class="border-t border-neutral-200">
                <?php get_template_part( 'template-parts/page/accessible-kitchens/intro' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/transition-image' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/features' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/process' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/considerations' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/gallery' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/faqs' ); ?>
                <?php get_template_part( 'template-parts/page/accessible-kitchens/cta' ); ?>
            </div>

        <?php
    endwhile;
endif; ?>

<?php get_footer(); ?>



