<?php
/**
 * Kitchen archive / taxonomy template
 * - Page header (hero + intro)
 * - Opposite-taxonomy filter bar
 * - Combined style + brand filtering via query args
 * - Results grid + pagination
 *
 * @package blainestwentysix
 */

get_header();

$brand_logo = null;
$term       = is_tax() ? get_queried_object() : null;

/**
 * -------------------------------------------------------
 * Header content
 * -------------------------------------------------------
 */
if ( is_tax() && $term && ! is_wp_error( $term ) ) {
    $term_key   = $term->taxonomy . '_' . $term->term_id;
    $hero       = function_exists( 'get_field' ) ? get_field( 'hero_image', $term_key ) : null;
    $brand_logo = function_exists( 'get_field' ) ? get_field( 'brand_logo', $term_key ) : null;
    $intro_t    = function_exists( 'get_field' ) ? get_field( 'intro_title', $term_key ) : null;
    $intro_txt  = function_exists( 'get_field' ) ? get_field( 'intro_text', $term_key ) : null;

    $title = $intro_t ?: $term->name;

} elseif ( is_post_type_archive( 'kitchen' ) ) {
    $hero      = function_exists( 'get_field' ) ? get_field( 'kitchen_archive_hero_image', 'option' ) : null;
    $intro_t   = function_exists( 'get_field' ) ? get_field( 'kitchen_archive_intro_title', 'option' ) : null;
    $intro_txt = function_exists( 'get_field' ) ? get_field( 'kitchen_archive_intro_content', 'option' ) : null;

    $title = $intro_t ?: post_type_archive_title( '', false );

} else {
    $hero      = null;
    $intro_txt = null;
    $title     = null;
}

get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero,
        'title'       => $title,
        'intro'       => $intro_txt,
        'breadcrumbs' => true,
        'align'       => 'center',
        'logo'        => $brand_logo,
        'hero_style'  => 'editorial',
        'panel_side'  => 'left',
    ]
);

/**
 * -------------------------------------------------------
 * Current archive / filter state
 * -------------------------------------------------------
 */
$current_object    = get_queried_object();
$current_taxonomy  = is_tax() && isset( $current_object->taxonomy ) ? $current_object->taxonomy : '';
$current_term_slug = is_tax() && isset( $current_object->slug ) ? $current_object->slug : '';

$current_style = isset( $_GET['kitchen_style'] )
    ? sanitize_title( wp_unslash( $_GET['kitchen_style'] ) )
    : '';

$current_brand = isset( $_GET['kitchen_brand'] )
    ? sanitize_title( wp_unslash( $_GET['kitchen_brand'] ) )
    : '';

/**
 * If we're on a taxonomy archive, use that taxonomy term
 * as the starting state unless the same dimension is already
 * supplied explicitly in the query string.
 */
if ( 'kitchen_style' === $current_taxonomy && ! $current_style ) {
    $current_style = $current_term_slug;
}

if ( 'kitchen_brand' === $current_taxonomy && ! $current_brand ) {
    $current_brand = $current_term_slug;
}

/**
 * -------------------------------------------------------
 * Opposite-taxonomy pill bar config
 * -------------------------------------------------------
 */
$filter_taxonomy = 'kitchen_brand';
$filter_label    = __( 'Kitchen brands', 'blainestwentysix' );

if ( 'kitchen_brand' === $current_taxonomy ) {
    $filter_taxonomy = 'kitchen_style';
    $filter_label    = __( 'Kitchen styles', 'blainestwentysix' );
}

/**
 * -------------------------------------------------------
 * Pagination state
 * -------------------------------------------------------
 */
$paged = max( 1, (int) get_query_var( 'paged' ) );

/**
 * -------------------------------------------------------
 * Build combined query
 * -------------------------------------------------------
 */
$tax_query = [];

if ( $current_style ) {
    $tax_query[] = [
        'taxonomy' => 'kitchen_style',
        'field'    => 'slug',
        'terms'    => [ $current_style ],
    ];
}

if ( $current_brand ) {
    $tax_query[] = [
        'taxonomy' => 'kitchen_brand',
        'field'    => 'slug',
        'terms'    => [ $current_brand ],
    ];
}

$args = [
    'post_type'      => 'kitchen',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => 'title',
    'order'          => 'ASC',
];

if ( ! empty( $tax_query ) ) {
    if ( count( $tax_query ) > 1 ) {
        $args['tax_query'] = array_merge( [ 'relation' => 'AND' ], $tax_query );
    } else {
        $args['tax_query'] = $tax_query;
    }
}

$query = new WP_Query( $args );
?>

    <section class="pb-10 lg:pb-12">
        <div class="container-wide relative">

            <?php
            get_template_part(
                'template-parts/components/filter-bar-taxonomy',
                null,
                [
                    'taxonomy'         => $filter_taxonomy,
                    'post_type'        => 'kitchen',
                    'aria_label'       => $filter_label,
                    'current_style'    => $current_style,
                    'current_brand'    => $current_brand,
                    'current_taxonomy' => $current_taxonomy,
                ]
            );
            ?>

            <div class="prose-site mb-6 mt-2 flex items-center gap-3 text-sm text-slate-700">
            <span
                    class="flex h-[30px] w-[30px] items-center justify-center rounded-full border border-slate-300 bg-white text-sm font-semibold leading-none text-[var(--freedom-accent)]"
                    aria-hidden="true"
            >
                F
            </span>
                <p class="m-0"> = Available with Freedom accessibility options</p>
            </div>

            <div
                    id="js-kitchen-grid"
                    class="grid gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-3"
                    data-kitchen-results
            >
                <?php if ( $query->have_posts() ) : ?>
                    <?php
                    while ( $query->have_posts() ) :
                        $query->the_post();
                        get_template_part( 'template-parts/kitchens/kitchen', 'card' );
                    endwhile;
                    ?>
                <?php else : ?>
                    <p class="text-center text-slate-600">No kitchens were found.</p>
                <?php endif; ?>
            </div>

            <?php wp_reset_postdata(); ?>

            <div id="js-kitchen-pagination" class="mt-4" data-kitchen-pagination>
                <?php
                get_template_part(
                    'template-parts/components/pagination',
                    null,
                    [
                        'query'    => $query,
                        'current'  => $paged,
                        'total'    => $query->max_num_pages,
                        'add_args' => array_filter(
                            [
                                'kitchen_style' => $current_style ?: null,
                                'kitchen_brand' => $current_brand ?: null,
                            ]
                        ),
                    ]
                );
                ?>
            </div>

        </div>
    </section>

<?php
get_template_part(
    'template-parts/components/brand-logo-slider',
    null,
    [
        'variant' => 'interiors',
    ]
);

get_footer();