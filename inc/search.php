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
        'cookie-policy',
        'electrical-terms-of-sale',
        // Electrical shopping departments are represented by WooCommerce archives.
        'electrical/cooking',
        'electrical/dishwashers',
        'electrical/floorcare',
        'electrical/laundry',
        'electrical/refrigeration',
        'electrical/small-appliances',
        'electrical/tv-entertainment',
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
 * Rank editorial matches for a useful first page of results.
 *
 * For a department search, show the main landing page, then the ranges,
 * followed by relevant projects and supporting information. For other
 * searches, prioritise exact and partial title matches over body matches.
 *
 * @param int    $post_id     Result post ID.
 * @param string $search_term Search phrase.
 * @param string $department Department post type, if applicable.
 * @return int Lower scores rank first.
 */
function blaines_search_editorial_score( int $post_id, string $search_term, string $department = '' ): int {
    $post = get_post( $post_id );

    if ( ! $post instanceof WP_Post ) {
        return 999;
    }

    $term  = strtolower( trim( $search_term ) );
    $title = strtolower( trim( (string) $post->post_title ) );
    $slug  = (string) $post->post_name;

    if ( '' !== $department ) {
        $department_slugs = [
            'kitchen'  => 'kitchens',
            'bathroom' => 'bathrooms',
            'bedroom'  => 'bedrooms',
        ];

        // Main department landing pages always lead department searches.
        if ( 'page' === $post->post_type && ( $department_slugs[ $department ] ?? '' ) === $slug ) {
            return 0;
        }

        // The named ranges are the primary results after the landing page.
        if ( $department === $post->post_type ) {
            return 10;
        }

        // Clearance belongs below ranges and projects, even when its slug
        // includes the department name.
        if ( 'page' === $post->post_type && false !== strpos( $slug, 'clearance' ) ) {
            return 30;
        }

        // Department-specific pages (e.g. Classic Kitchens and Modern
        // Kitchens) follow ranges but precede completed projects and general
        // website information such as About Us.
        if (
            'page' === $post->post_type
            && false !== strpos( $slug, $department )
        ) {
            return 15;
        }

        if ( 'project' === $post->post_type ) {
            return 20;
        }

        return 40;
    }

    if ( $title === $term ) {
        return 0;
    }

    if ( 0 === strpos( $title, $term ) ) {
        return 10;
    }

    if ( false !== strpos( $title, $term ) ) {
        return 20;
    }

    if ( in_array( $post->post_type, [ 'kitchen', 'bathroom', 'bedroom', 'project' ], true ) ) {
        return 30;
    }

    return 40;
}

/**
 * Retrieve unified editorial search matches in relevance order.
 *
 * Preserves the keyword search and department expansion. The result IDs
 * are deliberately ordered for WP_Query's orderby => post__in in search.php.
 */
function blaines_get_editorial_search_ids( string $search_term ): array {
    $search_term = trim( $search_term );

    if ( '' === $search_term ) {
        return [];
    }

    $ids             = blaines_search_content_ids( $search_term );
    $department_type = blaines_search_department_post_type( $search_term );

    if ( $department_type ) {
        $department_ids = get_posts(
            [
                'post_type'           => $department_type,
                'post_status'         => 'publish',
                'posts_per_page'      => -1,
                'fields'              => 'ids',
                'orderby'            => 'title',
                'order'              => 'ASC',
                'ignore_sticky_posts' => true,
            ]
        );

        $ids = array_merge( $ids, $department_ids );
    }

    $ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );

    // Preload posts once rather than executing database reads from the comparator.
    if ( $ids ) {
        _prime_post_caches( $ids, false, false );
    }

    usort(
        $ids,
        static function ( int $first, int $second ) use ( $search_term, $department_type ): int {
            $first_score  = blaines_search_editorial_score( $first, $search_term, $department_type );
            $second_score = blaines_search_editorial_score( $second, $search_term, $department_type );

            if ( $first_score !== $second_score ) {
                return $first_score <=> $second_score;
            }

            // Consistent ordering for equally relevant cards.
            return strcasecmp( get_the_title( $first ), get_the_title( $second ) )
                ?: ( $first <=> $second );
        }
    );

    return $ids;
}
