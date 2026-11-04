<?php
/**
 * Template routing & query adjustments.
 *
 * @package Blaines
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ==========================================================
   KITCHEN SEARCH ROUTING
   ========================================================== */

/**
 * Route Kitchen searches to the Kitchen archive template.
 */
function blt_route_kitchen_search_to_archive_template( string $template ): string {

    if ( ! is_search() ) {
        return $template;
    }

    $post_type = get_query_var( 'post_type' );

    // Only intercept Kitchen searches.
    if ( 'kitchen' !== $post_type ) {
        return $template;
    }

    $archive_template = locate_template( 'archive-kitchen.php' );

    return $archive_template
        ? $archive_template
        : $template;
}

add_filter(
    'template_include',
    'blt_route_kitchen_search_to_archive_template'
);


/* ==========================================================
   ELECTRICAL DEPARTMENT ROUTING
   ========================================================== */

/**
 * Route controlled Electrical department pages through one
 * shared transactional department template.
 */
function blaines_route_electrical_department_template(
    string $template
): string {

    if ( ! is_page() ) {
        return $template;
    }

    $page_id = get_queried_object_id();

    if ( ! $page_id ) {
        return $template;
    }

    $page = get_post( $page_id );

    if ( ! $page instanceof WP_Post ) {
        return $template;
    }

    /*
     * Only these controlled Electrical department pages
     * use the shared transactional archive template.
     */
    $department_slugs = [
        'laundry',
        'refrigeration',
        'dishwashers',
        'cooking',
        'small-appliances',
        'floorcare',
        'tv-entertainment',
    ];

    if ( ! in_array( $page->post_name, $department_slugs, true ) ) {
        return $template;
    }

    /*
     * Ensure the page genuinely sits beneath /electrical/.
     * This prevents an unrelated page elsewhere on the site
     * with the same slug from being intercepted.
     */
    $parent_id = wp_get_post_parent_id( $page_id );

    if ( ! $parent_id ) {
        return $template;
    }

    $parent_slug = get_post_field(
        'post_name',
        $parent_id
    );

    if ( 'electrical' !== $parent_slug ) {
        return $template;
    }

    /*
     * Require a matching WooCommerce product category.
     */
    $product_category = get_term_by(
        'slug',
        $page->post_name,
        'product_cat'
    );

    if ( ! $product_category instanceof WP_Term ) {
        return $template;
    }

    $department_template = locate_template(
        'page-electrical-department.php'
    );

    return $department_template
        ? $department_template
        : $template;
}

add_filter(
    'template_include',
    'blaines_route_electrical_department_template',
    20
);