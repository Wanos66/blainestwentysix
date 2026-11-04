<?php
/**
 * Cromwell EPOS order cross-reference.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display the EPOS reference within the General
 * column of the WooCommerce order editor.
 *
 * @param WC_Order $order WooCommerce order.
 */
add_action(
    'woocommerce_admin_order_data_after_order_details',
    function ( $order ) {

        if ( ! $order instanceof WC_Order ) {
            return;
        }

        $reference = $order->get_meta(
            '_blaines_cromwell_epos_reference',
            true
        );

        ?>
        <p class="form-field form-field-wide blaines-epos-reference">
            <label for="blaines_cromwell_epos_reference">
                <?php esc_html_e(
                    'Cromwell EPOS reference',
                    'blainestwentysix'
                ); ?>
            </label>

            <input
                type="text"
                id="blaines_cromwell_epos_reference"
                name="blaines_cromwell_epos_reference"
                value="<?php echo esc_attr( $reference ); ?>"
                placeholder="CSH........"
                autocomplete="off"
            />

            <span class="description">
                Internal reference — not shown to customers.
            </span>
        </p>
        <?php
    }
);

/**
 * Save the Cromwell EPOS reference when an order
 * is updated in WooCommerce Admin.
 *
 * Compatible with WooCommerce HPOS.
 *
 * @param int $order_id WooCommerce order ID.
 */
add_action(
    'woocommerce_process_shop_order_meta',
    function ( $order_id ) {

        // Only process our field when it is submitted.
        if ( ! isset( $_POST['blaines_cromwell_epos_reference'] ) ) {
            return;
        }

        $order = wc_get_order( $order_id );

        if ( ! $order instanceof WC_Order ) {
            return;
        }

        // Sanitise and normalise the reference.
        $reference = sanitize_text_field(
            wp_unslash(
                $_POST['blaines_cromwell_epos_reference']
            )
        );

        $reference = strtoupper( trim( $reference ) );

        if ( '' === $reference ) {
            $order->delete_meta_data(
                '_blaines_cromwell_epos_reference'
            );
        } else {
            $order->update_meta_data(
                '_blaines_cromwell_epos_reference',
                $reference
            );
        }

        $order->save();
    },
    40
);

