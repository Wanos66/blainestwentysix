<?php
/**
 * WebSite schema.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function blaines_schema_website(): ?array {

    $site_url  = home_url( '/' );
    $site_name = get_bloginfo( 'name' );

    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        '@id'      => trailingslashit( $site_url ) . '#website',
        'url'      => $site_url,
        'name'     => $site_name,

        // Tie the WebSite entity back to the Organization we output in 45.2.
        'publisher' => [
            '@id' => trailingslashit( $site_url ) . '#organization',
        ],
    ];

    /**
     * If (later) you add a real front-end search page, we can add SearchAction here.
     * For now, keep it clean and truthful.
     */
    return apply_filters( 'blaines_schema_website', $data );
}

