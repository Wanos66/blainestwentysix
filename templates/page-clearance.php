<?php
/**
 * Template Name: Clearance Products
 *
 * custom page template pagination uses pg
 * do not switch it back to paged
 */

get_header();

$breadcrumbs = $args['breadcrumbs'] ?? true;

$page_id = get_queried_object_id();

$electrical_clearance_term = get_term_by( 'slug', 'electrical-clearance', 'product_cat' );

if ( ! $electrical_clearance_term || is_wp_error( $electrical_clearance_term ) ) {
    get_footer();
    return;
}

/**
 * Header data
 */
$intro = has_excerpt($page_id) ? wpautop(get_the_excerpt($page_id)) : '';

$current_showroom = isset( $_GET['showroom'] ) ? absint( $_GET['showroom'] ) : 0;
$current_cat      = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
$current_sort     = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : '';

$electrical_child_slugs = get_terms(
    [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'fields'     => 'slugs',
        'parent'     => (int) $electrical_clearance_term->term_id,
    ]
);

if ( is_wp_error( $electrical_child_slugs ) ) {
    $electrical_child_slugs = [];
}

if ( $current_cat && ! in_array( $current_cat, $electrical_child_slugs, true ) ) {
    $current_cat = '';
}

/**
 * Query data
 */
$paged = isset( $_GET['pg'] ) ? max( 1, absint( $_GET['pg'] ) ) : 1;

$args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'tax_query'      => [
        [
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => [ 'electrical-clearance' ],
            'include_children' => true,
        ],
    ],
];

if ( $current_cat ) {
    $args['tax_query'][] = [
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => [ $current_cat ],
    ];
}

if ( $current_showroom ) {
    $args['meta_query'] = [
        'relation' => 'OR',
        [
            'key'     => 'showroom_page',
            'value'   => (string) $current_showroom,
            'compare' => '=',
        ],
        [
            'key'     => 'showroom_page',
            'value'   => '"' . $current_showroom . '"',
            'compare' => 'LIKE',
        ],
    ];
}

switch ( $current_sort ) {
    case 'price_asc':
        $args['meta_key'] = '_price';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'ASC';
        break;

    case 'price_desc':
        $args['meta_key'] = '_price';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
        break;

    case 'title_asc':
        $args['orderby'] = 'title';
        $args['order']   = 'ASC';
        break;

    case 'title_desc':
        $args['orderby'] = 'title';
        $args['order']   = 'DESC';
        break;

    default:
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
        break;
}

$query = new WP_Query($args);
?>

<?php
$hero_image = get_field( 'hero_image', $page_id );

if ( ! $hero_image && has_post_thumbnail( $page_id ) ) {
    $hero_image = get_post_thumbnail_id( $page_id );
}
?>

<?php if ( $hero_image ) : ?>
    <section class="w-full">
        <div class="w-full h-[320px] md:h-[400px] overflow-hidden">
            <?php
            echo wp_get_attachment_image(
                is_array( $hero_image ) ? $hero_image['ID'] : $hero_image,
                'full',
                false,
                [
                    'class' => 'inset-0 w-full h-full object-cover',
                ]
            );
            ?>
        </div>
    </section>
<?php endif; ?>

<div id="clearance-results"></div>

<?php if ( $breadcrumbs ) : ?>
    <div class="container-wide py-3 text-sm">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>
<?php endif; ?>

<?php
$has_active_filters = ! empty( $current_showroom ) || ! empty( $current_cat );
?>

    <section
            class="container-wide scroll-mt-28 pb-10 lg:pb-12"
            x-data="{ filtersOpen: <?php echo $has_active_filters ? 'true' : 'false'; ?> }"
    >
        <div class="grid grid-cols-12 gap-8 lg:gap-10">

            <div class="col-span-12 lg:hidden">
                <button
                        type="button"
                        class="flex w-full items-center justify-between border border-neutral-300 px-4 py-3 text-sm font-medium text-neutral-900"
                        @click="filtersOpen = !filtersOpen"
                        :aria-expanded="filtersOpen.toString()"
                        aria-controls="clearance-filters-mobile"
                >
                    <span x-text="filtersOpen ? 'Close filters' : 'Refine results'"></span>

                    <svg
                            class="h-4 w-4 transition-transform duration-200"
                            :class="{ 'rotate-180': filtersOpen }"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                    >
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.512a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <aside class="col-span-12 hidden lg:col-span-3 lg:block">
                <?php get_template_part('template-parts/clearance/sidebar-filters'); ?>
            </aside>

            <aside
                    id="clearance-filters-mobile"
                    class="col-span-12 lg:hidden"
                    x-cloak
                    x-show="filtersOpen"
                    x-collapse
            >
                <?php get_template_part('template-parts/clearance/sidebar-filters'); ?>
            </aside>

            <div class="col-span-12 lg:col-span-9 min-w-0">

                <?php
                get_template_part(
                    'template-parts/clearance/toolbar',
                    null,
                    [
                        'query'             => $query,
                        'current_sort'      => $current_sort,
                        'current_showroom'  => $current_showroom,
                        'current_cat'       => $current_cat,
                        'current_page'     => $paged,
                        'per_page'         => 12,
                    ]
                );
                ?>

                <?php if ($query->have_posts()) : ?>
                    <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                        <?php
                        while ($query->have_posts()) :
                            $query->the_post();

                            get_template_part(
                                'template-parts/clearance/card',
                                null,
                                [
                                    'product_id' => get_the_ID(),
                                ]
                            );
                        endwhile;
                        ?>
                        <?php wp_reset_postdata(); ?>
                    </div>

                    <div class="mt-10 border-t border-neutral-200 pt-6 text-sm">
                        <?php

                        $page_id = get_the_ID();
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
                                'add_args' => array_filter(
                                    [
                                        'showroom'    => $current_showroom ?: null,
                                        'product_cat' => $current_cat ?: null,
                                        'sort'        => $current_sort ?: null,
                                    ]
                                ),
                            ]
                        );
                        ?>
                    </div>

                    <?php wp_reset_postdata(); ?>

                <?php else : ?>
                    <div class="mt-8 border border-neutral-200 bg-white p-8 shadow-sm">
                        <p class="text-neutral-700">No clearance products available.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

<?php get_footer(); ?>