<?php
/**
 * Order received / thank you page.
 *
 * @package blainestwentysix
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="blaines-retail blaines-retail--order-confirmation blaines-order-confirmation">

    <?php if ( $order ) : ?>

        <?php
        $order_id           = $order->get_id();
        $order_number       = $order->get_order_number();
        $order_date         = $order->get_date_created();
        $order_email        = $order->get_billing_email();
        $order_total        = $order->get_formatted_order_total();
        $payment_method     = trim( (string) $order->get_payment_method_title() );
        $payment_method_id  = (string) $order->get_payment_method();
        $fulfilment_method  = trim( (string) $order->get_shipping_method() );
        $needs_payment      = $order->needs_payment();
        $has_failed_payment = $order->has_status( 'failed' );

        $preferred_delivery_date = function_exists(
            'blaines_get_order_preferred_delivery_date_label'
        )
            ? blaines_get_order_preferred_delivery_date_label( $order )
            : '';

        $can_view_account_orders = (
            is_user_logged_in()
            && (int) $order->get_user_id() === get_current_user_id()
            && function_exists( 'wc_get_account_endpoint_url' )
        );

        $account_orders_url = $can_view_account_orders
            ? wc_get_account_endpoint_url( 'orders' )
            : '';
        ?>

        <?php do_action( 'woocommerce_before_thankyou', $order_id ); ?>

        <?php if ( $has_failed_payment ) : ?>

            <section
                class="retail-order-status retail-order-status--failed"
                aria-labelledby="retail-order-failed-title"
            >
                <div class="retail-order-status__marker" aria-hidden="true">
                    !
                </div>

                <div class="retail-order-status__content">

                    <p class="retail-order-status__label">
                        Payment issue
                    </p>

                    <h2 id="retail-order-failed-title">
                        Your order could not be completed
                    </h2>

                    <p>
                        Unfortunately your payment could not be processed. Please try again or contact Web Sales if you need help completing your order.
                    </p>

                    <dl class="retail-order-status__facts">

                        <div>
                            <dt>Order number</dt>
                            <dd><?php echo esc_html( $order_number ); ?></dd>
                        </div>

                        <div>
                            <dt>Total</dt>
                            <dd><?php echo wp_kses_post( $order_total ); ?></dd>
                        </div>

                    </dl>

                    <div class="retail-order-status__actions">

                        <?php if ( $needs_payment ) : ?>

                            <a
                                href="<?php echo esc_url(
                                    $order->get_checkout_payment_url()
                                ); ?>"
                                class="retail-order-button retail-order-button--primary"
                            >
                                Try payment again
                            </a>

                        <?php endif; ?>

                        <a
                            href="tel:01760302111"
                            class="retail-order-button retail-order-button--secondary"
                        >
                            Call Web Sales
                        </a>

                        <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>

                            <a
                                href="<?php echo esc_url(
                                    wc_get_page_permalink( 'shop' )
                                ); ?>"
                                class="retail-order-button retail-order-button--secondary"
                            >
                                Return to shop
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </section>

        <?php else : ?>

            <section
                class="retail-order-received"
                aria-labelledby="retail-order-received-title"
            >
                <div class="retail-order-received__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" focusable="false">
                        <path
                            d="m5.5 12.5 4 4 9-9"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <div class="retail-order-received__content">

                    <p class="retail-order-received__label">
                        Order received
                    </p>

                    <h2 id="retail-order-received-title">
                        Thank you — we have received your order
                    </h2>

                    <p>
                        A confirmation email has been sent to you. Web Sales will review your products, selected services and fulfilment details, then contact you if anything needs confirming.
                    </p>

                </div>

                <dl class="retail-order-received__facts">

                    <div>
                        <dt>Order number</dt>
                        <dd><?php echo esc_html( $order_number ); ?></dd>
                    </div>

                    <?php if ( $order_date ) : ?>

                        <div>
                            <dt>Date</dt>
                            <dd>
                                <?php echo esc_html(
                                    wc_format_datetime( $order_date )
                                ); ?>
                            </dd>
                        </div>

                    <?php endif; ?>

                    <?php if ( $order_email ) : ?>

                        <div>
                            <dt>Email</dt>
                            <dd><?php echo esc_html( $order_email ); ?></dd>
                        </div>

                    <?php endif; ?>

                    <div>
                        <dt>Total</dt>
                        <dd><?php echo wp_kses_post( $order_total ); ?></dd>
                    </div>

                </dl>

            </section>

            <div class="retail-order-layout">

                <main class="retail-order-main">

                    <section
                        class="retail-order-section retail-order-next"
                        aria-labelledby="retail-order-next-title"
                    >
                        <header class="retail-order-section__header">

                            <p>What happens next?</p>

                            <h2 id="retail-order-next-title">
                                Your order is now with Web Sales
                            </h2>

                        </header>

                        <ol class="retail-order-next__steps">

                            <li>
                                <span>1</span>

                                <div>
                                    <h3>Order review</h3>

                                    <p>
                                        We will check product availability, your fulfilment choice and any selected services.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <span>2</span>

                                <div>
                                    <h3>Arrangements confirmed</h3>

                                    <p>
                                        Delivery or collection arrangements will be reviewed and confirmed where required.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <span>3</span>

                                <div>
                                    <h3>Services coordinated</h3>

                                    <p>
                                        Installation, disconnection, disposal or packaging services will be coordinated with fulfilment.
                                    </p>
                                </div>
                            </li>

                        </ol>

                    </section>

                    <section
                        class="retail-order-section retail-order-products"
                        aria-labelledby="retail-order-products-title"
                    >
                        <header class="retail-order-section__header">

                            <p>Purchased products</p>

                            <h2 id="retail-order-products-title">
                                Order details
                            </h2>

                        </header>

                        <div class="retail-order-products__list">

                            <?php foreach ( $order->get_items() as $item_id => $item ) : ?>

                                <?php
                                $product      = $item->get_product();
                                $product_name = $item->get_name();
                                $quantity     = $item->get_quantity();
                                $line_total   = $order->get_formatted_line_subtotal(
                                    $item
                                );

                                $selected_services = wc_get_order_item_meta(
                                    $item_id,
                                    'Selected fulfilment services',
                                    true
                                );

                                $product_url = (
                                    $product instanceof WC_Product
                                    && $product->is_visible()
                                )
                                    ? $product->get_permalink()
                                    : '';

                                if ( $product instanceof WC_Product ) {
                                    $product_image = $product->get_image(
                                        'woocommerce_thumbnail',
                                        [
                                            'class'    => 'retail-order-product__image',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]
                                    );
                                } else {
                                    $product_image = wc_placeholder_img(
                                        'woocommerce_thumbnail',
                                        [
                                            'class'    => 'retail-order-product__image',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]
                                    );
                                }
                                ?>

                                <article class="retail-order-product">

                                    <div class="retail-order-product__media">

                                        <?php if ( $product_url ) : ?>

                                            <a href="<?php echo esc_url(
                                                $product_url
                                            ); ?>">
                                                <?php
                                                echo $product_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                ?>
                                            </a>

                                        <?php else : ?>

                                            <?php
                                            echo $product_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            ?>

                                        <?php endif; ?>

                                    </div>

                                    <div class="retail-order-product__content">

                                        <h3>

                                            <?php if ( $product_url ) : ?>

                                                <a href="<?php echo esc_url(
                                                    $product_url
                                                ); ?>">
                                                    <?php echo esc_html(
                                                        $product_name
                                                    ); ?>
                                                </a>

                                            <?php else : ?>

                                                <?php echo esc_html(
                                                    $product_name
                                                ); ?>

                                            <?php endif; ?>

                                            <span>
                                                × <?php echo esc_html(
                                                    (string) $quantity
                                                ); ?>
                                            </span>

                                        </h3>

                                        <?php if (
                                            $product instanceof WC_Product
                                            && $product->get_sku()
                                        ) : ?>

                                            <p class="retail-order-product__code">
                                                <span>Product code:</span>
                                                <?php echo esc_html(
                                                    $product->get_sku()
                                                ); ?>
                                            </p>

                                        <?php endif; ?>

                                        <?php if ( $selected_services ) : ?>

                                            <div class="retail-order-product__services">

                                                <p>Services requested</p>

                                                <div>
                                                    <?php
                                                    $services = array_filter(
                                                        array_map(
                                                            'trim',
                                                            explode(
                                                                ', ',
                                                                wp_strip_all_tags(
                                                                    (string) $selected_services
                                                                )
                                                            )
                                                        )
                                                    );
                                                    ?>

                                                    <?php if ( $services ) : ?>

                                                        <ul>

                                                            <?php foreach ( $services as $service ) : ?>

                                                                <li>
                                                                    <?php echo esc_html(
                                                                        $service
                                                                    ); ?>
                                                                </li>

                                                            <?php endforeach; ?>

                                                        </ul>

                                                    <?php else : ?>

                                                        <p>
                                                            <?php echo esc_html(
                                                                (string) $selected_services
                                                            ); ?>
                                                        </p>

                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <div class="retail-order-product__total">

                                        <span>Line total</span>

                                        <p>
                                            <?php echo wp_kses_post(
                                                $line_total
                                            ); ?>
                                        </p>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                    <section
                        class="retail-order-addresses"
                        aria-label="Customer addresses"
                    >
                        <article class="retail-order-address">

                            <header>
                                <p>Customer details</p>
                                <h2>Billing details</h2>
                            </header>

                            <address>
                                <?php
                                echo wp_kses_post(
                                    $order->get_formatted_billing_address()
                                );
                                ?>

                                <?php if ( $order->get_billing_phone() ) : ?>

                                    <p>
                                        <a href="tel:<?php echo esc_attr(
                                            preg_replace(
                                                '/[^0-9+]/',
                                                '',
                                                $order->get_billing_phone()
                                            )
                                        ); ?>">
                                            <?php echo esc_html(
                                                $order->get_billing_phone()
                                            ); ?>
                                        </a>
                                    </p>

                                <?php endif; ?>

                                <?php if ( $order_email ) : ?>

                                    <p>
                                        <a href="mailto:<?php echo esc_attr(
                                            $order_email
                                        ); ?>">
                                            <?php echo esc_html(
                                                $order_email
                                            ); ?>
                                        </a>
                                    </p>

                                <?php endif; ?>

                            </address>

                        </article>

                        <article class="retail-order-address">

                            <header>
                                <p>Fulfilment address</p>
                                <h2>Delivery or collection details</h2>
                            </header>

                            <div class="retail-order-address__content">

                                <?php if (
                                    $order->get_formatted_shipping_address()
                                ) : ?>

                                    <address>
                                        <?php
                                        echo wp_kses_post(
                                            $order->get_formatted_shipping_address()
                                        );
                                        ?>
                                    </address>

                                <?php else : ?>

                                    <p>
                                        Delivery or collection details will be confirmed by Web Sales where required.
                                    </p>

                                <?php endif; ?>

                            </div>

                        </article>

                    </section>

                </main>

                <aside class="retail-order-sidebar">

                    <section class="retail-order-summary">

                        <header>
                            <p>Order confirmation</p>
                            <h2>Order summary</h2>
                        </header>

                        <dl class="retail-order-summary__facts">

                            <div>
                                <dt>Order number</dt>
                                <dd><?php echo esc_html( $order_number ); ?></dd>
                            </div>

                            <?php if ( $payment_method ) : ?>

                                <div>
                                    <dt>Payment method</dt>
                                    <dd><?php echo esc_html(
                                        $payment_method
                                    ); ?></dd>
                                </div>

                            <?php endif; ?>

                            <?php if ( $fulfilment_method ) : ?>

                                <div>
                                    <dt>Fulfilment</dt>
                                    <dd><?php echo esc_html(
                                        $fulfilment_method
                                    ); ?></dd>
                                </div>

                            <?php endif; ?>

                            <?php if ( $preferred_delivery_date ) : ?>

                                <div>
                                    <dt>Preferred delivery date</dt>
                                    <dd><?php echo esc_html(
                                        $preferred_delivery_date
                                    ); ?></dd>
                                </div>

                            <?php endif; ?>

                        </dl>

                        <div class="retail-order-summary__totals">

                            <?php
                            foreach (
                                $order->get_order_item_totals()
                                as $key => $total
                            ) :
                                if ( 'cart_subtotal' === $key ) {
                                    continue;
                                }
                                ?>

                                <div class="<?php echo 'order_total' === $key
                                    ? 'is-total'
                                    : ''; ?>">

                                    <dt>
                                        <?php echo esc_html(
                                            wp_strip_all_tags(
                                                $total['label']
                                            )
                                        ); ?>
                                    </dt>

                                    <dd>
                                        <?php echo wp_kses_post(
                                            $total['value']
                                        ); ?>
                                    </dd>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <?php if (
                            $can_view_account_orders
                            && $account_orders_url
                        ) : ?>

                            <a
                                href="<?php echo esc_url(
                                    $account_orders_url
                                ); ?>"
                                class="retail-order-summary__account-link"
                            >
                                View your orders
                            </a>

                        <?php endif; ?>

                    </section>

                    <section class="retail-order-help">

                        <p>Need help?</p>

                        <h2>Speak to Web Sales</h2>

                        <p>
                            Contact us if you need to discuss your order. Please quote order number
                            <strong><?php echo esc_html(
                                $order_number
                            ); ?></strong>.
                        </p>

                        <a href="tel:01760302111">
                            01760 302111
                        </a>

                    </section>

                </aside>

            </div>

            <?php
            /**
             * Preserve gateway / tracking thank-you hooks while preventing
             * WooCommerce's default order details table from being output a
             * second time.
             */
            remove_action(
                'woocommerce_thankyou',
                'woocommerce_order_details_table',
                10
            );

            /**
             * Cash on Delivery is local-development only for this project, so
             * suppress its default payment instruction in the custom layout.
             * Staging/live gateways may still output thank-you content.
             */
            if ( 'cod' !== $payment_method_id ) {
                do_action(
                    'woocommerce_thankyou_' . $payment_method_id,
                    $order_id
                );
            }

            /**
             * Retain the generic hook for analytics, tracking, CRM and export
             * integrations.
             */
            do_action( 'woocommerce_thankyou', $order_id );
            ?>

        <?php endif; ?>

    <?php else : ?>

        <section
            class="retail-order-status retail-order-status--fallback"
            aria-labelledby="retail-order-fallback-title"
        >
            <div class="retail-order-status__marker" aria-hidden="true">
                ✓
            </div>

            <div class="retail-order-status__content">

                <p class="retail-order-status__label">
                    Order confirmation
                </p>

                <h2 id="retail-order-fallback-title">
                    Thank you
                </h2>

                <p>
                    Thank you. Your order has been received.
                </p>

                <div class="retail-order-status__actions">

                    <a
                        href="<?php echo esc_url(
                            home_url( '/electrical/' )
                        ); ?>"
                        class="retail-order-button retail-order-button--primary"
                    >
                        Shop electrical
                    </a>

                </div>

            </div>

        </section>

    <?php endif; ?>

</div>
