<?php
/**
 * Transactional product promotion data.
 *
 * Registers the product-level ACF fields and exposes a normalised, importer-
 * friendly API for front-end templates.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register structured product promotion fields with ACF Pro.
 */
function blaines_register_transactional_promotion_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        [
            'key'                   => 'group_blaines_transactional_promotions',
            'title'                 => 'Transactional product promotions',
            'fields'                => [
                [
                    'key'          => 'field_blaines_product_promotions',
                    'label'        => 'Promotions',
                    'name'         => 'product_promotions',
                    'type'         => 'repeater',
                    'instructions' => 'Add current or scheduled product promotions. The first active row is highlighted beside the purchase controls; all active rows appear in Promotion details.',
                    'required'     => 0,
                    'layout'       => 'block',
                    'button_label' => 'Add promotion',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_blaines_promotion_enabled',
                            'label'         => 'Enabled',
                            'name'          => 'promotion_enabled',
                            'type'          => 'true_false',
                            'instructions'  => 'Temporarily disable this promotion without deleting it.',
                            'default_value' => 1,
                            'ui'            => 1,
                        ],
                        [
                            'key'          => 'field_blaines_promotion_title',
                            'label'        => 'Title',
                            'name'         => 'promotion_title',
                            'type'         => 'text',
                            'instructions' => 'A concise customer-facing title, for example “Claim £100 cashback”.',
                            'required'     => 1,
                            'maxlength'    => 90,
                            'wrapper'      => [
                                'width' => '60',
                            ],
                        ],
                        [
                            'key'           => 'field_blaines_promotion_style',
                            'label'         => 'Display style',
                            'name'          => 'promotion_style',
                            'type'          => 'select',
                            'choices'       => [
                                'blue'    => 'Blue',
                                'red'     => 'Red',
                                'green'   => 'Green',
                                'neutral' => 'Neutral',
                            ],
                            'default_value' => 'blue',
                            'return_format' => 'value',
                            'wrapper'       => [
                                'width' => '40',
                            ],
                        ],
                        [
                            'key'          => 'field_blaines_promotion_summary',
                            'label'        => 'Compact summary',
                            'name'         => 'promotion_summary',
                            'type'         => 'textarea',
                            'instructions' => 'One short sentence shown near the purchase panel. Do not repeat the full terms here.',
                            'required'     => 1,
                            'rows'         => 2,
                            'maxlength'    => 180,
                            'new_lines'    => '',
                        ],
                        [
                            'key'          => 'field_blaines_promotion_details',
                            'label'        => 'Full details',
                            'name'         => 'promotion_details',
                            'type'         => 'wysiwyg',
                            'instructions' => 'The fuller explanation shown inside the Promotions row. Include the qualifying purchase or claim process, but use the terms link for lengthy legal terms.',
                            'required'     => 0,
                            'tabs'         => 'visual',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                            'delay'        => 1,
                        ],
                        [
                            'key'            => 'field_blaines_promotion_start_date',
                            'label'          => 'Start date',
                            'name'           => 'promotion_start_date',
                            'type'           => 'date_picker',
                            'instructions'   => 'Leave blank to make the promotion available immediately.',
                            'display_format' => 'd/m/Y',
                            'return_format'  => 'Ymd',
                            'first_day'      => 1,
                            'wrapper'        => [
                                'width' => '50',
                            ],
                        ],
                        [
                            'key'            => 'field_blaines_promotion_end_date',
                            'label'          => 'End date',
                            'name'           => 'promotion_end_date',
                            'type'           => 'date_picker',
                            'instructions'   => 'The promotion remains visible through this date. Leave blank for no automatic expiry.',
                            'display_format' => 'd/m/Y',
                            'return_format'  => 'Ymd',
                            'first_day'      => 1,
                            'wrapper'        => [
                                'width' => '50',
                            ],
                        ],
                        [
                            'key'          => 'field_blaines_promotion_terms_url',
                            'label'        => 'Terms URL',
                            'name'         => 'promotion_terms_url',
                            'type'         => 'url',
                            'instructions' => 'Optional link to the manufacturer or campaign terms.',
                            'wrapper'      => [
                                'width' => '65',
                            ],
                        ],
                        [
                            'key'           => 'field_blaines_promotion_terms_label',
                            'label'         => 'Terms link label',
                            'name'          => 'promotion_terms_label',
                            'type'          => 'text',
                            'default_value' => 'View promotion terms',
                            'maxlength'     => 60,
                            'wrapper'       => [
                                'width' => '35',
                            ],
                        ],
                    ],
                ],
            ],
            'location'              => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'product',
                    ],
                ],
            ],
            'menu_order'            => 35,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
            'show_in_rest'          => 0,
        ]
    );
}
add_action( 'acf/init', 'blaines_register_transactional_promotion_fields' );

/**
 * Return the first available value from an ACF-shaped or normalised promotion row.
 *
 * @param array  $promotion     Promotion row.
 * @param string $acf_key       ACF field key.
 * @param string $normalised_key Normalised key.
 * @param mixed  $default       Fallback value.
 * @return mixed
 */
function blaines_get_transactional_promotion_value( array $promotion, $acf_key, $normalised_key, $default = '' ) {
    if ( array_key_exists( $acf_key, $promotion ) ) {
        return $promotion[ $acf_key ];
    }

    if ( array_key_exists( $normalised_key, $promotion ) ) {
        return $promotion[ $normalised_key ];
    }

    return $default;
}

/**
 * Normalise common boolean representations used by ACF and feed imports.
 *
 * @param mixed $value   Raw value.
 * @param bool  $default Default value when the input is ambiguous.
 * @return bool
 */
function blaines_normalise_transactional_promotion_boolean( $value, $default = true ) {
    if ( is_bool( $value ) ) {
        return $value;
    }

    if ( is_int( $value ) || is_float( $value ) ) {
        return 0 !== (int) $value;
    }

    if ( is_string( $value ) ) {
        $value = strtolower( trim( $value ) );

        if ( in_array( $value, [ '0', 'false', 'no', 'off', 'disabled' ], true ) ) {
            return false;
        }

        if ( in_array( $value, [ '1', 'true', 'yes', 'on', 'enabled' ], true ) ) {
            return true;
        }
    }

    return (bool) $default;
}

/**
 * Convert a supported promotion date value to ACF's Ymd format.
 *
 * @param mixed $value Raw date value.
 * @return string
 */
function blaines_normalise_transactional_promotion_date( $value ) {
    if ( ! is_scalar( $value ) ) {
        return '';
    }

    $value = trim( (string) $value );

    if ( '' === $value ) {
        return '';
    }

    $timezone = wp_timezone();
    $formats  = [ 'Ymd', 'Y-m-d', 'd/m/Y' ];

    foreach ( $formats as $format ) {
        $date = DateTimeImmutable::createFromFormat( '!' . $format, $value, $timezone );

        if ( $date instanceof DateTimeImmutable ) {
            $errors = DateTimeImmutable::getLastErrors();

            if ( false === $errors || ( 0 === $errors['warning_count'] && 0 === $errors['error_count'] ) ) {
                return $date->format( 'Ymd' );
            }
        }
    }

    return '';
}

/**
 * Confirm that a raw date is either blank or valid.
 *
 * @param mixed  $raw_value       Raw date value.
 * @param string $normalised_date Normalised Ymd value.
 * @return bool
 */
function blaines_transactional_promotion_date_is_valid( $raw_value, $normalised_date ) {
    if ( null === $raw_value ) {
        return true;
    }

    if ( ! is_scalar( $raw_value ) ) {
        return false;
    }

    return '' === trim( (string) $raw_value ) || '' !== $normalised_date;
}

/**
 * Normalise one promotion row.
 *
 * Supports both ACF repeater field names and the shorter normalised keys used by
 * the consolidated importer meta fallback.
 *
 * @param mixed $promotion Raw promotion row.
 * @param int   $index     Original row index.
 * @return array|null
 */
function blaines_normalise_transactional_promotion( $promotion, $index = 0 ) {
    if ( ! is_array( $promotion ) ) {
        return null;
    }

    $enabled_raw = blaines_get_transactional_promotion_value(
        $promotion,
        'promotion_enabled',
        'enabled',
        true
    );

    $enabled = blaines_normalise_transactional_promotion_boolean( $enabled_raw, true );

    $title = sanitize_text_field(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_title',
            'title',
            ''
        )
    );

    $summary = sanitize_textarea_field(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_summary',
            'summary',
            ''
        )
    );

    $details = wp_kses_post(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_details',
            'details',
            ''
        )
    );

    if ( '' === $title && '' === $summary && '' === trim( wp_strip_all_tags( $details ) ) ) {
        return null;
    }

    $style = sanitize_key(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_style',
            'style',
            'blue'
        )
    );

    if ( ! in_array( $style, [ 'blue', 'red', 'green', 'neutral' ], true ) ) {
        $style = 'blue';
    }

    $raw_start_date = blaines_get_transactional_promotion_value(
        $promotion,
        'promotion_start_date',
        'start_date',
        ''
    );

    $raw_end_date = blaines_get_transactional_promotion_value(
        $promotion,
        'promotion_end_date',
        'end_date',
        ''
    );

    $start_date = blaines_normalise_transactional_promotion_date( $raw_start_date );
    $end_date   = blaines_normalise_transactional_promotion_date( $raw_end_date );

    $dates_ok = blaines_transactional_promotion_date_is_valid( $raw_start_date, $start_date )
        && blaines_transactional_promotion_date_is_valid( $raw_end_date, $end_date )
        && ! ( '' !== $start_date && '' !== $end_date && $end_date < $start_date );

    $terms_url = esc_url_raw(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_terms_url',
            'terms_url',
            ''
        )
    );

    $terms_label = sanitize_text_field(
        (string) blaines_get_transactional_promotion_value(
            $promotion,
            'promotion_terms_label',
            'terms_label',
            ''
        )
    );

    if ( '' === $terms_label ) {
        $terms_label = 'View promotion terms';
    }

    return [
        'index'       => absint( $index ),
        'enabled'     => $enabled,
        'title'       => $title,
        'summary'     => $summary,
        'details'     => $details,
        'style'       => $style,
        'start_date'  => $start_date,
        'end_date'    => $end_date,
        'dates_valid' => $dates_ok,
        'terms_url'   => $terms_url,
        'terms_label' => $terms_label,
    ];
}

/**
 * Determine whether a normalised promotion is active today.
 *
 * @param array       $promotion Normalised promotion.
 * @param string|null $today     Optional Ymd test date.
 * @return bool
 */
function blaines_is_transactional_promotion_active( array $promotion, $today = null ) {
    if ( empty( $promotion['enabled'] ) || empty( $promotion['dates_valid'] ) ) {
        return false;
    }

    $today = is_string( $today ) && preg_match( '/^\d{8}$/', $today )
        ? $today
        : current_datetime()->format( 'Ymd' );

    if ( ! empty( $promotion['start_date'] ) && $promotion['start_date'] > $today ) {
        return false;
    }

    if ( ! empty( $promotion['end_date'] ) && $promotion['end_date'] < $today ) {
        return false;
    }

    return true;
}

/**
 * Retrieve normalised product promotions.
 *
 * ACF is the editorial source. The consolidated fallback meta key allows a
 * future importer to provide complete promotion rows without writing ACF
 * repeater internals directly. Both ACF-shaped and normalised row keys are
 * accepted by the fallback.
 *
 * @param int  $product_id Product ID.
 * @param bool $active_only Return only currently active rows.
 * @return array
 */
function blaines_get_product_promotions( $product_id, $active_only = true ) {
    static $request_cache = [];

    $product_id = absint( $product_id );

    if ( ! $product_id ) {
        return [];
    }

    /*
     * Central Promotions Manager integration.
     *
     * When the Blaines Promotions plugin has active matching promotions, those
     * become the authoritative front-end source. During migration only, the
     * existing product-level ACF repeater remains as a fallback for products
     * that have not yet been moved into the central manager.
     */
    if ( function_exists( 'blaines_promotions_get_product_promotions' ) ) {
        $managed_promotions = blaines_promotions_get_product_promotions(
            $product_id,
            (bool) $active_only
        );

        if ( ! empty( $managed_promotions ) ) {
            return array_values( $managed_promotions );
        }

        $allow_legacy_fallback = apply_filters(
            'blaines_promotions_allow_legacy_product_fallback',
            true,
            $product_id,
            (bool) $active_only
        );

        if ( ! $allow_legacy_fallback ) {
            return [];
        }
    }

    if ( ! array_key_exists( $product_id, $request_cache ) ) {
        $raw_promotions = function_exists( 'get_field' )
            ? get_field( 'product_promotions', $product_id )
            : null;

        if ( ! is_array( $raw_promotions ) || empty( $raw_promotions ) ) {
            $raw_promotions = get_post_meta( $product_id, '_blaines_product_promotions', true );
        }

        if ( ! is_array( $raw_promotions ) ) {
            $request_cache[ $product_id ] = [];
        } else {
            $normalised_promotions = [];

            foreach ( $raw_promotions as $index => $raw_promotion ) {
                $promotion = blaines_normalise_transactional_promotion( $raw_promotion, $index );

                if ( null === $promotion ) {
                    continue;
                }

                $promotion['active'] = blaines_is_transactional_promotion_active( $promotion );
                $normalised_promotions[] = $promotion;
            }

            $request_cache[ $product_id ] = $normalised_promotions;
        }
    }

    $promotions = $request_cache[ $product_id ];

    if ( ! $active_only ) {
        return $promotions;
    }

    return array_values(
        array_filter(
            $promotions,
            static function ( $promotion ) {
                return ! empty( $promotion['active'] );
            }
        )
    );
}

/**
 * Return the first active promotion for the compact purchase-panel highlight.
 *
 * @param int $product_id Product ID.
 * @return array|null
 */
function blaines_get_primary_product_promotion( $product_id ) {
    $promotions = blaines_get_product_promotions( $product_id, true );

    return $promotions[0] ?? null;
}

/**
 * Format a promotion date using the site's WordPress date format.
 *
 * @param string $date Ymd date.
 * @return string
 */
function blaines_format_transactional_promotion_date( $date ) {
    $date = blaines_normalise_transactional_promotion_date( $date );

    if ( '' === $date ) {
        return '';
    }

    $parsed = DateTimeImmutable::createFromFormat( '!Ymd', $date, wp_timezone() );

    if ( ! $parsed instanceof DateTimeImmutable ) {
        return '';
    }

    return wp_date( get_option( 'date_format' ), $parsed->getTimestamp(), wp_timezone() );
}

/**
 * Build concise date context for a promotion.
 *
 * @param array $promotion Normalised promotion.
 * @return string
 */
function blaines_get_transactional_promotion_period_label( array $promotion ) {
    $start = ! empty( $promotion['start_date'] )
        ? blaines_format_transactional_promotion_date( $promotion['start_date'] )
        : '';

    $end = ! empty( $promotion['end_date'] )
        ? blaines_format_transactional_promotion_date( $promotion['end_date'] )
        : '';

    $today = current_datetime()->format( 'Ymd' );

    if ( $start && ! empty( $promotion['start_date'] ) && $promotion['start_date'] > $today ) {
        return sprintf( 'Starts %s', $start );
    }

    if ( $end ) {
        return sprintf( 'Ends %s', $end );
    }

    return '';
}

/**
 * Tailwind classes for promotion panels.
 *
 * @param string $style Promotion style.
 * @return array
 */
function blaines_get_transactional_promotion_classes( $style ) {
    $styles = [
        'red' => [
            'panel' => 'border-red-200 bg-red-50',
            'label' => 'text-red-700',
            'title' => 'text-red-950',
            'text'  => 'text-red-900',
            'link'  => 'text-red-950',
        ],
        'green' => [
            'panel' => 'border-green-200 bg-green-50',
            'label' => 'text-green-700',
            'title' => 'text-green-950',
            'text'  => 'text-green-900',
            'link'  => 'text-green-950',
        ],
        'neutral' => [
            'panel' => 'border-neutral-300 bg-neutral-50',
            'label' => 'text-neutral-600',
            'title' => 'text-neutral-950',
            'text'  => 'text-neutral-700',
            'link'  => 'text-neutral-950',
        ],
        'blue' => [
            'panel' => 'border-blue-200 bg-blue-50',
            'label' => 'text-blue-700',
            'title' => 'text-blue-950',
            'text'  => 'text-blue-900',
            'link'  => 'text-blue-950',
        ],
        'black-friday' => [
            'panel' => 'border-amber-300 bg-neutral-950',
            'label' => 'text-amber-300',
            'title' => 'text-white',
            'text'  => 'text-neutral-200',
            'link'  => 'text-white',
        ],
    ];

    $style = sanitize_key( (string) $style );

    return $styles[ $style ] ?? $styles['blue'];
}
