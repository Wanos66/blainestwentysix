<?php
/**
 * LocalBusiness schema for showroom pages.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function blaines_schema_local_business(): array {

    if ( ! is_page() ) {
        return [];
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return [];
    }

    $template = get_page_template_slug( $post_id );
    if ( $template !== 'page-showroom.php' && $template !== 'templates/page-showroom.php' ) {
        return [];
    }

    if ( ! function_exists( 'get_field' ) ) {
        return [];
    }

    $name            = get_the_title( $post_id );
    $url             = get_permalink( $post_id );
    $description     = get_the_excerpt( $post_id );
    $telephone       = trim( (string) get_field( 'phone', $post_id ) );
    $email           = trim( (string) get_field( 'email', $post_id ) );
    $address_raw     = trim( (string) get_field( 'address', $post_id ) );
    $google_maps_url = trim( (string) get_field( 'google_maps_url', $post_id ) );
    $hero_image_id   = (int) get_post_thumbnail_id( $post_id );
    $storefront_id   = (int) get_field( 'storefront_image', $post_id );
    $opening_hours   = get_field( 'opening_hours', $post_id );

    $image_url = '';
    if ( $storefront_id ) {
        $image_url = wp_get_attachment_image_url( $storefront_id, 'full' ) ?: '';
    } elseif ( $hero_image_id ) {
        $image_url = wp_get_attachment_image_url( $hero_image_id, 'full' ) ?: '';
    }

    // Address parsing
    $address_lines = array_values(
        array_filter(
            array_map(
                'trim',
                preg_split( '/\r\n|\r|\n/', $address_raw ?: '' )
            )
        )
    );

    $street_address   = $address_lines[0] ?? '';
    $address_locality = $address_lines[1] ?? '';
    $address_region   = '';
    $postal_code      = '';

    if ( isset( $address_lines[2], $address_lines[3] ) ) {
        $address_region = $address_lines[2];
        $postal_code    = $address_lines[3];
    } elseif ( isset( $address_lines[2] ) ) {
        if ( preg_match( '/^(.*?)([A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2})$/i', $address_lines[2], $m ) ) {
            $address_region = trim( $m[1], " ," );
            $postal_code    = trim( $m[2] );
        } else {
            $postal_code = trim( $address_lines[2] );
        }
    }

    // Opening hours mapping
    $day_map = [
        'monday'    => 'https://schema.org/Monday',
        'tuesday'   => 'https://schema.org/Tuesday',
        'wednesday' => 'https://schema.org/Wednesday',
        'thursday'  => 'https://schema.org/Thursday',
        'friday'    => 'https://schema.org/Friday',
        'saturday'  => 'https://schema.org/Saturday',
        'sunday'    => 'https://schema.org/Sunday',
    ];

    $opening_hours_spec = [];

    if ( is_array( $opening_hours ) ) {
        foreach ( $opening_hours as $row ) {
            $day_label = isset( $row['day'] ) ? trim( (string) $row['day'] ) : '';
            $hours     = isset( $row['hours'] ) ? trim( (string) $row['hours'] ) : '';

            if ( $day_label === '' || $hours === '' ) {
                continue;
            }

            $days = [];

            if ( preg_match( '/^([A-Za-z]+)\s*[–-]\s*([A-Za-z]+)$/', $day_label, $m ) ) {
                $keys  = array_keys( $day_map );
                $start = array_search( strtolower( $m[1] ), $keys, true );
                $end   = array_search( strtolower( $m[2] ), $keys, true );

                if ( $start !== false && $end !== false && $start <= $end ) {
                    for ( $i = $start; $i <= $end; $i++ ) {
                        $days[] = $day_map[ $keys[ $i ] ];
                    }
                }
            } else {
                $single = strtolower( $day_label );
                if ( isset( $day_map[ $single ] ) ) {
                    $days[] = $day_map[ $single ];
                }
            }

            $opens  = '';
            $closes = '';

            if ( preg_match( '/(\d{1,2})[.:](\d{2})\s*(am|pm)\s*[–-]\s*(\d{1,2})[.:](\d{2})\s*(am|pm)/i', $hours, $m ) ) {
                $open_hour  = (int) $m[1];
                $open_min   = $m[2];
                $open_ampm  = strtolower( $m[3] );
                $close_hour = (int) $m[4];
                $close_min  = $m[5];
                $close_ampm = strtolower( $m[6] );

                if ( $open_ampm === 'pm' && $open_hour !== 12 ) $open_hour += 12;
                if ( $open_ampm === 'am' && $open_hour === 12 ) $open_hour = 0;
                if ( $close_ampm === 'pm' && $close_hour !== 12 ) $close_hour += 12;
                if ( $close_ampm === 'am' && $close_hour === 12 ) $close_hour = 0;

                $opens  = sprintf( '%02d:%s', $open_hour, $open_min );
                $closes = sprintf( '%02d:%s', $close_hour, $close_min );
            }

            if ( ! empty( $days ) && $opens !== '' && $closes !== '' ) {
                $opening_hours_spec[] = [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => count( $days ) === 1 ? $days[0] : $days,
                    'opens'     => $opens,
                    'closes'    => $closes,
                ];
            }
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'LocalBusiness',
        'name'     => $name,
        'url'      => $url,
    ];

    if ( $description ) {
        $schema['description'] = wp_strip_all_tags( $description );
    }

    if ( $image_url ) {
        $schema['image'] = $image_url;
    }

    if ( $telephone ) {
        $schema['telephone'] = $telephone;
    }

    if ( $email ) {
        $schema['email'] = $email;
    }

    if ( $google_maps_url ) {
        $schema['hasMap'] = $google_maps_url;
    }

    if ( $street_address || $address_locality || $postal_code ) {
        $schema['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $street_address,
            'addressLocality' => $address_locality,
            'addressRegion'   => $address_region,
            'postalCode'      => $postal_code,
            'addressCountry'  => 'GB',
        ];
    }

    if ( ! empty( $opening_hours_spec ) ) {
        $schema['openingHoursSpecification'] = $opening_hours_spec;
    }

    return $schema;
}
