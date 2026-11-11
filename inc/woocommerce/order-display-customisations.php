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

/**
 * CP2A.4 — Format existing service metadata in HTML emails.
 *
 * New WooCommerce email layouts allow only a limited selection of inline
 * elements in standard item metadata. Render our service detail using the
 * supported item-meta-start hook instead, then suppress only its duplicated
 * metadata row while WooCommerce renders this particular item.
 *
 * The stored item meta and separate WooCommerce fee totals are unchanged.
 */
if ( ! function_exists( 'blaines_email_fulfilment_service_rows' ) ) {
    /** @return array<int,array{name:string,price:string}> */
    function blaines_email_fulfilment_service_rows( WC_Order_Item $item ): array {
        $stored = trim( (string) $item->get_meta( 'Selected fulfilment services', true ) );
        if ( '' === $stored ) {
            return [];
        }

        // The original metadata was built with wc_price(). Its currency symbol
        // can be stored as &pound; (or &#163;) rather than as a literal £.
        // Decode entities BEFORE matching monetary amounts. Do not rewrite
        // the actual metadata, as payment providers and historical orders use it.
        $stored = html_entity_decode(
            wp_strip_all_tags( $stored ),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Normalise non-breaking spaces produced by currency formatters.
        $stored = str_replace( [ "\xC2\xA0", "\xE2\x80\xAF" ], ' ', $stored );

        $names = [
            'Washing Machine/Washer Dryer Installation' => 'Washing machine installation',
            'Washing Machine/Washer Dryer Disposal' => 'Old appliance disposal',
            'Packaging Removal Washing Machine/Washer Dryer' => 'Packaging removal',
        ];

        $rows = [];
        foreach ( preg_split( '/,\s+(?=[^,]+\s+—\s*£)/u', $stored ) ?: [] as $entry ) {
            if ( ! preg_match( '/^\s*(.+?)\s+—\s+(£[0-9,]+(?:\.[0-9]{2})?)\s*$/u', $entry, $matches ) ) {
                // Preserve native display if a historical value is unfamiliar.
                return [];
            }
            $name = trim( $matches[1] );
            $rows[] = [
                'name'  => $names[ $name ] ?? $name,
                'price' => $matches[2],
            ];
        }
        return $rows;
    }
}

if ( ! function_exists( 'blaines_email_render_fulfilment_services' ) ) {
    function blaines_email_render_fulfilment_services( $item_id, $item, $order, $plain_text ): void {
        if ( $plain_text || ! doing_action( 'woocommerce_email_order_details' ) || ! $item instanceof WC_Order_Item_Product ) {
            return;
        }

        $rows = blaines_email_fulfilment_service_rows( $item );
        if ( ! $rows ) {
            return;
        }

        // This flag applies only until woocommerce_order_item_meta_end.
        $GLOBALS['blaines_email_formatted_service_item'] = $item->get_id();
        ?>
        <div style="margin:12px 0 8px;max-width:100%;">
            <div style="font-weight:700;font-size:12px;color:#143981;margin:0 0 6px;">
                Additional services
            </div>
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="width:100%;max-width:270px;border-collapse:collapse;">
                <?php foreach ( $rows as $row ) : ?>
                    <tr>
                        <td style="padding:2px 8px 2px 0;border:0;font-size:12px;line-height:1.45;color:#374151;vertical-align:top;text-align:left;">
                            <?php echo esc_html( $row['name'] ); ?>
                        </td>
                        <td width="62" align="right" style="width:62px;padding:2px 0;border:0;font-size:12px;line-height:1.45;color:#143981;text-align:right;vertical-align:top;white-space:nowrap;">
                            <?php echo esc_html( $row['price'] ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php
    }
    add_action( 'woocommerce_order_item_meta_start', 'blaines_email_render_fulfilment_services', 10, 4 );
}

if ( ! function_exists( 'blaines_email_hide_duplicate_service_meta' ) ) {
    function blaines_email_hide_duplicate_service_meta( $formatted_meta, $item ) {
        if (
            ! doing_action( 'woocommerce_email_order_details' )
            || ! isset( $GLOBALS['blaines_email_formatted_service_item'] )
            || (int) $GLOBALS['blaines_email_formatted_service_item'] !== (int) $item->get_id()
        ) {
            return $formatted_meta;
        }

        foreach ( $formatted_meta as $meta_id => $meta ) {
            if ( is_object( $meta ) && 'Selected fulfilment services' === ( $meta->key ?? '' ) ) {
                unset( $formatted_meta[ $meta_id ] );
            }
        }
        return $formatted_meta;
    }
    add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'blaines_email_hide_duplicate_service_meta', 100, 2 );
}

if ( ! function_exists( 'blaines_email_end_fulfilment_services' ) ) {
    function blaines_email_end_fulfilment_services(): void {
        unset( $GLOBALS['blaines_email_formatted_service_item'] );
    }
    add_action( 'woocommerce_order_item_meta_end', 'blaines_email_end_fulfilment_services', 10 );
}

/**
 * CP2A.5 — Align product, quantity and price at the top of email rows that
 * have the expanded additional-services section. WooCommerce normally uses
 * vertical-align: middle for these cells, making them appear halfway down.
 *
 * Only service-bearing rows in the HTML order-details email are affected.
 */
if ( ! function_exists( 'blaines_email_class_for_fulfilment_services' ) ) {
    function blaines_email_class_for_fulfilment_services( string $class, $item, $order ): string {
        if (
            ! doing_action( 'woocommerce_email_order_details' )
            || ! $item instanceof WC_Order_Item_Product
            || ! blaines_email_fulfilment_service_rows( $item )
        ) {
            return $class;
        }

        return $class . ' blaines-email-has-services';
    }
    add_filter( 'woocommerce_order_item_class', 'blaines_email_class_for_fulfilment_services', 20, 3 );
}

if ( ! function_exists( 'blaines_email_style_for_fulfilment_services' ) ) {
    function blaines_email_style_for_fulfilment_services( string $css ): string {
        // WooCommerce inlines these rules into the email HTML where supported.
        // !important overrides WooCommerce's inline vertical-align:middle.
        $css .= "\n"
            . '#body_content tr.blaines-email-has-services > td.td {'
            . 'vertical-align:top !important;}'
            . "\n"
            . '#body_content tr.blaines-email-has-services table.order-item-data td {'
            . 'vertical-align:top !important;}'
            . "\n";

        return $css;
    }
    add_filter( 'woocommerce_email_styles', 'blaines_email_style_for_fulfilment_services', 20 );
}

/**
 * CP2B.4 — Send the existing WooCommerce customer cancellation email when
 * Blaines' custom Order Confirmed status transitions to Cancelled.
 *
 * Core WooCommerce handles Processing/On-hold -> Cancelled but does not
 * register a customer notification for the custom Order Confirmed transition.
 * This is a notification-only bridge: it does not cancel payment, refund,
 * change stock or modify the order status.
 */
if ( ! function_exists( 'blaines_email_order_confirmed_to_cancelled' ) ) {
    function blaines_email_order_confirmed_to_cancelled( $order_id, $order = false ): void {
        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        if ( ! $order instanceof WC_Order ) {
            $order = wc_get_order( $order_id );
        }

        if ( ! $order instanceof WC_Order || ! $order->has_status( 'cancelled' ) ) {
            return;
        }

        $emails = WC()->mailer()->get_emails();
        $email  = $emails['WC_Email_Customer_Cancelled_Order'] ?? null;

        if ( ! $email instanceof WC_Email || ! $email->is_enabled() ) {
            return;
        }

        $email->trigger( $order->get_id(), $order );
    }

    add_action(
        'woocommerce_order_status_order-confirmed_to_cancelled',
        'blaines_email_order_confirmed_to_cancelled',
        20,
        2
    );
}
