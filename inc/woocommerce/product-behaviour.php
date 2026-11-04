<?php
/**
 * WooCommerce product behaviour helpers.
 *
 * Defines which WooCommerce products are transactional and which remain enquiry-led.
 *
 * @package Blaines
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get a product ID from a product object, ID, or global product.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return int
 */
function blaines_get_product_id( $product = null ): int {
    if ( ! function_exists( 'wc_get_product' ) ) {
        return 0;
    }

    if ( ! $product ) {
        global $product;
    }

    if ( $product instanceof WC_Product ) {
        return (int) $product->get_id();
    }

    return absint( $product );
}

/**
 * Check whether a product has one or more WooCommerce product category slugs.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @param array              $slugs   Product category slugs.
 * @return bool
 */
function blaines_product_has_category_slug( $product, array $slugs ): bool {
    $product_id = blaines_get_product_id( $product );

    if ( ! $product_id ) {
        return false;
    }

    return has_term( $slugs, 'product_cat', $product_id );
}

/**
 * Check whether a product is assigned to a category slug or any descendant of it.
 *
 * Example:
 * Parent: electrical
 * Product assigned to: Electrical > Laundry > Washing Machines
 * Result: true
 *
 * @param WC_Product|int|null $product     Product object or product ID.
 * @param string              $parent_slug Parent product category slug.
 * @return bool
 */
function blaines_product_is_in_category_tree( $product, string $parent_slug ): bool {
    $product_id = blaines_get_product_id( $product );

    if ( ! $product_id ) {
        return false;
    }

    $parent_term = get_term_by( 'slug', $parent_slug, 'product_cat' );

    if ( ! $parent_term instanceof WP_Term ) {
        return false;
    }

    $product_terms = get_the_terms( $product_id, 'product_cat' );

    if ( empty( $product_terms ) || is_wp_error( $product_terms ) ) {
        return false;
    }

    foreach ( $product_terms as $term ) {
        if ( ! $term instanceof WP_Term ) {
            continue;
        }

        if ( (int) $term->term_id === (int) $parent_term->term_id ) {
            return true;
        }

        $ancestors = get_ancestors( $term->term_id, 'product_cat' );

        if ( in_array( (int) $parent_term->term_id, array_map( 'intval', $ancestors ), true ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Determine whether a WooCommerce product belongs to the Clearance family.
 *
 * These products remain enquiry-led and should not show transactional purchasing UI.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return bool
 */
function blaines_is_clearance_product( $product = null ): bool {
    $clearance_categories = [
        'clearance',
        'electrical-clearance',
        'kitchen-clearance',
        'bathroom-clearance',
    ];

    return blaines_product_has_category_slug( $product, $clearance_categories );
}

/**
 * Determine whether a product is an ex-display kitchen or bathroom product.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return bool
 */
function blaines_is_display_product( $product = null ): bool {
    $display_categories = [
        'kitchen-clearance',
        'bathroom-clearance',
    ];

    return blaines_product_has_category_slug( $product, $display_categories );
}

/**
 * Determine whether a WooCommerce product should behave as transactional.
 *
 * Transactional products currently include products assigned to Electrical
 * or any descendant of Electrical.
 *
 * Clearance and display products are explicitly protected and remain enquiry-led.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return bool
 */
function blaines_is_transactional_product( $product = null ): bool {
    /*
     * Protected families always win.
     */
    if ( blaines_is_clearance_product( $product ) || blaines_is_display_product( $product ) ) {
        return false;
    }

    return blaines_product_is_in_category_tree( $product, 'electrical' );
}

/**
 * Determine whether a WooCommerce product should remain enquiry-led.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return bool
 */
function blaines_is_enquiry_product( $product = null ): bool {
    $product_id = blaines_get_product_id( $product );

    if ( ! $product_id ) {
        return false;
    }

    return ! blaines_is_transactional_product( $product );
}

/**
 * Get ACF key features for a WooCommerce product.
 *
 * Expected ACF structure:
 * Repeater field: features
 * Sub field: feature_text
 *
 * @param int $product_id Product ID.
 * @return array
 */
function blaines_get_product_key_features( int $product_id ): array {
    if ( ! function_exists( 'get_field' ) || ! $product_id ) {
        return [];
    }

    $features = get_field( 'key_features', $product_id );

    if ( empty( $features ) || ! is_array( $features ) ) {
        return [];
    }

    $items = [];

    foreach ( $features as $feature ) {
        $text = $feature['feature'] ?? '';

        if ( $text ) {
            $items[] = wp_strip_all_tags( $text );
        }
    }

    return array_values( array_filter( $items ) );
}

/**
 * Get product energy rating image ID.
 *
 * @param int $product_id Product ID.
 * @return int
 */
function blaines_get_product_energy_rating_image_id( int $product_id ): int {
    if ( ! $product_id || ! function_exists( 'get_field' ) ) {
        return 0;
    }

    return absint( get_field( 'product_energy_rating_image', $product_id ) );
}


/**
 * Normalise a Media Library attachment, ACF file value, or external URL.
 *
 * @param mixed $value Resource value.
 * @return array{url:string,attachment_id:int,mime_type:string,is_image:bool,is_pdf:bool}
 */
function blaines_normalise_product_document_resource( $value ): array {
    $attachment_id = 0;
    $url           = '';

    if ( is_array( $value ) ) {
        $attachment_id = absint( $value['ID'] ?? $value['id'] ?? 0 );
        $url           = isset( $value['url'] ) ? esc_url_raw( (string) $value['url'] ) : '';
    } elseif ( is_numeric( $value ) ) {
        $attachment_id = absint( $value );
    } elseif ( is_string( $value ) ) {
        $url = esc_url_raw( trim( $value ) );
    }

    if ( $attachment_id && '' === $url ) {
        $attachment_url = wp_get_attachment_url( $attachment_id );
        $url            = $attachment_url ? esc_url_raw( $attachment_url ) : '';
    }

    $mime_type = $attachment_id ? (string) get_post_mime_type( $attachment_id ) : '';

    if ( '' === $mime_type && '' !== $url ) {
        $path      = (string) wp_parse_url( $url, PHP_URL_PATH );
        $file_type = wp_check_filetype( $path );
        $mime_type = isset( $file_type['type'] ) ? (string) $file_type['type'] : '';
    }

    return [
        'url'           => $url,
        'attachment_id' => $attachment_id,
        'mime_type'     => $mime_type,
        'is_image'      => 0 === strpos( $mime_type, 'image/' ),
        'is_pdf'        => 'application/pdf' === $mime_type,
    ];
}

/**
 * Resolve a product document from an uploaded ACF file, an external ACF URL,
 * or an importer-friendly fallback meta key.
 *
 * @param int    $product_id        Product ID.
 * @param string $file_field        ACF file field name.
 * @param string $url_field         ACF URL field name.
 * @param string $fallback_meta_key Fallback post meta key.
 * @return array{url:string,attachment_id:int,mime_type:string,is_image:bool,is_pdf:bool}
 */
function blaines_get_product_document_resource(
    int $product_id,
    string $file_field,
    string $url_field,
    string $fallback_meta_key
): array {
    $empty = [
        'url'           => '',
        'attachment_id' => 0,
        'mime_type'     => '',
        'is_image'      => false,
        'is_pdf'        => false,
    ];

    if ( ! $product_id ) {
        return $empty;
    }

    if ( function_exists( 'get_field' ) ) {
        $file_resource = blaines_normalise_product_document_resource(
            get_field( $file_field, $product_id )
        );

        if ( '' !== $file_resource['url'] ) {
            return $file_resource;
        }

        $url_resource = blaines_normalise_product_document_resource(
            get_field( $url_field, $product_id )
        );

        if ( '' !== $url_resource['url'] ) {
            return $url_resource;
        }
    }

    $fallback_resource = blaines_normalise_product_document_resource(
        get_post_meta( $product_id, $fallback_meta_key, true )
    );

    return '' !== $fallback_resource['url'] ? $fallback_resource : $empty;
}

/**
 * Get the full model-specific energy label resource.
 *
 * @param int $product_id Product ID.
 * @return array{url:string,attachment_id:int,mime_type:string,is_image:bool,is_pdf:bool}
 */
function blaines_get_product_full_energy_label_resource( int $product_id ): array {
    return blaines_get_product_document_resource(
        $product_id,
        'product_full_energy_label_file',
        'product_full_energy_label_url',
        '_blaines_full_energy_label_url'
    );
}

/**
 * Get the product information sheet resource.
 *
 * @param int $product_id Product ID.
 * @return array{url:string,attachment_id:int,mime_type:string,is_image:bool,is_pdf:bool}
 */
function blaines_get_product_information_sheet_resource( int $product_id ): array {
    return blaines_get_product_document_resource(
        $product_id,
        'product_information_sheet_file',
        'product_information_sheet_url',
        '_blaines_product_information_sheet_url'
    );
}

/**
 * Get the full model-specific energy label URL.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function blaines_get_product_full_energy_label_url( int $product_id ): string {
    $resource = blaines_get_product_full_energy_label_resource( $product_id );

    return $resource['url'];
}

/**
 * Get the product information sheet URL.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function blaines_get_product_information_sheet_url( int $product_id ): string {
    $resource = blaines_get_product_information_sheet_resource( $product_id );

    return $resource['url'];
}

/**
 * Get promotional badges for a WooCommerce product.
 *
 * Expected ACF structure:
 * Repeater field: product_badges
 * Sub fields: badge_text, badge_style
 *
 * @param int $product_id Product ID.
 * @return array
 */
function blaines_get_product_badges( int $product_id ): array {
    if ( ! $product_id || ! function_exists( 'get_field' ) ) {
        return [];
    }

    $badges = get_field( 'product_badges', $product_id );

    if ( empty( $badges ) || ! is_array( $badges ) ) {
        return [];
    }

    $items = [];

    foreach ( $badges as $badge ) {
        $text  = trim( (string) ( $badge['badge_text'] ?? '' ) );
        $style = trim( (string) ( $badge['badge_style'] ?? 'neutral' ) );

        if ( ! $text ) {
            continue;
        }

        $allowed_styles = [
            'blue',
            'green',
            'red',
            'purple',
            'neutral',
        ];

        if ( ! in_array( $style, $allowed_styles, true ) ) {
            $style = 'neutral';
        }

        $items[] = [
            'text'  => $text,
            'style' => $style,
        ];
    }

    return $items;
}

/**
 * Return badge classes for product promotional badges.
 *
 * @param string $style Badge style.
 * @return string
 */
function blaines_get_product_badge_classes( string $style ): string {
    $classes = [
        'blue'    => 'bg-blue-100 text-blue-900',
        'green'   => 'bg-green-100 text-green-900',
        'red'     => 'bg-red-100 text-red-900',
        'purple'  => 'bg-purple-100 text-purple-900',
        'neutral' => 'bg-neutral-100 text-neutral-800',
    ];

    return $classes[ $style ] ?? $classes['neutral'];
}

/**
 * Check whether a product SKU starts with one of the supplied prefixes.
 *
 * @param WC_Product|int|null $product  Product object or product ID.
 * @param array<int, string>  $prefixes SKU prefixes.
 * @return bool
 */
function blaines_product_sku_starts_with_prefix( $product, array $prefixes ): bool {
    $product_id = blaines_get_product_id( $product );

    if ( ! $product_id ) {
        return false;
    }

    $product_obj = wc_get_product( $product_id );

    if ( ! $product_obj ) {
        return false;
    }

    $sku = strtoupper( trim( (string) $product_obj->get_sku() ) );

    if ( '' === $sku ) {
        return false;
    }

    foreach ( $prefixes as $prefix ) {
        $prefix = strtoupper( trim( (string) $prefix ) );

        if ( '' !== $prefix && 0 === strpos( $sku, $prefix ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Determine whether a product should use the Bosch / NEFF / Siemens warranty fallback.
 *
 * These products are controlled by the Blaines local warehouse feed for stock/price
 * rather than the Euronics feed, so warranty needs a manual/fallback path.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return bool
 */
function blaines_product_uses_bosch_neff_siemens_warranty_fallback( $product = null ): bool {
    return blaines_product_sku_starts_with_prefix(
        $product,
        [
            'BOS',
            'NEF',
            'SIE',
        ]
    );
}

/**
 * Get warranty / guarantee information for a product.
 *
 * Resolution order:
 * 1. Manual product-level ACF fields.
 * 2. Existing imported Euronics warranty meta, where available.
 * 3. Safe Bosch / NEFF / Siemens manufacturer guarantee fallback.
 * 4. Empty result.
 *
 * @param WC_Product|int|null $product Product object or product ID.
 * @return array{label:string,detail:string,registration_note:string,source:string}
 */
function blaines_get_product_warranty_information( $product = null ): array {
    $empty = [
        'label'             => '',
        'detail'            => '',
        'registration_note' => '',
        'source'            => '',
    ];

    $product_id = blaines_get_product_id( $product );

    if ( ! $product_id ) {
        return $empty;
    }

    /*
     * 1. Manual product-level warranty fields.
     */
    if ( function_exists( 'get_field' ) ) {
        $manual_label             = trim( (string) get_field( 'product_warranty_label', $product_id ) );
        $manual_detail            = trim( (string) get_field( 'product_warranty_detail', $product_id ) );
        $manual_registration_note = trim( (string) get_field( 'product_warranty_registration_note', $product_id ) );

        if (
            '' !== $manual_label
            || '' !== $manual_detail
            || '' !== $manual_registration_note
        ) {
            return [
                'label'             => '' !== $manual_label ? $manual_label : 'Manufacturer’s guarantee',
                'detail'            => $manual_detail,
                'registration_note' => $manual_registration_note,
                'source'            => 'manual',
            ];
        }
    }

    /*
     * 2. Existing imported Euronics warranty meta.
     *
     * Keep this fallback for products that are still managed by the Euronics feed,
     * and for any products that already have imported warranty data available.
     */
    $imported_warranty = trim( (string) get_post_meta( $product_id, '_euronics_warranty', true ) );

    $imported_registration_note = '';

    $registration_meta_keys = [
        '_euronics_warranty_registration_information',
        '_euronics_warranty_registration_note',
        '_euronics_warranty_registration',
    ];

    foreach ( $registration_meta_keys as $meta_key ) {
        $meta_value = trim( (string) get_post_meta( $product_id, $meta_key, true ) );

        if ( '' !== $meta_value ) {
            $imported_registration_note = $meta_value;
            break;
        }
    }

    if ( '' !== $imported_warranty ) {
        return [
            'label'             => 'Manufacturer’s guarantee',
            'detail'            => $imported_warranty,
            'registration_note' => $imported_registration_note,
            'source'            => 'imported',
        ];
    }

    /*
     * 3. Bosch / NEFF / Siemens safe generic fallback.
     *
     * Do not make a model-specific guarantee claim here. Use the manual product
     * fields where a precise warranty term needs to be displayed.
     */
    if ( blaines_product_uses_bosch_neff_siemens_warranty_fallback( $product_id ) ) {
        return [
            'label'             => 'Manufacturer’s guarantee',
            'detail'            => 'This product is supplied with the applicable manufacturer’s guarantee.',
            'registration_note' => 'Registration may be required for extended guarantee cover where offered by the manufacturer. Please contact Web Sales if you would like to confirm the current guarantee details before ordering.',
            'source'            => 'bosch_neff_siemens_fallback',
        ];
    }

    return $empty;
}

/**
 * Use UK basket terminology for WooCommerce buttons.
 */
add_filter(
    'woocommerce_product_single_add_to_cart_text',
    function () {
        return __( 'Add to Basket', 'blainestwentysix' );
    },
    99
);

add_filter(
    'woocommerce_product_add_to_cart_text',
    function () {
        return __( 'Add to Basket', 'blainestwentysix' );
    },
    99
);

/**
 * Return the current WooCommerce cart item count.
 */
function blaines_get_cart_count(): int {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return 0;
    }

    return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Refresh header basket count when WooCommerce updates cart fragments.
 */
add_filter(
    'woocommerce_add_to_cart_fragments',
    function ( array $fragments ): array {
        $cart_count = function_exists( 'blaines_get_cart_count' )
            ? blaines_get_cart_count()
            : 0;

        ob_start();
        ?>
        <span class="js-header-cart-count absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-700 px-1 text-[11px] font-semibold leading-none text-white">
            <?php echo esc_html( $cart_count ); ?>
        </span>
        <?php

        $fragments['.js-header-cart-count'] = ob_get_clean();

        return $fragments;
    }
);

/**
 * Include WooCommerce product brand matches in product search results.
 */
add_action(
    'pre_get_posts',
    function ( WP_Query $query ): void {
        if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
            return;
        }

        if ( 'product' !== $query->get( 'post_type' ) ) {
            return;
        }

        $search_term = $query->get( 's' );

        if ( ! $search_term || ! taxonomy_exists( 'product_brand' ) ) {
            return;
        }

        $matching_brand_terms = get_terms(
            [
                'taxonomy'   => 'product_brand',
                'hide_empty' => false,
                'search'     => $search_term,
                'fields'     => 'ids',
            ]
        );

        if ( is_wp_error( $matching_brand_terms ) || empty( $matching_brand_terms ) ) {
            return;
        }

        $brand_product_ids = get_posts(
            [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_brand',
                        'field'    => 'term_id',
                        'terms'    => $matching_brand_terms,
                    ],
                ],
            ]
        );

        if ( empty( $brand_product_ids ) ) {
            return;
        }

        $existing_post__in = $query->get( 'post__in' );

        if ( ! empty( $existing_post__in ) && is_array( $existing_post__in ) ) {
            $brand_product_ids = array_unique(
                array_merge(
                    array_map( 'absint', $existing_post__in ),
                    array_map( 'absint', $brand_product_ids )
                )
            );
        }

        $query->set( 'post__in', $brand_product_ids );
        $query->set( 's', '' );
    }
);

/**
 * Find products matching a product search term across title/content, SKU,
 * product brand, and product categories.
 */
function blaines_get_product_search_ids( string $search_term ): array {
    $search_term = trim( $search_term );

    if ( '' === $search_term ) {
        return [];
    }

    $matched_ids = [];

    /*
     * 1. Standard WordPress product search:
     * title, content, excerpt.
     */
    $standard_ids = get_posts(
        [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            's'              => $search_term,
        ]
    );

    $matched_ids = array_merge( $matched_ids, $standard_ids );

    /*
     * 2. SKU / product code search.
     */
    $sku_ids = get_posts(
        [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => '_sku',
                    'value'   => $search_term,
                    'compare' => 'LIKE',
                ],
            ],
        ]
    );

    $matched_ids = array_merge( $matched_ids, $sku_ids );

    /*
     * 3. Product brand taxonomy search.
     */
    if ( taxonomy_exists( 'product_brand' ) ) {
        $brand_terms = get_terms(
            [
                'taxonomy'   => 'product_brand',
                'hide_empty' => false,
                'search'     => $search_term,
                'fields'     => 'ids',
            ]
        );

        if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
            $brand_product_ids = get_posts(
                [
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'product_brand',
                            'field'    => 'term_id',
                            'terms'    => $brand_terms,
                        ],
                    ],
                ]
            );

            $matched_ids = array_merge( $matched_ids, $brand_product_ids );
        }
    }

    /*
     * 4. Product category / product type search.
     * Examples: Laundry, Washing Machines, Tumble Dryers.
     */
    $category_terms = get_terms(
        [
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'search'     => $search_term,
            'fields'     => 'ids',
        ]
    );

    if ( ! is_wp_error( $category_terms ) && ! empty( $category_terms ) ) {
        $category_product_ids = get_posts(
            [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [
                    [
                        'taxonomy'         => 'product_cat',
                        'field'            => 'term_id',
                        'terms'            => $category_terms,
                        'include_children' => true,
                    ],
                ],
            ]
        );

        $matched_ids = array_merge( $matched_ids, $category_product_ids );
    }

    $matched_ids = array_map( 'absint', $matched_ids );
    $matched_ids = array_values( array_unique( array_filter( $matched_ids ) ) );

    /*
     * Only return transactional products for the header product search.
     * This prevents enquiry-led clearance/display products polluting the ecommerce search.
     */
    if ( function_exists( 'blaines_is_transactional_product' ) ) {
        $matched_ids = array_values(
            array_filter(
                $matched_ids,
                static function ( int $product_id ): bool {
                    $product = wc_get_product( $product_id );

                    return $product && blaines_is_transactional_product( $product );
                }
            )
        );
    }

    return $matched_ids;
}