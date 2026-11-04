<?php
/**
 * BreadcrumbList schema (theme-owned).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function blaines_schema_breadcrumbs(): ?array {

    if ( is_front_page() ) return null;

    $items = blaines_schema_build_breadcrumb_items();
    if ( count( $items ) < 2 ) return null;

    $list_items = [];
    $pos = 1;

    foreach ( $items as $item ) {
        $list_items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => $item['name'],
            'item'     => $item['url'],
        ];
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        '@id'             => trailingslashit( home_url( '/' ) ) . '#breadcrumbs',
        'itemListElement' => $list_items,
    ];
}

/**
 * Minimal breadcrumb items builder.
 * Adjust CPT archive links/slugs as needed.
 */
function blaines_schema_build_breadcrumb_items(): array {

    $site_url  = home_url( '/' );
    $items = [
        [
            'name' => 'Home',
            'url'  => $site_url,
        ],
    ];

    // Blog index page (if set) isHome() returns true for posts index.
    if ( is_home() ) {
        $items[] = [
            'name' => get_the_title( get_option( 'page_for_posts' ) ) ?: 'Blog',
            'url'  => get_permalink( get_option( 'page_for_posts' ) ) ?: $site_url,
        ];
        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    // Search / 404 can be included if you want; often optional.
    if ( is_search() ) {
        $items[] = [
            'name' => 'Search results',
            'url'  => get_search_link(),
        ];
        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    if ( is_404() ) {
        $items[] = [
            'name' => 'Not found',
            'url'  => $site_url,
        ];
        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    // Taxonomy archives.
    if ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            $tax = get_taxonomy( $term->taxonomy );
            if ( $tax && ! empty( $tax->object_type ) ) {
                $post_type = $tax->object_type[0];
                $pt_obj = get_post_type_object( $post_type );
                if ( $pt_obj && ! empty( $pt_obj->has_archive ) ) {
                    $items[] = [
                        'name' => $pt_obj->labels->name,
                        'url'  => get_post_type_archive_link( $post_type ) ?: $site_url,
                    ];
                }
            }

            $items[] = [
                'name' => single_term_title( '', false ),
                'url'  => get_term_link( $term ),
            ];
        }

        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    // Post type archives.
    if ( is_post_type_archive() ) {
        $pt = get_query_var( 'post_type' );
        $pt = is_array( $pt ) ? reset( $pt ) : $pt;

        $pt_obj = $pt ? get_post_type_object( $pt ) : null;
        if ( $pt_obj ) {
            $items[] = [
                'name' => $pt_obj->labels->name,
                'url'  => get_post_type_archive_link( $pt ) ?: $site_url,
            ];
        }

        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    // Singular.
    if ( is_singular() ) {
        $post_type = get_post_type();
        $pt_obj = $post_type ? get_post_type_object( $post_type ) : null;

        // If CPT has an archive, include it.
        if ( $pt_obj && ! empty( $pt_obj->has_archive ) ) {
            $items[] = [
                'name' => $pt_obj->labels->name,
                'url'  => get_post_type_archive_link( $post_type ) ?: $site_url,
            ];
        } elseif ( is_singular( 'post' ) ) {
            // Blog posts: include blog index if configured.
            $blog_id = (int) get_option( 'page_for_posts' );
            if ( $blog_id ) {
                $items[] = [
                    'name' => get_the_title( $blog_id ) ?: 'Blog',
                    'url'  => get_permalink( $blog_id ),
                ];
            }
        }

        $items[] = [
            'name' => get_the_title() ?: 'Current page',
            'url'  => get_permalink() ?: $site_url,
        ];

        return blaines_schema_sanitise_breadcrumb_items( $items );
    }

    // Generic fallback.
    $items[] = [
        'name' => wp_get_document_title(),
        'url'  => $site_url,
    ];

    return blaines_schema_sanitise_breadcrumb_items( $items );
}

function blaines_schema_sanitise_breadcrumb_items( array $items ): array {
    $out = [];

    foreach ( $items as $item ) {
        $name = isset( $item['name'] ) ? trim( wp_strip_all_tags( (string) $item['name'] ) ) : '';
        $url  = isset( $item['url'] ) ? esc_url_raw( (string) $item['url'] ) : '';

        if ( $name === '' || $url === '' ) continue;

        $out[] = [ 'name' => $name, 'url' => $url ];
    }

    // Deduplicate consecutive duplicates (common with fallbacks).
    $deduped = [];
    foreach ( $out as $i => $row ) {
        if ( $i > 0 && $out[$i-1]['url'] === $row['url'] ) continue;
        $deduped[] = $row;
    }

    return $deduped;
}
