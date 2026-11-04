<?php
/**
 * Blaines WooCommerce fulfilment service catalogue.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_fulfilment_service_catalogue' ) ) {
    /**
     * Get available fulfilment services.
     *
     * Prices are stored as plain decimal pound values.
     *
     * @return array<string, array<string, mixed>>
     */
    function blaines_get_fulfilment_service_catalogue(): array {
        return [
            /*
             * Installation services — Laundry and Dishwashers.
             */
            'EUR-INSTALLWMWD' => [
                'group' => 'installation',
                'label' => 'Washing Machine/Washer Dryer Installation',
                'price' => 30.00,
            ],

            'EUR-INSTALLINTWMWD' => [
                'group' => 'installation',
                'label' => 'Integrated Washing Machine/Washer Dryer Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLTD' => [
                'group' => 'installation',
                'label' => 'Tumble Dryer Installation',
                'price' => 15.00,
            ],

            'EUR-INSTALLINTTD' => [
                'group' => 'installation',
                'label' => 'Integrated Tumble Dryer Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLDW' => [
                'group' => 'installation',
                'label' => 'Dishwasher Installation',
                'price' => 30.00,
            ],

            'EUR-INSTALLINTDW' => [
                'group' => 'installation',
                'label' => 'Integrated Dishwasher Installation',
                'price' => 115.00,
            ],

            /*
 * Installation services — Refrigeration.
 */
            'EUR-INSTALLFRIDGE' => [
                'group' => 'installation',
                'label' => 'Fridge Installation',
                'price' => 15.00,
            ],

            'EUR-INSTALLFREEZER' => [
                'group' => 'installation',
                'label' => 'Freezer Installation',
                'price' => 15.00,
            ],

            'EUR-INSTALLFF' => [
                'group' => 'installation',
                'label' => 'Fridge Freezer Installation',
                'price' => 15.00,
            ],

            'EUR-INSTALLINTFRIDGE' => [
                'group' => 'installation',
                'label' => 'Integrated Fridge Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLINTFRZR' => [
                'group' => 'installation',
                'label' => 'Integrated Freezer Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLINTFF' => [
                'group' => 'installation',
                'label' => 'Integrated Fridge Freezer Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLSBS' => [
                'group' => 'installation',
                'label' => 'Side by Side Fridge Freezer Installation',
                'price' => 40.00,
            ],

            'EUR-INSTALLDRSOFFSBS' => [
                'group' => 'installation',
                'label' => 'Doors Off Side by Side Fridge Freezer Installation',
                'price' => 70.00,
            ],

            'EUR-INSTALLINTFREEZER' => [
                'group' => 'installation',
                'label' => 'Integrated Freezer Installation',
                'price' => 115.00,
            ],

            /*
 * Installation services — Cooking.
 */
            'EUR-INSTALLDUALCOOK' => [
                'group' => 'installation',
                'label' => 'Dual Fuel Cooker Installation',
                'price' => 125.00,
            ],

            'EUR-INSTALLDUALRANGE' => [
                'group' => 'installation',
                'label' => 'Dual Fuel Range Cooker Installation',
                'price' => 125.00,
            ],

            'EUR-INSTALLELECCOOK' => [
                'group' => 'installation',
                'label' => 'Electric Freestanding Cooker Installation',
                'price' => 100.00,
            ],

            'EUR-INSTALLELECHOB' => [
                'group' => 'installation',
                'label' => 'Integrated Electric Hob Installation',
                'price' => 110.00,
            ],

            'EUR-INSTALLELECOVEN' => [
                'group' => 'installation',
                'label' => 'Integrated Electric Oven Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLELECRANGE' => [
                'group' => 'installation',
                'label' => 'Electric Range Cooker Installation',
                'price' => 100.00,
            ],

            'EUR-INSTALLGASCOOKER' => [
                'group' => 'installation',
                'label' => 'Freestanding Gas Cooker Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLGASHOB' => [
                'group' => 'installation',
                'label' => 'Integrated Gas Hob Installation',
                'price' => 120.00,
            ],

            'EUR-INSTALLGASOVEN' => [
                'group' => 'installation',
                'label' => 'Integrated Gas Oven Installation',
                'price' => 115.00,
            ],

            'EUR-INSTALLGASRANGE' => [
                'group' => 'installation',
                'label' => 'Gas Range Cooker Installation',
                'price' => 115.00,
            ],

            /*
 * Installation services — TV & Entertainment.
 */
            'EUR-INSTALLTVSTDARD' => [
                'group' => 'installation',
                'label' => 'TV Installation',
                'price' => 45.00,
            ],

            'EUR-INSTALLTVWALLBKT' => [
                'group' => 'installation',
                'label' => 'Wall Mounted TV Installation',
                'price' => 125.00,
            ],

            /*
 * Disconnection services.
 */
            'EUR-DISCONNECTDW' => [
                'group' => 'disconnection',
                'label' => 'Dishwasher Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTFF' => [
                'group' => 'disconnection',
                'label' => 'Fridge Freezer Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTFRZR' => [
                'group' => 'disconnection',
                'label' => 'Freezer Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTFRIDGE' => [
                'group' => 'disconnection',
                'label' => 'Fridge Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTSBS' => [
                'group' => 'disconnection',
                'label' => 'Side by Side Fridge Freezer Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTTD' => [
                'group' => 'disconnection',
                'label' => 'Tumble Dryer Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTWMWD' => [
                'group' => 'disconnection',
                'label' => 'Washing Machine/Washer Dryer Disconnection',
                'price' => 10.00,
            ],

            'EUR-DISCONNECTFREEZER' => [
                'group' => 'disconnection',
                'label' => 'Freezer Disconnection',
                'price' => 10.00,
            ],

            /*
 * Disposal services.
 */
            'EUR-DISPOSALCOOKER' => [
                'group' => 'disposal',
                'label' => 'Freestanding Cooker Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALDW' => [
                'group' => 'disposal',
                'label' => 'Dishwasher Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALFF' => [
                'group' => 'disposal',
                'label' => 'Fridge Freezer Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALFREEZER' => [
                'group' => 'disposal',
                'label' => 'Freezer Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALFRIDGE' => [
                'group' => 'disposal',
                'label' => 'Fridge Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALHOB' => [
                'group' => 'disposal',
                'label' => 'Hob Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALMWAVE' => [
                'group' => 'disposal',
                'label' => 'Microwave Disposal',
                'price' => 15.00,
            ],

            'EUR-DISPOSALOVEN' => [
                'group' => 'disposal',
                'label' => 'Oven Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALRANGE' => [
                'group' => 'disposal',
                'label' => 'Range Cooker Disposal',
                'price' => 30.00,
            ],

            'EUR-DISPOSALSBS' => [
                'group' => 'disposal',
                'label' => 'Side by Side Fridge Freezer Disposal',
                'price' => 30.00,
            ],

            'EUR-DISPOSALTD' => [
                'group' => 'disposal',
                'label' => 'Tumble Dryer Disposal',
                'price' => 25.00,
            ],

            'EUR-DISPOSALTV' => [
                'group' => 'disposal',
                'label' => 'TV Disposal',
                'price' => 15.00,
            ],

            'EUR-DISPOSALWMWD' => [
                'group' => 'disposal',
                'label' => 'Washing Machine/Washer Dryer Disposal',
                'price' => 25.00,
            ],

            /*
 * Packaging removal services.
 */
            'EUR-REMPACKDW' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Dishwasher',
                'price' => 5.00,
            ],

            'EUR-REMPACKTD' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Tumble Dryer',
                'price' => 5.00,
            ],

            'EUR-REMPACKWMWD' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Washing Machine/Washer Dryer',
                'price' => 5.00,
            ],

            'EUR-REMPACKCOOKER' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Freestanding Cooker',
                'price' => 5.00,
            ],

            'EUR-REMPACKFF' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Fridge Freezer',
                'price' => 5.00,
            ],

            'EUR-REMPACKFREEZER' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Freezer',
                'price' => 5.00,
            ],

            'EUR-REMPACKFRIDGE' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Fridge',
                'price' => 5.00,
            ],

            'EUR-REMPACKHOB' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Integrated Hob',
                'price' => 5.00,
            ],

            'EUR-REMPACKOVEN' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Integrated Oven',
                'price' => 5.00,
            ],

            'EUR-REMPACKRANGE' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Range Cooker',
                'price' => 5.00,
            ],

            'EUR-REMPACKSBS' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal Side by Side Fridge Freezer',
                'price' => 5.00,
            ],

            'EUR-REMPACKTV' => [
                'group' => 'packaging',
                'label' => 'Packaging Removal TV',
                'price' => 5.00,
            ],

            /*
             * AV setup services.
             */
            'EUR-DVDPLAYERSETUP' => [
                'group' => 'setup',
                'label' => 'Set Up DVD Player',
                'price' => 25.00,
            ],

            'EUR-HOMECINEMASETUP' => [
                'group' => 'setup',
                'label' => 'Set Up Home Cinema',
                'price' => 25.00,
            ],
        ];
    }

    if ( ! function_exists( 'blaines_get_fulfilment_service' ) ) {
        /**
         * Get a single fulfilment service by service code.
         *
         * @param string $service_code Service product code.
         * @return array<string, mixed>|null
         */
        function blaines_get_fulfilment_service( string $service_code ): ?array {
            $catalogue = blaines_get_fulfilment_service_catalogue();

            return $catalogue[ $service_code ] ?? null;
        }
    }

    if ( ! function_exists( 'blaines_get_fulfilment_service_price' ) ) {
        /**
         * Get a fulfilment service price.
         *
         * @param string $service_code Service product code.
         * @return float
         */
        function blaines_get_fulfilment_service_price( string $service_code ): float {
            $service = blaines_get_fulfilment_service( $service_code );

            return $service && isset( $service['price'] )
                ? (float) $service['price']
                : 0.00;
        }
    }

    if ( ! function_exists( 'blaines_get_fulfilment_services_by_group' ) ) {
        /**
         * Get fulfilment services by group.
         *
         * @param string $group Service group.
         * @return array<string, array<string, mixed>>
         */
        function blaines_get_fulfilment_services_by_group( string $group ): array {
            $catalogue = blaines_get_fulfilment_service_catalogue();

            return array_filter(
                $catalogue,
                static function ( array $service ) use ( $group ): bool {
                    return isset( $service['group'] ) && $group === $service['group'];
                }
            );
        }
    }
}
