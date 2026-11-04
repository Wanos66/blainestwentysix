<?php
/**
 * Blaines WooCommerce order display customisations.
 *
 * Polishes order item meta and order totals labels for customer emails,
 * admin emails, the order received page and WooCommerce admin order screens.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_rename_selected_fulfilment_services_meta_key' ) ) {
    /**
     * Rename the selected services order item meta key when displayed.
     *
     * The stored meta key remains unchanged for compatibility.
     *
     * @param string        $display_key Display meta key.
     * @param WC_Meta_Data  $meta        Meta object.
     * @param WC_Order_Item $item        Order item.
     * @return string
     */
    function blaines_rename_selected_fulfilment_services_meta_key( string $display_key, WC_Meta_Data $meta, WC_Order_Item $item ): string {
        if ( 'Selected fulfilment services' === $display_key ) {
            return 'Services requested';
        }

        return $display_key;
    }

    add_filter( 'woocommerce_order_item_display_meta_key', 'blaines_rename_selected_fulfilment_services_meta_key', 20, 3 );
}

if ( ! function_exists( 'blaines_format_selected_fulfilment_services_meta_value' ) ) {
    /**
     * Display selected fulfilment services on separate lines.
     *
     * @param string        $display_value Display meta value.
     * @param WC_Meta_Data  $meta          Meta object.
     * @param WC_Order_Item $item          Order item.
     * @return string
     */
    function blaines_format_selected_fulfilment_services_meta_value( string $display_value, WC_Meta_Data $meta, WC_Order_Item $item ): string {
        if ( 'Selected fulfilment services' !== $meta->key ) {
            return $display_value;
        }

        $services = array_filter(
            array_map(
                'trim',
                explode( ', ', wp_strip_all_tags( $display_value ) )
            )
        );

        if ( empty( $services ) ) {
            return $display_value;
        }

        return implode( '<br>', array_map( 'esc_html', $services ) );
    }

    add_filter( 'woocommerce_order_item_display_meta_value', 'blaines_format_selected_fulfilment_services_meta_value', 20, 3 );
}

if ( ! function_exists( 'blaines_rename_order_shipping_total_label' ) ) {
    /**
     * Rename Shipping to Fulfilment in order totals displays.
     *
     * @param array<string, array<string, string>> $totals Order totals.
     * @param WC_Order                            $order  Order object.
     * @param string                              $tax_display Tax display mode.
     * @return array<string, array<string, string>>
     */
    function blaines_rename_order_shipping_total_label( array $totals, WC_Order $order, string $tax_display ): array {
        if ( isset( $totals['shipping']['label'] ) ) {
            $totals['shipping']['label'] = 'Fulfilment:';
        }

        return $totals;
    }

    add_filter( 'woocommerce_get_order_item_totals', 'blaines_rename_order_shipping_total_label', 20, 3 );
}
