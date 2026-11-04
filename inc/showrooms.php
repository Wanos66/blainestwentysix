<?php
/**
 * Shared Blaines showroom information.
 *
 * Individual showroom pages are the authoritative
 * source for contact details.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieve the details of a Blaines showroom.
 *
 * @param string $slug Showroom identifier.
 * @return array|null Showroom details or null.
 */
function blaines_get_showroom_details( string $slug ): ?array {

    $slug = sanitize_title( $slug );

    // Only recognised Blaines showrooms.
    $valid_showrooms = array(
        'swaffham',
        'dereham',
        'watton',
    );

    if ( ! in_array( $slug, $valid_showrooms, true ) ) {
        return null;
    }

    // Locate the existing individual showroom page.
    $page = get_page_by_path( 'showrooms/' . $slug );

    if ( ! $page instanceof WP_Post ) {
        return null;
    }

    $page_id = (int) $page->ID;

    if ( ! function_exists( 'get_field' ) ) {
        return null;
    }

    $opening_hours = get_field( 'opening_hours', $page_id );

    return array(
        'id'              => $page_id,
        'slug'            => $slug,
        'name'            => get_the_title( $page_id ),
        'url'             => get_permalink( $page_id ),
        'address'         => (string) get_field( 'address', $page_id ),
        'phone'           => (string) get_field( 'phone', $page_id ),
        'email'           => (string) get_field( 'email', $page_id ),
        'opening_hours'   => is_array( $opening_hours ) ? $opening_hours : array(),
        'google_maps_url' => (string) get_field( 'google_maps_url', $page_id ),
    );
}

