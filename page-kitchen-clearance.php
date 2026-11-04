<?php
/**
 * Template Name: Kitchen Clearance
 *
 * Archive-style page for ex-showroom kitchen display products.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$page_id = get_queried_object_id();

/**
 * -------------------------------------------------------
 * Header content
 * -------------------------------------------------------
 */
$hero = function_exists( 'get_field' ) ? get_field( 'hero_image', $page_id ) : null;

if ( ! $hero && has_post_thumbnail( $page_id ) ) {
    $hero = [
        'ID' => get_post_thumbnail_id( $page_id ),
    ];
}

$intro_txt = has_excerpt( $page_id ) ? wpautop( get_the_excerpt( $page_id ) ) : '';
$title     = get_the_title( $page_id );

get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero,
        'title'       => $title,
        'intro'       => "Maybe put a bit of general content here?",
        'breadcrumbs' => true,
        'align'       => 'center',
        'hero_style'  => 'editorial',
        'panel_side'  => 'left',
    ]
);

/**
 * Query data
 */
$paged = isset( $_GET['pg'] ) ? max( 1, absint( $_GET['pg'] ) ) : 1;

$query = new WP_Query(
    [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'tax_query'      => [
            [
                'taxonomy'         => 'product_cat',
                'field'            => 'slug',
                'terms'            => [ 'kitchen-clearance' ],
                'include_children' => false,
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]
);
?>

<section class="container-wide py-10 lg:py-14">

    <?php if ( $query->have_posts() ) : ?>

        <div class="grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-2 lg:grid-cols-3">
            <?php
            while ( $query->have_posts() ) :
                $query->the_post();

                get_template_part(
                    'template-parts/clearance/card-display-product',
                    null,
                    [
                        'product_id'   => get_the_ID(),
                        'display_type' => 'kitchen',
                    ]
                );
            endwhile;
            ?>
        </div>

        <?php if ( $query->max_num_pages > 1 ) : ?>
            <div class="mt-10 border-t border-neutral-200 pt-6 text-sm">
                <?php
                $page_url = get_permalink( $page_id );

                get_template_part(
                    'template-parts/components/pagination',
                    null,
                    [
                        'query'    => $query,
                        'current'  => $paged,
                        'total'    => $query->max_num_pages,
                        'base'     => trailingslashit( $page_url ) . '%_%',
                        'format'   => '?pg=%#%',
                        'add_args' => [],
                    ]
                );
                ?>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <div class="border border-neutral-200 bg-white p-8">
            <h2 class="font-heading text-2xl font-light text-neutral-950">
                No kitchen clearance displays available
            </h2>

            <p class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                We do not currently have any ex-showroom kitchen displays available. Please check back soon or contact your nearest showroom.
            </p>
        </div>

    <?php endif; ?>

</section>

<?php get_footer(); ?>
