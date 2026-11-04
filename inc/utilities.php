<?php
/**
 * Utility functions (AJAX handlers, query filters, tag helpers, SVG helpers)
 *
 * @package blainestwentysix
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function blainestwentysix_filters_enabled(): bool {
    if ( defined('BLAINES_FILTERS_ENABLED') ) {
        return (bool) BLAINES_FILTERS_ENABLED;
    }
    return true; // default on
}

/**
 * AJAX: Return content-area HTML
 */
function blainestwentysix_ajax_content_area() {

    if ( ! isset($_GET['blt_ajax']) || $_GET['blt_ajax'] !== '1' ) {
        return;
    }

    // Ensure WP does NOT load full templates
    status_header(200);

    // Output full content area (title + chips + grid)
    get_template_part( 'template-parts/loop/content-area' );

    wp_die();
}


/**
 * Modify main Blog query (category + tag faceted filters)
 */
function blt_facet_filter_query( $query ) {

    // Only modify front-end main queries
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Identify the Blog page (page_for_posts)
    $blog_id = get_option( 'page_for_posts' );

    // We only run this on:
    //  - the Blog page
    //  - any request that includes category/tag/search
    $is_blog_page = ( $blog_id && $query->get('page_id') == $blog_id );
    $has_filters  = isset($_GET['category']) || isset($_GET['tag']) || isset($_GET['s']);

    if ( ! $is_blog_page && ! $has_filters ) {
        return;
    }

    // Extract filters
    $category_slug = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
    $tag_slug      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

    // Build tax_query if needed
    $tax_query = [];

    if ( $category_slug ) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $category_slug,
        ];
    }

    if ( $tag_slug ) {
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => $tag_slug,
        ];
    }

    // Apply tax_query if both or either filter exists
    if ( ! empty( $tax_query ) ) {
        // Ensure multiple filters combine with AND
        $tax_query['relation'] = 'AND';
        $query->set( 'tax_query', $tax_query );
    }

    // Make sure we’re querying posts and not a static page
    $query->set( 'post_type', 'post' );

    // Let search run normally — WP handles ?s=
}
add_action( 'pre_get_posts', 'blt_facet_filter_query' );


/**
 * Helper: Get tags belonging to posts inside a specific category
 */
function blt_get_tags_for_category( $category_slug ) {

    if ( empty( $category_slug ) ) {
        return get_tags( [ 'hide_empty' => false ] );
    }

    // Find posts in this category
    $posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ]
        ]
    ]);

    if ( empty( $posts ) ) {
        return []; // no posts in category → no tags
    }

    // Get unique tags from these posts
    $tags = wp_get_object_terms( $posts, 'post_tag', [
        'orderby' => 'name',
        'order'   => 'ASC',
    ]);

    return $tags;
}


/**
 * Load an SVG from /assets/icons/ and optionally add CSS classes.
 */
function blaines_icon( $name, $classes = '' ) {

    $file = get_template_directory() . '/assets/icons/' . $name . '.svg';

    if ( ! file_exists( $file ) ) {
        return '';
    }

    $svg = file_get_contents( $file );

    // Inject classes into first <svg> tag
    if ( $classes ) {
        $svg = preg_replace(
            '/<svg\b([^>]*)>/',
            '<svg$1 class="' . esc_attr( $classes ) . '">',
            $svg,
            1
        );
    }

    return $svg;
}


/**
 * Returns a menu_icon value for a CPT using an SVG file or raw SVG code.
 *
 * @param string $svg  Path to SVG file OR raw SVG markup.
 * @return string
 */
function cpt_svg_icon( $svg ) {

    // If an SVG *file path* is passed
    if ( file_exists( $svg ) ) {
        $svg_code = file_get_contents( $svg );
    }
    // If inline SVG markup was passed
    else {
        $svg_code = $svg;
    }

    // Encode SVG for WordPress menu_icon
    return 'data:image/svg+xml;base64,' . base64_encode( $svg_code );
}

if ( ! function_exists( 'blt_nav_thumb_url' ) ) {
    /**
     * Return thumbnail URL for a post object (used in prev/next navigation cards).
     *
     * @param WP_Post|null $post_obj
     * @param string       $size     Image size to request (default: 'thumbnail')
     * @return string
     */
    function blt_nav_thumb_url( $post_obj, string $size = 'thumbnail' ) : string {

        if ( ! $post_obj instanceof WP_Post ) {
            return '';
        }

        $thumb_id = get_post_thumbnail_id( $post_obj->ID );
        if ( ! $thumb_id ) {
            return '';
        }

        $src = wp_get_attachment_image_src( $thumb_id, $size );

        return ( is_array( $src ) && ! empty( $src[0] ) )
            ? $src[0]
            : '';
    }
}

if ( ! function_exists( 'blt_nav_thumb_img' ) ) {
    /**
     * Return responsive <img> HTML for a post thumbnail.
     *
     * @param WP_Post|null $post_obj
     * @param string       $size
     * @param array        $attr
     * @return string
     */
    function blt_nav_thumb_img( $post_obj, string $size = 'thumbnail', array $attr = [] ) : string {
        if ( ! $post_obj instanceof WP_Post ) {
            return '';
        }

        $thumb_id = get_post_thumbnail_id( $post_obj->ID );
        if ( ! $thumb_id ) {
            return '';
        }

        // Merge defaults with caller attributes.
        $defaults = [
            'class'   => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]',
            'alt'     => '',          // Decorative: title is adjacent text link.
            'loading' => 'lazy',
        ];

        // If caller provides class, append (don’t overwrite).
        if ( isset( $attr['class'] ) ) {
            $defaults['class'] = trim( $defaults['class'] . ' ' . $attr['class'] );
            unset( $attr['class'] );
        }

        $final_attr = array_merge( $defaults, $attr );

        return wp_get_attachment_image( $thumb_id, $size, false, $final_attr ) ?: '';
    }
}

/**
 * Clear trending sidebar transient cache when posts change.
 */
function blt_clear_sidebar_trending_cache() {

    global $wpdb;

    $transients = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_blt_sidebar_trending_%'"
    );

    foreach ( $transients as $transient ) {
        $key = str_replace( '_transient_', '', $transient );
        delete_transient( $key );
    }
}

add_action( 'save_post_post', 'blt_clear_sidebar_trending_cache' );
add_action( 'deleted_post',   'blt_clear_sidebar_trending_cache' );
add_action( 'trashed_post',   'blt_clear_sidebar_trending_cache' );

/**
 * Clear sidebar category transient when categories change.
 */
function blt_clear_sidebar_category_cache() {

    global $wpdb;

    $transients = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_blt_sidebar_categories_%'"
    );

    foreach ( $transients as $transient ) {
        $key = str_replace( '_transient_', '', $transient );
        delete_transient( $key );
    }
}

add_action( 'created_category', 'blt_clear_sidebar_category_cache' );
add_action( 'edited_category',  'blt_clear_sidebar_category_cache' );
add_action( 'delete_category',  'blt_clear_sidebar_category_cache' );
add_action( 'save_post',        'blt_clear_sidebar_category_cache' );
