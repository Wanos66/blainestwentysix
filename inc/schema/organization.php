<?php
/**
 * Organization schema.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function blaines_schema_organization(): ?array {

    $site_url  = home_url( '/' );
    $site_name = get_bloginfo( 'name' );

    // Get logo URL from custom logo.
    $logo_url = '';
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( $logo ) {
            $logo_url = $logo[0];
        }
    }

    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        '@id'      => trailingslashit( $site_url ) . '#organization',
        'url'      => $site_url,
        'name'     => $site_name,
    ];

    if ( $logo_url ) {
        $data['logo'] = [
            '@type' => 'ImageObject',
            '@id'   => trailingslashit( $site_url ) . '#logo',
            'url'   => $logo_url,
        ];
    }

    /**
     * Allow extension (e.g. sameAs social links later).
     */
    return apply_filters( 'blaines_schema_organization', $data );
}

