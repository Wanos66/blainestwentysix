<?php
/**
 * Blaines Ready for Collection customer email.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

class WC_Email_Blaines_Ready_For_Collection extends WC_Email {

    public function __construct() {

        $this->id             = 'blaines_ready_for_collection';
        $this->customer_email = true;

        $this->title       = 'Ready for Collection';
        $this->description = 'Sent manually when a showroom collection order is ready.';

        $this->heading    = 'Your order is ready for collection!';
        $this->subject    = 'Your Blaines order is ready for collection';
        $this->email_type = 'html';

        // No automatic triggers at this stage.
        parent::__construct();
    }

    /**
     * Send the Ready for Collection notification.
     *
     * @param int $order_id WooCommerce order ID.
     * @return bool Whether the email was submitted successfully.
     */
    public function trigger( $order_id ) {

        $order = wc_get_order( $order_id );

        if ( ! $order instanceof WC_Order ) {
            return false;
        }

        // Only accepted collection orders are eligible.
        if ( ! $order->has_status( 'order-confirmed' ) ) {
            return false;
        }

        $is_collection = false;

        foreach ( $order->get_items( 'shipping' ) as $shipping_item ) {
            if ( 'local_pickup' === $shipping_item->get_method_id() ) {
                $is_collection = true;
                break;
            }
        }

        if ( ! $is_collection ) {
            return false;
        }

        // Prevent duplicate notifications.
        if ( $order->get_meta(
            '_blaines_ready_for_collection_email_sent',
            true
        ) ) {
            return false;
        }

        $this->object    = $order;
        $this->recipient = $order->get_billing_email();

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return false;
        }

        $sent = $this->send(
            $this->get_recipient(),
            $this->get_subject(),
            $this->get_content(),
            $this->get_headers(),
            $this->get_attachments()
        );

        if ( $sent ) {

            $order->update_meta_data(
                '_blaines_ready_for_collection_email_sent',
                'yes'
            );

            $order->add_order_note(
                'Ready for Collection email submitted successfully.'
            );

            $order->save();
        }

        return (bool) $sent;
    }


    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'no',
            ),
            'subject' => array(
                'title'   => 'Subject',
                'type'    => 'text',
                'default' => $this->subject,
            ),
            'heading' => array(
                'title'   => 'Email heading',
                'type'    => 'text',
                'default' => $this->heading,
            ),
            'email_type' => array(
                'title'   => 'Email type',
                'type'    => 'select',
                'default' => 'html',
                'options' => array(
                    'html' => 'HTML',
                ),
            ),
        );
    }

    public function get_content_html() {

        $order = $this->object instanceof WC_Order
            ? $this->object
            : null;

        ob_start();

        do_action(
            'woocommerce_email_header',
            $this->get_heading(),
            $this
        );

        if ( $order ) {

            $first_name = $order->get_billing_first_name() ?: 'there';

            $location = (string) $order->get_meta(
                '_blaines_collection_location',
                true
            );

            $showroom = blaines_get_showroom_details( $location );

            $showroom_name = $showroom['name'] ?? '';
            $address       = $showroom['address'] ?? '';
            $phone         = $showroom['phone'] ?? '';
            $opening_hours = $showroom['opening_hours'] ?? array();

            ?>
            <p>Hi <?php echo esc_html( $first_name ); ?>,</p>

            <p>
                Good news! Your Blaines Electrical order
                <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
                is now ready for collection.
            </p>

            <h2>Your collection details</h2>

            <?php if ( $showroom_name ) : ?>
                <p>
                    <strong>Collection showroom:</strong>
                    Blaines <?php echo esc_html( $showroom_name ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $address ) : ?>
                <p>
                    <strong>Collection address:</strong><br>
                    <?php echo nl2br( esc_html( $address ) ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $phone ) : ?>
                <p>
                    <strong>Showroom telephone:</strong>
                    <?php echo esc_html( $phone ); ?>
                </p>
            <?php endif; ?>

            <?php if ( ! empty( $opening_hours ) ) : ?>
                <p><strong>Showroom opening hours:</strong></p>

                <p>
                    <?php foreach ( $opening_hours as $row ) : ?>
                        <?php
                        $day = trim( (string) ( $row['day'] ?? '' ) );
                        $hours = trim( (string) ( $row['hours'] ?? '' ) );

                        if ( ! $day && ! $hours ) {
                            continue;
                        }
                        ?>

                        <?php echo esc_html( trim( $day . ' ' . $hours ) ); ?><br>

                    <?php endforeach; ?>
                </p>
            <?php endif; ?>

            <p>
                Your goods are ready and you may now arrange
                to collect them during the showroom's opening hours.
            </p>

            <h2>What to bring</h2>

            <p>
                Please bring your order confirmation,
                either printed or on your phone,
                together with suitable photographic identification.
            </p>

            <p>
                If you're collecting a large appliance,
                please arrange suitable transport and
                assistance with loading and securing your goods.
            </p>

            <p>
                If someone else will be collecting on your behalf,
                please contact the showroom beforehand
                to agree the collection arrangements.
            </p>

            <p>
                We look forward to seeing you!
            </p>

            <p>
                The Blaines Team
            </p>
            <?php

        } else {
            ?>
            <p>Your Blaines order is ready for collection.</p>
            <p>Your showroom collection details will appear here.</p>
            <?php
        }

        do_action(
            'woocommerce_email_footer',
            $this
        );

        return ob_get_clean();
    }
}

