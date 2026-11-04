<?php
/**
 * Blaines WooCommerce fulfilment service rules.
 *
 * Maps WooCommerce product categories to available fulfilment services.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_fulfilment_service_code_map' ) ) {
    /**
     * Map product category slugs to available fulfilment service codes.
     *
     * @return array<string, array<int, string>>
     */
    function blaines_get_fulfilment_service_code_map(): array {
        return [
            /*
 * Laundry.
 */
            'laundry-washing-machines' => [
                'freestanding' => [
                    'EUR-INSTALLWMWD',
                    'EUR-DISCONNECTWMWD',
                    'EUR-DISPOSALWMWD',
                    'EUR-REMPACKWMWD',
                ],
                'integrated' => [
                    'EUR-INSTALLINTWMWD',
                    'EUR-DISCONNECTWMWD',
                    'EUR-DISPOSALWMWD',
                    'EUR-REMPACKWMWD',
                ],
            ],

            'laundry-washer-dryers' => [
                'freestanding' => [
                    'EUR-INSTALLWMWD',
                    'EUR-DISCONNECTWMWD',
                    'EUR-DISPOSALWMWD',
                    'EUR-REMPACKWMWD',
                ],
                'integrated' => [
                    'EUR-INSTALLINTWMWD',
                    'EUR-DISCONNECTWMWD',
                    'EUR-DISPOSALWMWD',
                    'EUR-REMPACKWMWD',
                ],
            ],

            'laundry-tumble-dryers' => [
                'freestanding' => [
                    'EUR-INSTALLTD',
                    'EUR-DISCONNECTTD',
                    'EUR-DISPOSALTD',
                    'EUR-REMPACKTD',
                ],
                'integrated' => [
                    'EUR-INSTALLINTTD',
                    'EUR-DISCONNECTTD',
                    'EUR-DISPOSALTD',
                    'EUR-REMPACKTD',
                ],
            ],

            /*
 * Dishwashers.
 */
            'dishwashers' => [
                'freestanding' => [
                    'EUR-INSTALLDW',
                    'EUR-DISCONNECTDW',
                    'EUR-DISPOSALDW',
                    'EUR-REMPACKDW',
                ],
                'integrated' => [
                    'EUR-INSTALLINTDW',
                    'EUR-DISCONNECTDW',
                    'EUR-DISPOSALDW',
                    'EUR-REMPACKDW',
                ],
            ],

            'dishwashers-dishwashers' => [
                'freestanding' => [
                    'EUR-INSTALLDW',
                    'EUR-DISCONNECTDW',
                    'EUR-DISPOSALDW',
                    'EUR-REMPACKDW',
                ],
                'integrated' => [
                    'EUR-INSTALLINTDW',
                    'EUR-DISCONNECTDW',
                    'EUR-DISPOSALDW',
                    'EUR-REMPACKDW',
                ],
            ],

            /*
 * Refrigeration.
 */
            'refrigeration-fridges' => [
                'freestanding' => [
                    'EUR-INSTALLFRIDGE',
                    'EUR-DISCONNECTFRIDGE',
                    'EUR-DISPOSALFRIDGE',
                    'EUR-REMPACKFRIDGE',
                ],
                'integrated' => [
                    'EUR-INSTALLINTFRIDGE',
                    'EUR-DISCONNECTFRIDGE',
                    'EUR-DISPOSALFRIDGE',
                    'EUR-REMPACKFRIDGE',
                ],
            ],

            'refrigeration-freezers' => [
                'freestanding' => [
                    'EUR-INSTALLFREEZER',
                    'EUR-DISCONNECTFREEZER',
                    'EUR-DISPOSALFREEZER',
                    'EUR-REMPACKFREEZER',
                ],
                'integrated' => [
                    'EUR-INSTALLINTFREEZER',
                    'EUR-DISCONNECTFREEZER',
                    'EUR-DISPOSALFREEZER',
                    'EUR-REMPACKFREEZER',
                ],
            ],

            'refrigeration-chest-freezers' => [
                'freestanding' => [
                    'EUR-INSTALLFREEZER',
                    'EUR-DISCONNECTFREEZER',
                    'EUR-DISPOSALFREEZER',
                    'EUR-REMPACKFREEZER',
                ],
            ],

            'refrigeration-fridge-freezers' => [
                'freestanding' => [
                    'EUR-INSTALLFF',
                    'EUR-DISCONNECTFF',
                    'EUR-DISPOSALFF',
                    'EUR-REMPACKFF',
                ],
                'integrated' => [
                    'EUR-INSTALLINTFF',
                    'EUR-DISCONNECTFF',
                    'EUR-DISPOSALFF',
                    'EUR-REMPACKFF',
                ],
            ],

            'refrigeration-american-style-fridge-freezers' => [
                'side-by-side' => [
                    'EUR-INSTALLSBS',
                    'EUR-DISCONNECTSBS',
                    'EUR-DISPOSALSBS',
                    'EUR-REMPACKSBS',
                ],
                'default' => [
                    'EUR-INSTALLSBS',
                    'EUR-DISCONNECTSBS',
                    'EUR-DISPOSALSBS',
                    'EUR-REMPACKSBS',
                ],
            ],

            /*
 * Cooking.
 */
            'cooking-cookers' => [
                'electric' => [
                    'EUR-INSTALLELECCOOK',
                    'EUR-DISPOSALCOOKER',
                    'EUR-REMPACKCOOKER',
                ],
                'gas' => [
                    'EUR-INSTALLGASCOOKER',
                    'EUR-DISPOSALCOOKER',
                    'EUR-REMPACKCOOKER',
                ],
                'dual-fuel' => [
                    'EUR-INSTALLDUALCOOK',
                    'EUR-DISPOSALCOOKER',
                    'EUR-REMPACKCOOKER',
                ],
            ],

            'cooking-range-cookers' => [
                'electric' => [
                    'EUR-INSTALLELECRANGE',
                    'EUR-DISPOSALRANGE',
                    'EUR-REMPACKRANGE',
                ],
                'gas' => [
                    'EUR-INSTALLGASRANGE',
                    'EUR-DISPOSALRANGE',
                    'EUR-REMPACKRANGE',
                ],
                'dual-fuel' => [
                    'EUR-INSTALLDUALRANGE',
                    'EUR-DISPOSALRANGE',
                    'EUR-REMPACKRANGE',
                ],
            ],

            'cooking-built-in-ovens' => [
                'electric' => [
                    'EUR-INSTALLELECOVEN',
                    'EUR-DISPOSALOVEN',
                    'EUR-REMPACKOVEN',
                ],
                'gas' => [
                    'EUR-INSTALLGASOVEN',
                    'EUR-DISPOSALOVEN',
                    'EUR-REMPACKOVEN',
                ],
            ],

            'cooking-hobs' => [
                'electric' => [
                    'EUR-INSTALLELECHOB',
                    'EUR-DISPOSALHOB',
                    'EUR-REMPACKHOB',
                ],
                'gas' => [
                    'EUR-INSTALLGASHOB',
                    'EUR-DISPOSALHOB',
                    'EUR-REMPACKHOB',
                ],
            ],

            'cooking-microwaves' => [
                'default' => [
                    'EUR-DISPOSALMWAVE',
                ],
            ],
        ];
    }

    if ( ! function_exists( 'blaines_get_product_fitting_type' ) ) {
        /**
         * Get the product fitting type from the WooCommerce Fitting Type attribute.
         *
         * Expected attribute taxonomy: fitting-type.
         * WooCommerce resolves this to the correct pa_ taxonomy name.
         *
         * @param int|WC_Product $product Product ID or product object.
         * @return string
         */
        function blaines_get_product_fitting_type( int|WC_Product $product ): string {
            $product_obj = is_a( $product, 'WC_Product' )
                ? $product
                : wc_get_product( $product );

            if ( ! $product_obj ) {
                return '';
            }

            $product_id = $product_obj->get_id();

            if ( $product_obj->is_type( 'variation' ) ) {
                $product_id = $product_obj->get_parent_id();
            }

            $taxonomy = function_exists( 'wc_attribute_taxonomy_name' )
                ? wc_attribute_taxonomy_name( 'installation-type' )
                : 'pa_installation-type';

            $terms = get_the_terms( $product_id, $taxonomy );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                return '';
            }

            $term = reset( $terms );

            return $term instanceof WP_Term ? sanitize_title( $term->slug ) : '';
        }
    }

    if ( ! function_exists( 'blaines_get_product_fuel_type' ) ) {
        /**
         * Get the product fuel type from the WooCommerce Fuel Type attribute.
         *
         * Expected attribute taxonomy: fuel-type.
         * WooCommerce resolves this to the correct pa_ taxonomy name.
         *
         * @param int|WC_Product $product Product ID or product object.
         * @return string
         */
        function blaines_get_product_fuel_type( int|WC_Product $product ): string {
            $product_obj = is_a( $product, 'WC_Product' )
                ? $product
                : wc_get_product( $product );

            if ( ! $product_obj ) {
                return '';
            }

            $product_id = $product_obj->get_id();

            if ( $product_obj->is_type( 'variation' ) ) {
                $product_id = $product_obj->get_parent_id();
            }

            $taxonomy = function_exists( 'wc_attribute_taxonomy_name' )
                ? wc_attribute_taxonomy_name( 'fuel-type' )
                : 'pa_fuel-type';

            $terms = get_the_terms( $product_id, $taxonomy );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                return '';
            }

            $term = reset( $terms );

            return $term instanceof WP_Term ? sanitize_title( $term->slug ) : '';
        }
    }

    if ( ! function_exists( 'blaines_get_product_fulfilment_service_codes' ) ) {
        /**
         * Get fulfilment service codes available for a product.
         *
         * @param int|WC_Product $product Product ID or product object.
         * @return array<int, string>
         */
        function blaines_get_product_fulfilment_service_codes( int|WC_Product $product ): array {
            $product_obj = is_a( $product, 'WC_Product' )
                ? $product
                : wc_get_product( $product );

            if ( ! $product_obj ) {
                return [];
            }

            if (
                function_exists( 'blaines_is_transactional_product' )
                && ! blaines_is_transactional_product( $product_obj )
            ) {
                return [];
            }

            $product_id = $product_obj->get_id();

            if ( $product_obj->is_type( 'variation' ) ) {
                $product_id = $product_obj->get_parent_id();
            }

            $terms = get_the_terms( $product_id, 'product_cat' );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                return [];
            }

            $product_slugs = [];

            foreach ( $terms as $term ) {
                if ( ! $term instanceof WP_Term ) {
                    continue;
                }

                $product_slugs[] = $term->slug;

                $ancestor_ids = get_ancestors( $term->term_id, 'product_cat' );

                foreach ( $ancestor_ids as $ancestor_id ) {
                    $ancestor = get_term( $ancestor_id, 'product_cat' );

                    if ( $ancestor instanceof WP_Term ) {
                        $product_slugs[] = $ancestor->slug;
                    }
                }
            }

            $product_slugs = array_values( array_unique( $product_slugs ) );

            $service_map   = blaines_get_fulfilment_service_code_map();
            $fitting_type = function_exists( 'blaines_get_product_fitting_type' )
                ? blaines_get_product_fitting_type( $product_obj )
                : '';

            $fuel_type = function_exists( 'blaines_get_product_fuel_type' )
                ? blaines_get_product_fuel_type( $product_obj )
                : '';
            $service_codes = [];

            foreach ( $service_map as $category_slug => $codes ) {
                if ( ! in_array( $category_slug, $product_slugs, true ) ) {
                    continue;
                }

                /*
                 * Backwards-compatible support for old flat mappings:
                 * 'category-slug' => [ 'SERVICE1', 'SERVICE2' ]
                 */
                if ( isset( $codes[0] ) && is_string( $codes[0] ) ) {
                    $service_codes = array_merge( $service_codes, $codes );
                    continue;
                }

                /*
                 * New fitting-type-aware mappings:
                 * 'category-slug' => [
                 *     'freestanding' => [ ... ],
                 *     'integrated'   => [ ... ],
                 * ]
                 */
                if (
                    $fitting_type
                    && isset( $codes[ $fitting_type ] )
                    && is_array( $codes[ $fitting_type ] )
                ) {
                    $service_codes = array_merge( $service_codes, $codes[ $fitting_type ] );
                    continue;
                }

                if (
                    $fuel_type
                    && isset( $codes[ $fuel_type ] )
                    && is_array( $codes[ $fuel_type ] )
                ) {
                    $service_codes = array_merge( $service_codes, $codes[ $fuel_type ] );
                    continue;
                }

                if ( isset( $codes['default'] ) && is_array( $codes['default'] ) ) {
                    $service_codes = array_merge( $service_codes, $codes['default'] );
                }
            }

            return array_values( array_unique( $service_codes ) );
        }
    }

    if ( ! function_exists( 'blaines_get_product_fulfilment_services' ) ) {
        /**
         * Get available fulfilment services for a product.
         *
         * Returns full service data including code, group, label and price.
         *
         * @param int|WC_Product $product Product ID or product object.
         * @return array<int, array<string, mixed>>
         */
        function blaines_get_product_fulfilment_services( int|WC_Product $product ): array {
            if ( ! function_exists( 'blaines_get_fulfilment_service' ) ) {
                return [];
            }

            $service_codes = blaines_get_product_fulfilment_service_codes( $product );

            if ( empty( $service_codes ) ) {
                return [];
            }

            $services = [];

            foreach ( $service_codes as $service_code ) {
                $service = blaines_get_fulfilment_service( $service_code );

                if ( ! $service ) {
                    continue;
                }

                $services[] = array_merge(
                    [
                        'code' => $service_code,
                    ],
                    $service
                );
            }

            return $services;
        }
    }
}