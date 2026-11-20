<?php
/**
 * Unified Blaines website search helpers.
 *
 * Supports Interiors, projects, pages and news.
 * Transactional electrical products are handled separately
 * by blaines_get_product_search_ids().
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

/**
 * Public content types included in the editorial search.
 */
function blaines_search_content_post_types(): array {
    return [
        'page',
        'post',
        'kitchen',
        'bathroom',
        'bedroom',
        'project',
    ];
}

/**
 * Pages excluded from public search results.
 *
 * Use slugs rather than database IDs so that exclusions
 * remain portable between Local, Staging and Live.
 */
function blaines_search_excluded_page_slugs(): array {
    return [
        'cart',
        'checkout',
        'my-account',
        'shop',
        'home',
        'landing-page',
        'sample-page',
        'debug',
        'cta',
        'test-for-mytheme',
        'electrical-product-test',
        'privacy-policy',
        'electrical-terms-of-sale',
        'electrical-returns-refunds-cancellations',
    ];
}

/**
 * Resolve excluded page IDs.
 */
function blaines_search_excluded_page_ids(): array {
    $ids = [];

    foreach ( blaines_search_excluded_page_slugs() as $slug ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );

        if ( $page instanceof WP_Post ) {
            $ids[] = (int) $page->ID;
        }
    }

    return $ids;
}

/**
 * Match general website content by title, excerpt or body.
 *
 * Returns IDs rather than rendered posts, allowing the
 * search template to control presentation and pagination.
 */
function blaines_search_content_ids( string $search_term ): array {
    $search_term = trim( $search_term );

    if ( '' === $search_term ) {
        return [];
    }

    $ids = get_posts(
        [
            'post_type'           => blaines_search_content_post_types(),
            'post_status'         => 'publish',
            'posts_per_page'      => -1,
            'fields'              => 'ids',
            's'                   => $search_term,
            'post__not_in'        => blaines_search_excluded_page_ids(),
            'ignore_sticky_posts' => true,
        ]
    );

    return array_values(
        array_unique( array_map( 'absint', $ids ) )
    );
}

/**
 * Map department search terms to their Interiors post types.
 *
 * This allows a search for "bathrooms" to discover bathroom
 * ranges whose titles are model names such as Elan or Aruba.
 */
function blaines_search_department_post_type( string $search_term ): string {
    $term = strtolower( trim( $search_term ) );

    $departments = [
        'kitchen'  => 'kitchen',
        'kitchens' => 'kitchen',
        'bathroom' => 'bathroom',
        'bathrooms'=> 'bathroom',
        'bedroom'  => 'bedroom',
        'bedrooms' => 'bedroom',
    ];

    return $departments[ $term ] ?? '';
}

/**
 * Retrieve unified editorial search matches.
 *
 * Direct keyword matches come first, followed by relevant
 * Interiors ranges for exact department searches.
 */
function blaines_get_editorial_search_ids( string $search_term ): array {
    $ids = blaines_search_content_ids( $search_term );

    $department_type = blaines_search_department_post_type(
        $search_term
    );

    if ( $department_type ) {
        $department_ids = get_posts(
            [
                'post_type'      => $department_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'orderby'        => 'title',
                'order'          => 'ASC',
            ]
        );

        $ids = array_merge( $ids, $department_ids );
    }

    return array_values(
        array_unique( array_map( 'absint', $ids ) )
    );
}

