<?php
/**
 * Archive template for Bathrooms
 *
 * @package blainestwentysix
 */

get_header();

$hero = null;
$title = null;
$intro_txt = null;
$brand_logo = null;

$term = is_tax() ? get_queried_object() : null;

if ( is_tax() && $term && ! is_wp_error($term) ) {
    $term_key   = $term->taxonomy . '_' . $term->term_id;

    $hero       = function_exists('get_field') ? get_field('hero_image', $term_key) : null;
    $brand_logo = function_exists('get_field') ? get_field('brand_logo', $term_key) : null;

    $intro_t    = function_exists('get_field') ? get_field('intro_title', $term_key) : null;
    $intro_txt  = function_exists('get_field') ? get_field('intro_text', $term_key) : null;

    $title      = $intro_t ?: $term->name;

} elseif ( is_post_type_archive('bathroom') ) {
    // Options fields (use your actual field names)
    $hero      = function_exists('get_field') ? get_field('bathroom_archive_hero_image', 'option') : null;
    $intro_t   = function_exists('get_field') ? get_field('bathroom_archive_intro_title', 'option') : null;
    $intro_txt = function_exists('get_field') ? get_field('bathroom_archive_intro_content', 'option') : null;

    $title     = $intro_t ?: post_type_archive_title('', false);
}

get_template_part('template-parts/components/page-header', null, [
    'hero'        => $hero,
    'title'       => $title,
    'intro'       => $intro_txt,
    'breadcrumbs' => true,
    'align'       => 'center',
    'logo'        => $brand_logo,
    'hero_style'  => 'editorial',
    'panel_side'  => 'right',
]);
?>

<!-- RESULTS GRID -->
<section class="pb-10 lg:pb-12">
    <div class="container-wide relative">

        <?php
        get_template_part(
            'template-parts/components/filter-bar-taxonomy',
            null,
            [
                'taxonomy'         => 'bathroom_brand',
                'post_type'        => 'bathroom',
                'aria_label'       => __( 'Bathroom brands', 'blainestwentysix' ),
                'current_taxonomy' => get_query_var( 'bathroom_brand', '' ),
            ]
        );
        ?>

        <div class="grid gap-6 sm:gap-8 sm:grid-cols-2 lg:grid-cols-3" data-bathroom-results>
        <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/bathrooms/bathroom', 'card' );
                endwhile;
            else :
                echo '<p class="text-center text-slate-600">No bathrooms were found.</p>';
            endif;
            ?>
        </div>

        <div class="mt-4" data-bathroom-pagination>
            <?php get_template_part( 'template-parts/components/pagination' ); ?>
        </div>

    </div>
</section>

<?php get_template_part('template-parts/components/brand-logo-slider', null, [
    'variant' => 'interiors',
]); ?>

<?php get_footer(); ?>

