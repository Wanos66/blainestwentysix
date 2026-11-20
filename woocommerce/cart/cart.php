<?php
/**
 * Cart Page
 *
 * Theme override for the Blaines transactional WooCommerce basket.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<style>
    .blaines-retail--basket {
        --basket-navy: #0e295e;
        --basket-cobalt: #215ed9;
        --basket-stone: #f3f0e9;
        --basket-rule: #aeb9cc;
    }

    .blaines-retail--basket :is(.retail-basket-items, .retail-basket-summary) {
        border: 1px solid var(--basket-rule);
    }

    .blaines-retail--basket :is(.retail-basket-items__header, .retail-basket-summary__header) {
        border: 0;
        background: var(--basket-navy);
        color: #fff;
    }

    .blaines-retail--basket :is(.retail-basket-items__header, .retail-basket-summary__header) :is(h2, p) {
        color: #fff;
    }

    .blaines-retail--basket .retail-basket-items__count {
        border: 1px solid currentColor;
    }

    .blaines-retail--basket .retail-basket-items__list {
        background: var(--basket-stone);
    }

    .blaines-retail--basket .retail-basket-item {
        border: 1px solid var(--basket-rule);
    }

    .blaines-retail--basket .retail-basket-item__commercial {
        border-top-color: var(--basket-navy);
        background: var(--basket-stone);
    }

    .blaines-retail--basket .retail-basket-services {
        border-top-color: var(--basket-rule);
    }

    .blaines-retail--basket .retail-basket-services__header {
        background: var(--basket-navy);
    }

    .blaines-retail--basket .retail-basket-services__header :is(h4, p),
    .blaines-retail--basket .retail-basket-services__header > div > p:last-child {
        color: #fff;
    }

    .blaines-retail--basket .retail-basket-services__selected-count {
        border: 1px solid currentColor;
        background: transparent;
    }

    .blaines-retail--basket .retail-basket-service:not(:has(input:checked)):hover {
        background: var(--basket-stone);
    }

    .blaines-retail--basket .retail-basket-service.is-selected:not(:has(input:checked)) {
        background: #fff;
    }

    .blaines-retail--basket .retail-basket-service:has(input:checked),
    .blaines-retail--basket .retail-basket-summary .woocommerce-shipping-methods li:has(> input:checked) {
        background: var(--basket-cobalt);
        color: #fff;
    }

    .blaines-retail--basket .retail-basket-service:has(input:checked) :is(
        .retail-basket-service__group, .retail-basket-service__name,
        .retail-basket-service__selected, .retail-basket-service__price
    ),
    .blaines-retail--basket .retail-basket-summary .woocommerce-shipping-methods li:has(> input:checked) label {
        color: #fff;
    }

    .blaines-retail--basket .retail-basket-service:has(input:checked) input,
    .blaines-retail--basket .woocommerce-shipping-methods input:checked {
        accent-color: var(--basket-navy);
        color-scheme: dark;
    }

    .blaines-retail--basket .retail-basket-service:focus-within,
    .blaines-retail--basket .woocommerce-shipping-methods li:focus-within {
        outline: 2px solid var(--basket-navy);
        outline-offset: -2px;
    }

    .blaines-retail--basket .retail-basket-service:has(input:checked) input:focus-visible {
        outline: 2px solid #fff;
    }

    .blaines-retail--basket .retail-basket-summary .woocommerce-shipping-methods li:has(> input[type="radio"]) {
        position: relative;
        align-items: center;
        min-height: 2.75rem;
        padding: .5rem .65rem;
        border: 1px solid #7c8ba5;
    }

    .blaines-retail--basket .retail-basket-summary .woocommerce-shipping-methods li:has(> input[type="radio"]) label {
        text-align: left;
        white-space: normal;
        overflow-wrap: anywhere;
        cursor: pointer;
    }

    .blaines-retail--basket .retail-basket-summary .woocommerce-shipping-methods li:has(> input[type="radio"]) label::before {
        position: absolute;
        inset: 0;
        content: "";
    }

    .blaines-retail--basket .retail-basket-summary__address-note,
    .blaines-retail--basket .retail-basket-assurance,
    .blaines-retail--basket .retail-basket-postcode-block {
        border: 0;
        background: var(--basket-stone);
        color: var(--basket-navy);
    }

    .blaines-retail--basket .retail-basket-summary__address-note {
        margin: 0 0 1rem;
        padding: .75rem .85rem;
        font-size: .875rem;
        line-height: 1.5;
    }

    .blaines-retail--basket .retail-basket-postcode-block__status {
        display: inline-block;
        padding: .5rem .65rem;
        background: var(--basket-navy);
        color: #fff;
    }

    .blaines-retail--basket .retail-basket-postcode-block h4 {
        margin-top: .65rem;
        font-size: 1.15rem;
    }

    .blaines-retail--basket [data-blaines-cart-update-status]:not([hidden]),
    .blaines-retail--basket [data-blaines-cart-postcode-result]:not(.hidden) {
        padding: .75rem 1rem;
        border: 0;
        background: var(--basket-navy);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .blaines-retail--basket [data-blaines-cart-postcode-result].bg-green-50 {
        background: #195845;
    }

    .blaines-retail--basket [data-blaines-cart-postcode-result].bg-red-50 {
        background: #8c2737;
    }

    .blaines-retail--basket .retail-basket-summary .checkout-button {
        border-color: var(--basket-cobalt) !important;
        background: var(--basket-cobalt) !important;
    }

    .blaines-retail--basket .retail-basket-summary .checkout-button:hover {
        border-color: var(--basket-navy) !important;
        background: var(--basket-navy) !important;
    }
</style>

<div class="blaines-retail blaines-retail--basket">

    <div class="retail-basket-layout">

        <div class="retail-basket-main">

            <p
                class="sticky top-0 z-10 bg-white px-4 py-2 text-sm font-semibold text-neutral-900"
                data-blaines-cart-update-status
                role="status"
                hidden
            ></p>

            <form
                class="woocommerce-cart-form retail-basket-form"
                action="<?php echo esc_url( wc_get_cart_url() ); ?>"
                method="post"
            >
                <?php do_action( 'woocommerce_before_cart_table' ); ?>

                <div class="retail-basket-items woocommerce-cart-form__contents">

                    <header class="retail-basket-items__header">
                        <div>
                            <h2>Your items</h2>
                            <p>
                                Review quantities and choose any available services before checkout.
                            </p>
                        </div>

                        <span class="retail-basket-items__count">
                            <?php
                            printf(
                                esc_html(
                                    _n(
                                        '%d item',
                                        '%d items',
                                        WC()->cart->get_cart_contents_count(),
                                        'blainestwentysix'
                                    )
                                ),
                                esc_html( WC()->cart->get_cart_contents_count() )
                            );
                            ?>
                        </span>
                    </header>

                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                    <div class="retail-basket-items__list">

                        <?php
                        $collection_blocked_by_services = function_exists( 'blaines_cart_has_selected_fulfilment_services' )
                            && blaines_cart_has_selected_fulfilment_services( WC()->cart );

                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters(
                                'woocommerce_cart_item_product',
                                $cart_item['data'],
                                $cart_item,
                                $cart_item_key
                            );
                            $product_id = apply_filters(
                                'woocommerce_cart_item_product_id',
                                $cart_item['product_id'],
                                $cart_item,
                                $cart_item_key
                            );

                            if (
                                ! $_product
                                || ! $_product->exists()
                                || $cart_item['quantity'] <= 0
                                || ! apply_filters(
                                    'woocommerce_cart_item_visible',
                                    true,
                                    $cart_item,
                                    $cart_item_key
                                )
                            ) {
                                continue;
                            }

                            $product_permalink = apply_filters(
                                'woocommerce_cart_item_permalink',
                                $_product->is_visible()
                                    ? $_product->get_permalink( $cart_item )
                                    : '',
                                $cart_item,
                                $cart_item_key
                            );

                            $product_name = apply_filters(
                                'woocommerce_cart_item_name',
                                $_product->get_name(),
                                $cart_item,
                                $cart_item_key
                            );

                            $product_price = apply_filters(
                                'woocommerce_cart_item_price',
                                WC()->cart->get_product_price( $_product ),
                                $cart_item,
                                $cart_item_key
                            );

                            $product_subtotal = apply_filters(
                                'woocommerce_cart_item_subtotal',
                                WC()->cart->get_product_subtotal(
                                    $_product,
                                    $cart_item['quantity']
                                ),
                                $cart_item,
                                $cart_item_key
                            );

                            $thumbnail = apply_filters(
                                'woocommerce_cart_item_thumbnail',
                                $_product->get_image(
                                    'woocommerce_thumbnail',
                                    [
                                        'class'    => 'retail-basket-item__product-image',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                ),
                                $cart_item,
                                $cart_item_key
                            );

                            $sku = $_product->get_sku();

                            $availability = function_exists( 'blaines_get_transactional_availability_presentation' )
                                ? blaines_get_transactional_availability_presentation( $_product, 'cart' )
                                : [];
                            $availability_message = $availability['message'] ?? '';
                            $availability_state   = $availability['state'] ?? 'unknown';

                            $availability_title = 'Availability';
                            $availability_text  = $availability_message;

                            if ( 'instock' === $availability_state ) {
                                $availability_title = 'In stock';
                                $availability_text  = preg_replace(
                                    '/^In stock\s+—\s+/i',
                                    '',
                                    $availability_message
                                );
                            } elseif ( 'backorder' === $availability_state ) {
                                $availability_title = 'Available to order';
                                $availability_text  = preg_replace(
                                    '/^Available to order\s+—\s+/i',
                                    '',
                                    $availability_message
                                );
                            } elseif ( 'outofstock' === $availability_state ) {
                                $availability_title = 'Out of stock';
                                $availability_text  =
                                    'Please contact Web Sales for availability.';
                            }

                            if ( '' === trim( (string) $availability_text ) ) {
                                $availability_text = $availability_message;
                            }

                            if ( $collection_blocked_by_services && ! empty( $availability['delivery_detail'] ) ) {
                                $availability_text = $availability['delivery_detail'];
                            }

                            if ( '' !== trim( (string) $availability_text ) ) {
                                $availability_text = ucfirst(
                                    trim( (string) $availability_text )
                                );
                            }

                            $available_services = function_exists(
                                'blaines_get_valid_cart_fulfilment_services'
                            )
                                ? blaines_get_valid_cart_fulfilment_services(
                                    $_product
                                )
                                : [];

                            $selected_services = function_exists(
                                'blaines_get_selected_cart_fulfilment_services'
                            )
                                ? blaines_get_selected_cart_fulfilment_services(
                                    $cart_item
                                )
                                : [];

                            $services_blocked_by_postcode = (
                                function_exists(
                                    'blaines_fulfilment_services_blocked_by_checked_postcode'
                                )
                                && blaines_fulfilment_services_blocked_by_checked_postcode()
                            );

                            $checked_postcode = function_exists(
                                'blaines_get_checked_product_postcode'
                            )
                                ? blaines_get_checked_product_postcode()
                                : '';

                            $item_classes = apply_filters(
                                'woocommerce_cart_item_class',
                                'woocommerce-cart-form__cart-item cart_item retail-basket-item',
                                $cart_item,
                                $cart_item_key
                            );
                            ?>

                            <article class="<?php echo esc_attr( $item_classes ); ?>">

                                <div class="retail-basket-item__core">

                                    <div class="retail-basket-item__media">

                                        <?php if ( $product_permalink ) : ?>

                                            <a
                                                href="<?php echo esc_url(
                                                    $product_permalink
                                                ); ?>"
                                                class="retail-basket-item__image-link"
                                            >
                                                <?php
                                                echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                ?>
                                            </a>

                                        <?php else : ?>

                                            <div class="retail-basket-item__image-link">
                                                <?php
                                                echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                ?>
                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <div class="retail-basket-item__details">

                                        <?php if ( $product_permalink ) : ?>

                                            <h3 class="retail-basket-item__title">
                                                <a href="<?php echo esc_url(
                                                    $product_permalink
                                                ); ?>">
                                                    <?php
                                                    echo wp_kses_post(
                                                        $product_name
                                                    );
                                                    ?>
                                                </a>
                                            </h3>

                                        <?php else : ?>

                                            <h3 class="retail-basket-item__title">
                                                <?php
                                                echo wp_kses_post(
                                                    $product_name
                                                );
                                                ?>
                                            </h3>

                                        <?php endif; ?>

                                        <?php if ( $sku ) : ?>

                                            <p class="retail-basket-item__code">
                                                <span>Product code:</span>
                                                <?php echo esc_html( $sku ); ?>
                                            </p>

                                        <?php endif; ?>

                                        <?php
                                        do_action(
                                            'woocommerce_after_cart_item_name',
                                            $cart_item,
                                            $cart_item_key
                                        );

                                        echo wc_get_formatted_cart_item_data(
                                            $cart_item
                                        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        ?>

                                        <?php if ( $availability_message ) : ?>

                                            <div class="retail-basket-availability retail-basket-availability--<?php echo esc_attr(
                                                $availability_state
                                            ); ?>">

                                                <span
                                                    class="retail-basket-availability__marker"
                                                    aria-hidden="true"
                                                ></span>

                                                <div>
                                                    <p class="retail-basket-availability__title">
                                                        <?php echo esc_html(
                                                            $availability_title
                                                        ); ?>
                                                    </p>

                                                    <p class="retail-basket-availability__text">
                                                        <?php echo esc_html(
                                                            $availability_text
                                                        ); ?>
                                                    </p>
                                                </div>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <div class="retail-basket-item__commercial">

                                        <div class="retail-basket-item__commercial-row">

                                            <div>
                                                <span class="retail-basket-item__label">
                                                    Unit price
                                                </span>

                                                <div class="retail-basket-item__price">
                                                    <?php
                                                    echo $product_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                    ?>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="retail-basket-item__label"
                                                    for="retail-cart-quantity-<?php echo esc_attr(
                                                        $cart_item_key
                                                    ); ?>"
                                                >
                                                    Quantity
                                                </label>

                                                <div class="cart-quantity-control retail-basket-item__quantity">

                                                    <?php
                                                    if ( $_product->is_sold_individually() ) {
                                                        $min_quantity = 1;
                                                        $max_quantity = 1;
                                                    } else {
                                                        $min_quantity = 0;
                                                        $max_quantity =
                                                            $_product->get_max_purchase_quantity();
                                                    }

                                                    $product_quantity =
                                                        woocommerce_quantity_input(
                                                            [
                                                                'input_id'     =>
                                                                    'retail-cart-quantity-'
                                                                    . $cart_item_key,
                                                                'input_name'   =>
                                                                    "cart[{$cart_item_key}][qty]",
                                                                'input_value'  =>
                                                                    $cart_item['quantity'],
                                                                'max_value'    =>
                                                                    $max_quantity,
                                                                'min_value'    =>
                                                                    $min_quantity,
                                                                'product_name' =>
                                                                    wp_strip_all_tags(
                                                                        $product_name
                                                                    ),
                                                                'classes'      => [
                                                                    'input-text',
                                                                    'qty',
                                                                    'text',
                                                                ],
                                                            ],
                                                            $_product,
                                                            false
                                                        );

                                                    echo apply_filters(
                                                        'woocommerce_cart_item_quantity',
                                                        $product_quantity,
                                                        $cart_item_key,
                                                        $cart_item
                                                    ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                    ?>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="retail-basket-item__subtotal">

                                            <span class="retail-basket-item__label">
                                                Item subtotal
                                            </span>

                                            <div class="retail-basket-item__subtotal-price">
                                                <?php
                                                echo $product_subtotal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                ?>
                                            </div>

                                        </div>

                                        <div class="retail-basket-item__remove">

                                            <?php
                                            echo apply_filters(
                                                'woocommerce_cart_item_remove_link',
                                                sprintf(
                                                    '<a href="%1$s" aria-label="%2$s" data-product_id="%3$s" data-product_sku="%4$s"><span aria-hidden="true">&times;</span> Remove item</a>',
                                                    esc_url(
                                                        wc_get_cart_remove_url(
                                                            $cart_item_key
                                                        )
                                                    ),
                                                    esc_attr(
                                                        sprintf(
                                                            __(
                                                                'Remove %s from basket',
                                                                'woocommerce'
                                                            ),
                                                            wp_strip_all_tags(
                                                                $product_name
                                                            )
                                                        )
                                                    ),
                                                    esc_attr( $product_id ),
                                                    esc_attr( $sku )
                                                ),
                                                $cart_item_key
                                            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            ?>

                                        </div>

                                    </div>

                                </div>

                                <?php if ( ! empty( $available_services ) ) : ?>

                                    <section class="retail-basket-services">

                                        <?php if ( $services_blocked_by_postcode ) : ?>

                                            <div class="retail-basket-postcode-block">

                                                <header>
                                                    <p class="retail-basket-postcode-block__status">
                                                        Collection only for this postcode
                                                    </p>

                                                    <h4>
                                                        Optional delivery services are unavailable
                                                    </h4>
                                                </header>

                                                <p>
                                                    <?php if ( $checked_postcode ) : ?>
                                                        Your checked postcode,
                                                        <strong><?php echo esc_html(
                                                            $checked_postcode
                                                        ); ?></strong>,
                                                        is outside our local delivery area.
                                                    <?php else : ?>
                                                        Your checked postcode is outside our local delivery area.
                                                    <?php endif; ?>
                                                    Installation, disposal, disconnection and packaging services cannot be selected for collection orders.
                                                </p>

                                                <p>
                                                    Continue to checkout for collection, check another postcode, or contact Web Sales for advice.
                                                </p>

                                                <div
                                                    class="retail-basket-postcode-checker"
                                                    data-blaines-cart-postcode-checker
                                                    data-ajax-url="<?php echo esc_url(
                                                        admin_url( 'admin-ajax.php' )
                                                    ); ?>"
                                                    data-nonce="<?php echo esc_attr(
                                                        wp_create_nonce(
                                                            'blaines_product_postcode_checker'
                                                        )
                                                    ); ?>"
                                                >
                                                    <label
                                                        for="blaines-cart-postcode-input-<?php echo esc_attr(
                                                            $cart_item_key
                                                        ); ?>"
                                                    >
                                                        Check another postcode
                                                    </label>

                                                    <div class="retail-basket-postcode-checker__controls">

                                                        <input
                                                            type="text"
                                                            id="blaines-cart-postcode-input-<?php echo esc_attr(
                                                                $cart_item_key
                                                            ); ?>"
                                                            value="<?php echo esc_attr(
                                                                $checked_postcode
                                                            ); ?>"
                                                            autocomplete="postal-code"
                                                            placeholder="e.g. NR19 2AB"
                                                            data-blaines-cart-postcode-input
                                                        >

                                                        <button
                                                            type="button"
                                                            data-blaines-cart-postcode-submit
                                                        >
                                                            Check postcode
                                                        </button>

                                                    </div>

                                                    <div
                                                        class="retail-basket-postcode-checker__result hidden"
                                                        data-blaines-cart-postcode-result
                                                        aria-live="polite"
                                                    ></div>

                                                </div>

                                                <div class="retail-basket-postcode-block__actions">

                                                    <a href="<?php echo esc_url(
                                                        home_url( '/electrical/' )
                                                    ); ?>">
                                                        Continue shopping
                                                    </a>

                                                    <a href="tel:01760302111">
                                                        Call Web Sales
                                                    </a>

                                                </div>

                                            </div>

                                        <?php else : ?>

                                            <header class="retail-basket-services__header">

                                                <div>
                                                    <p class="retail-basket-services__label">
                                                        Optional services
                                                    </p>

                                                    <h4>
                                                        Services for this product
                                                    </h4>

                                                    <p>
                                                        Select any services you need for local delivery.
                                                    </p>
                                                </div>

                                                <?php if ( ! empty( $selected_services ) ) : ?>

                                                    <span class="retail-basket-services__selected-count">
                                                        <?php
                                                        printf(
                                                            esc_html(
                                                                _n(
                                                                    '%d selected',
                                                                    '%d selected',
                                                                    count( $selected_services ),
                                                                    'blainestwentysix'
                                                                )
                                                            ),
                                                            esc_html(
                                                                count(
                                                                    $selected_services
                                                                )
                                                            )
                                                        );
                                                        ?>
                                                    </span>

                                                <?php endif; ?>

                                            </header>

                                            <div class="retail-basket-services__list">

                                                <?php
                                                foreach (
                                                    $available_services
                                                    as $service_code => $service
                                                ) :
                                                    $service_label = isset(
                                                        $service['label']
                                                    )
                                                        ? (string) $service['label']
                                                        : $service_code;

                                                    $service_group = isset(
                                                        $service['group']
                                                    )
                                                        ? (string) $service['group']
                                                        : '';

                                                    $service_price = isset(
                                                        $service['price']
                                                    )
                                                        ? (float) $service['price']
                                                        : 0.00;

                                                    $is_selected = in_array(
                                                        $service_code,
                                                        $selected_services,
                                                        true
                                                    );
                                                    ?>

                                                    <label class="retail-basket-service<?php echo $is_selected ? ' is-selected' : ''; ?>">

                                                        <span class="retail-basket-service__control">

                                                            <input
                                                                type="checkbox"
                                                                name="blaines_fulfilment_services[<?php echo esc_attr(
                                                                    $cart_item_key
                                                                ); ?>][]"
                                                                value="<?php echo esc_attr(
                                                                    $service_code
                                                                ); ?>"
                                                                <?php checked(
                                                                    $is_selected
                                                                ); ?>
                                                            >

                                                        </span>

                                                        <span class="retail-basket-service__content">

                                                            <?php if ( $service_group ) : ?>

                                                                <span class="retail-basket-service__group">
                                                                    <?php echo esc_html(
                                                                        ucfirst(
                                                                            $service_group
                                                                        )
                                                                    ); ?>
                                                                </span>

                                                            <?php endif; ?>

                                                            <span class="retail-basket-service__name">
                                                                <?php echo esc_html(
                                                                    $service_label
                                                                ); ?>
                                                            </span>

                                                            <?php if ( $is_selected ) : ?>

                                                                <span class="retail-basket-service__selected">
                                                                    Selected
                                                                </span>

                                                            <?php endif; ?>

                                                        </span>

                                                        <span class="retail-basket-service__price">
                                                            <?php
                                                            echo wp_kses_post(
                                                                wc_price(
                                                                    $service_price
                                                                )
                                                            );
                                                            ?>
                                                        </span>

                                                    </label>

                                                <?php endforeach; ?>

                                            </div>

                                        <?php endif; ?>

                                    </section>

                                <?php endif; ?>

                            </article>

                        <?php } ?>

                    </div>

                    <?php do_action( 'woocommerce_cart_contents' ); ?>

                </div>

                <div class="retail-basket-actions">

                    <?php if ( wc_coupons_enabled() ) : ?>

                        <div class="retail-basket-coupon">

                            <label for="coupon_code">
                                Have a discount code?
                            </label>

                            <div>
                                <input
                                    type="text"
                                    name="coupon_code"
                                    class="input-text"
                                    id="coupon_code"
                                    value=""
                                    placeholder="<?php esc_attr_e(
                                        'Enter coupon code',
                                        'woocommerce'
                                    ); ?>"
                                >

                                <button
                                    type="submit"
                                    class="button"
                                    name="apply_coupon"
                                    value="<?php esc_attr_e(
                                        'Apply coupon',
                                        'woocommerce'
                                    ); ?>"
                                >
                                    <?php esc_html_e(
                                        'Apply code',
                                        'woocommerce'
                                    ); ?>
                                </button>
                            </div>

                            <?php do_action( 'woocommerce_cart_coupon' ); ?>

                        </div>

                    <?php endif; ?>

                    <div class="retail-basket-update">

                        <p>
                            <?php esc_html_e(
                                'Quantities and services update automatically. Use this button if an update doesn’t go through.',
                                'blainestwentysix'
                            ); ?>
                        </p>

                        <button
                            type="submit"
                            class="button"
                            name="update_cart"
                            value="<?php esc_attr_e(
                                'Update basket',
                                'woocommerce'
                            ); ?>"
                        >
                            <?php esc_html_e(
                                'Update basket',
                                'woocommerce'
                            ); ?>
                        </button>

                        <?php do_action( 'woocommerce_cart_actions' ); ?>

                        <?php
                        wp_nonce_field(
                            'woocommerce-cart',
                            'woocommerce-cart-nonce'
                        );
                        ?>

                    </div>

                </div>

                <?php do_action( 'woocommerce_after_cart_contents' ); ?>

                <?php do_action( 'woocommerce_after_cart_table' ); ?>

            </form>

        </div>

        <aside class="retail-basket-sidebar">

            <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

            <div class="retail-basket-summary">

                <header class="retail-basket-summary__header">
                    <p>Secure checkout</p>
                    <h2>Order summary</h2>
                </header>

                <div class="retail-basket-summary__content">

                    <p class="retail-basket-summary__address-note">
                        <?php esc_html_e( 'Buying for someone else? At checkout, you can enter their delivery address and keep your own billing address.', 'blainestwentysix' ); ?>
                    </p>

                    <?php
                    if ( function_exists( 'woocommerce_cart_totals' ) ) {
                        woocommerce_cart_totals();
                    }
                    ?>

                </div>

                <a
                    class="retail-basket-summary__continue-shopping"
                    href="<?php echo esc_url( home_url( '/electrical/' ) ); ?>"
                >
                    <span aria-hidden="true">&larr;</span>
                    <?php esc_html_e( 'Continue shopping', 'blainestwentysix' ); ?>
                </a>

            </div>

            <div class="retail-basket-assurance">

                <h3>Delivery, collection and services</h3>

                <p>
                    Local delivery and optional services depend on the delivery postcode.
                </p>

                <a href="tel:01760302111">
                    Web Sales: 01760 302111
                </a>

            </div>

        </aside>

    </div>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
