<?php
/**
 * Article schema.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function blaines_schema_article(): ?array {

    if ( ! is_singular() ) {
        return null;
    }

    global $post;

    if ( ! $post instanceof WP_Post ) {
        return null;
    }

    $post_type = get_post_type( $post );

    // Limit to content types we want indexed as articles.
    $allowed = [ 'post', 'project', 'kitchen' ];
    if ( ! in_array( $post_type, $allowed, true ) ) {
        return null;
    }

    $site_url  = home_url( '/' );
    $title     = get_the_title( $post );
    $permalink = get_permalink( $post );
    $published = get_the_date( DATE_W3C, $post );
    $modified  = get_the_modified_date( DATE_W3C, $post );

    $data = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        '@id'              => trailingslashit( $permalink ) . '#article',
        'headline'         => wp_strip_all_tags( $title ),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => $permalink,
        ],
        'datePublished'    => $published,
        'dateModified'     => $modified,
        'author'           => [
            '@type' => 'Organization',
            '@id'   => trailingslashit( $site_url ) . '#organization',
        ],
        'publisher'        => [
            '@type' => 'Organization',
            '@id'   => trailingslashit( $site_url ) . '#organization',
        ],
    ];

    // Featured image (if present).
    if ( has_post_thumbnail( $post ) ) {
        $image_id = get_post_thumbnail_id( $post );
        $image    = wp_get_attachment_image_src( $image_id, 'full' );

        if ( $image ) {
            $data['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $image[0],
                'width'  => (int) $image[1],
                'height' => (int) $image[2],
            ];
        }
    }

    return apply_filters( 'blaines_schema_article', $data, $post );
}

