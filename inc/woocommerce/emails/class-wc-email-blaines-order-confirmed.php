<?php
/**
 * Blaines Order Confirmed customer email.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

class WC_Email_Blaines_Order_Confirmed extends WC_Email {

    /**
     * Set up the email.
     */
    public function __construct() {

        $this->id             = 'blaines_order_confirmed';
        $this->customer_email = true;

        $this->title = 'Order Confirmed';

        $this->description = 'Sent to customers when Blaines '
                             . 'reviews and accepts an order.';

        $this->heading = 'Your order is confirmed!';
        $this->subject = 'Your Blaines order is confirmed';


        $this->email_type = 'html';

        /**
         * Trigger when an order enters Order Confirmed.
         */
        add_action(
            'woocommerce_order_status_order-confirmed',
            array( $this, 'trigger' ),
            10,
            2
        );

        parent::__construct();
    }

    /**
     * Settings displayed in WooCommerce > Settings > Emails.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'no',
            ),
            'subject' => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => 'Email subject line.',
                'placeholder' => $this->subject,
                'default'     => $this->subject,
            ),
            'heading' => array(
                'title'       => 'Email heading',
                'type'        => 'text',
                'description' => 'Main heading inside the email.',
                'placeholder' => $this->heading,
                'default'     => $this->heading,
            ),

            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => 'Choose the format of the email.',
                'default'     => 'html',
                'options'     => array(
                    'html' => 'HTML',
                ),
            ),

        );
    }


    /**
     * Trigger the Order Confirmed email.
     *
     * @param int            $order_id WooCommerce order ID.
     * @param WC_Order|false $order    Order object.
     */
    public function trigger( $order_id, $order = false ) {

        if ( ! $order_id ) {
            return;
        }

        $this->object = $order instanceof WC_Order
            ? $order
            : wc_get_order( $order_id );

        if ( ! $this->object ) {
            return;
        }

        // Avoid automatically sending a second confirmation.
        if ( $this->object->get_meta(
            '_blaines_order_confirmed_email_sent',
            true
        ) ) {
            return;
        }

        $this->recipient = $this->object->get_billing_email();

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }

        $sent = $this->send(
            $this->get_recipient(),
            $this->get_subject(),
            $this->get_content(),
            $this->get_headers(),
            $this->get_attachments()
        );

        // Record successful submission to the mail transport.
        if ( $sent ) {
            $this->object->update_meta_data(
                '_blaines_order_confirmed_email_sent',
                'yes'
            );

            $this->object->save();
        }
    }


    /**
     * Generate the HTML content for the Order Confirmed email.
     *
     * @return string
     */
    public function get_content_html() {

        $order = $this->object instanceof WC_Order
            ? $this->object
            : null;

        ob_start();

        // Standard WooCommerce branded email header.
        do_action(
            'woocommerce_email_header',
            $this->get_heading(),
            $this
        );

        if ( $order ) {

            $first_name = $order->get_billing_first_name();

            if ( empty( $first_name ) ) {
                $first_name = 'there';
            }

            ?>
            <p>
                <?php
                printf(
                    esc_html__( 'Hi %s,', 'blainestwentysix' ),
                    esc_html( $first_name )
                );
                ?>
            </p>

            <p>
                Thank you for choosing Blaines Electrical.
            </p>

            <p>
                We've checked and accepted your order,
                and your purchase is now confirmed.
            </p>

            <?php
            $is_collection = false;

            foreach ( $order->get_items( 'shipping' ) as $shipping_item ) {
                if ( 'local_pickup' === $shipping_item->get_method_id() ) {
                    $is_collection = true;
                    break;
                }
            }

            if ( $is_collection ) :

                $showroom = $order->get_meta(
                    '_blaines_collection_location_label',
                    true
                );

                $collection_slug = $order->get_meta(
                    '_blaines_collection_location',
                    true
                );

                $showroom_details = function_exists( 'blaines_get_showroom_details' )
                    ? blaines_get_showroom_details( (string) $collection_slug )
                    : null;

                $showroom_address = $showroom_details['address'] ?? '';

                $collection_date = $order->get_meta(
                    '_blaines_expected_collection_label',
                    true
                );
                ?>

                <h2>Your showroom collection</h2>

                <?php if ( $showroom ) : ?>
                <p>
                    <strong>Collection showroom:</strong>
                    Blaines <?php echo esc_html( $showroom ); ?>
                </p>
            <?php endif; ?>

                <?php if ( $showroom_address ) : ?>
                <p>
                    <strong>Collection address:</strong><br>
                    <?php echo nl2br( esc_html( $showroom_address ) ); ?>
                </p>
            <?php endif; ?>

                <?php if ( $collection_date ) : ?>
                <p>
                    <strong>Expected collection:</strong>
                    <?php echo esc_html( $collection_date ); ?>
                </p>
            <?php endif; ?>

                <p>
                    <strong>Please wait until we confirm
                        your order is ready for collection.</strong>
                </p>

                <p>
                    The collection date and time shown above
                    are estimates. Your showroom will contact
                    you when your goods are ready.
                    Please do not travel to collect your order
                    until you have received this confirmation.
                </p>

                <p>
                    When collecting, please bring your order
                    confirmation (printed or on your phone)
                    and suitable photographic identification.
                    For larger appliances, please arrange
                    suitable transport and loading assistance.
                </p>

            <?php else :

                $delivery_date = $order->get_meta(
                    '_blaines_preferred_delivery_date_label',
                    true
                );
                ?>

                <h2>Your local delivery</h2>

                <?php if ( $delivery_date ) : ?>
                <p>
                    <strong>Preferred delivery date:</strong>
                    <?php echo esc_html( $delivery_date ); ?>
                </p>
            <?php endif; ?>

                <p>
                    We'll arrange delivery to the address
                    provided with your order.
                </p>

                <p>
                    Please ensure someone is available
                    to receive your delivery.
                    We'll contact you if your delivery
                    arrangements need to change.
                </p>

            <?php endif; ?>


            <p>
                Here's a reminder of what you've ordered:
            </p>

            <?php

            // WooCommerce order table, totals and services.
            do_action(
                'woocommerce_email_order_details',
                $order,
                false,
                false,
                $this
            );

            // Standard WooCommerce customer information.
            do_action(
                'woocommerce_email_customer_details',
                $order,
                false,
                false,
                $this
            );

            ?>

            <p>
                Thank you for shopping with Blaines.
            </p>

            <p>
                The Blaines Team
            </p>

            <?php

        } else {

            // Safe fallback for previews before an order is loaded.
            ?>
            <p>Hi Customer,</p>

            <p>Thank you for choosing Blaines Electrical.</p>

            <p>
                We've checked and accepted your order,
                and your purchase is now confirmed.
            </p>

            <p>
                We'll arrange fulfilment in accordance
                with your selected delivery or collection
                arrangements.
            </p>

            <p>Here's a reminder of what you've ordered:</p>

            <p><em>Order details will appear here.</em></p>

            <p>
                Thank you for shopping with Blaines.
            </p>

            <p>The Blaines Team</p>
            <?php
        }

        // Standard WooCommerce branded email footer.
        do_action(
            'woocommerce_email_footer',
            $this
        );

        return ob_get_clean();
    }

}

