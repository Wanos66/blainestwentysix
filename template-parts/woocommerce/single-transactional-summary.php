<?php
/**
 * Unified transactional single-product purchase panel.
 *
 * AO-inspired containment using Blaines typography, colours and square corners.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
    return;
}

$product_id    = $product->get_id();
$product_title = get_the_title( $product_id );

$key_features = function_exists( 'blaines_get_product_key_features' )
    ? blaines_get_product_key_features( $product_id )
    : [];

$key_features = array_values(
    array_filter(
        is_array( $key_features ) ? $key_features : [],
        static function ( $feature ) {
            return is_scalar( $feature ) && '' !== trim( (string) $feature );
        }
    )
);

$is_agency_product = function_exists( 'get_field' )
    ? (bool) get_field( 'product_is_agency_product', $product_id )
    : (bool) get_post_meta( $product_id, 'product_is_agency_product', true );

$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$current_price = (float) $product->get_price();
$is_on_sale    = $product->is_on_sale()
    && $sale_price > 0
    && $regular_price > $sale_price;
$saving        = $is_on_sale ? $regular_price - $sale_price : 0;
$saving_decimals = ( $saving > 0 && floor( $saving ) === $saving ) ? 0 : wc_get_price_decimals();

$energy_rating_image_id = function_exists( 'blaines_get_product_energy_rating_image_id' )
    ? (int) blaines_get_product_energy_rating_image_id( $product_id )
    : 0;

$full_energy_label = function_exists( 'blaines_get_product_full_energy_label_resource' )
    ? blaines_get_product_full_energy_label_resource( $product_id )
    : [];

$product_information_sheet = function_exists( 'blaines_get_product_information_sheet_resource' )
    ? blaines_get_product_information_sheet_resource( $product_id )
    : [];

$full_energy_label_url = isset( $full_energy_label['url'] )
    ? trim( (string) $full_energy_label['url'] )
    : '';

$full_energy_label_is_image = ! empty( $full_energy_label['is_image'] );

$product_information_sheet_url = isset( $product_information_sheet['url'] )
    ? trim( (string) $product_information_sheet['url'] )
    : '';

$has_energy_information = $energy_rating_image_id
    || '' !== $full_energy_label_url
    || '' !== $product_information_sheet_url;

$available_services = function_exists( 'blaines_get_product_fulfilment_services' )
    ? blaines_get_product_fulfilment_services( $product )
    : [];

$available_services = array_values(
    array_filter(
        is_array( $available_services ) ? $available_services : [],
        static function ( $service ) {
            return is_array( $service )
                && isset( $service['label'], $service['price'] )
                && '' !== trim( (string) $service['label'] )
                && is_numeric( $service['price'] );
        }
    )
);

$product_promotions = function_exists( 'blaines_get_product_promotions' )
    ? blaines_get_product_promotions( $product_id, true )
    : [];

$primary_promotion = $product_promotions[0] ?? null;
$short_description = trim( (string) $product->get_short_description() );
?>

<section
    class="retail-purchase-panel overflow-hidden border border-neutral-200 bg-white font-body shadow-[0_14px_36px_rgba(15,23,42,0.13)] lg:shadow-[0_18px_44px_rgba(15,23,42,0.14)]"
    aria-label="Purchase this product"
>
    <div class="retail-purchase-panel__pricing px-5 py-5 sm:px-6 sm:py-6 lg:px-7 lg:py-7">
        <div class="retail-purchase-panel__pricing-layout flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
            <div class="min-w-0">
                <?php if ( $is_on_sale ) : ?>
                    <p class="text-sm font-medium text-neutral-500">
                        Was
                        <span class="line-through">
                            <?php echo wp_kses_post( wc_price( $regular_price ) ); ?>
                        </span>
                    </p>

                    <p class="mt-2 text-sm font-semibold text-neutral-700">Our price</p>

                    <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-3">
                        <p class="retail-purchase-panel__price retail-purchase-panel__price--sale text-[2.7rem] font-semibold leading-none tracking-[-0.04em] text-red-700 sm:text-5xl">
                            <?php echo wp_kses_post( wc_price( $sale_price ) ); ?>
                        </p>

                        <p class="retail-purchase-panel__saving border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-semibold leading-none text-red-800">
                            <span>Save</span>
                            <span><?php echo wp_kses_post( wc_price( $saving, [ 'decimals' => $saving_decimals ] ) ); ?></span>
                        </p>
                    </div>
                <?php else : ?>
                    <p class="text-sm font-semibold text-neutral-700">Our price</p>

                    <p class="retail-purchase-panel__price mt-2 text-[2.7rem] font-semibold leading-none tracking-[-0.04em] text-neutral-950 sm:text-5xl">
                        <?php echo wp_kses_post( wc_price( $current_price ) ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( wc_tax_enabled() ) : ?>
                    <p class="mt-3 text-xs leading-relaxed text-neutral-500">
                        Price includes VAT where applicable.
                    </p>
                <?php endif; ?>
            </div>

            <?php if ( $is_agency_product ) : ?>
                <div class="retail-purchase-panel__agency shrink-0 border-t border-neutral-200 pt-4 sm:border-t-0 sm:pt-0">
                    <img
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/euronics-agent-logo.webp' ); ?>"
                        alt="Sold as an agent of Euronics Ltd"
                        class="h-8 w-auto object-contain sm:h-9"
                    >
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $primary_promotion ) : ?>
        <?php
        get_template_part(
            'template-parts/woocommerce/single-transactional-promotion-summary',
            null,
            [
                'promotion' => $primary_promotion,
            ]
        );
        ?>
    <?php endif; ?>

    <?php if ( ! empty( $key_features ) ) : ?>
        <section
            class="retail-purchase-panel__section retail-purchase-panel__features border-t border-neutral-200 px-5 py-5 sm:px-6 lg:px-7"
            aria-labelledby="transactional-key-features-title"
        >
            <h2 id="transactional-key-features-title" class="retail-purchase-panel__section-title text-base font-semibold text-neutral-950">
                Key features
            </h2>

            <ul class="mt-3 space-y-2 text-sm font-light leading-relaxed text-neutral-700">
                <?php foreach ( array_slice( $key_features, 0, 5 ) as $feature ) : ?>
                    <li class="flex gap-2.5">
                        <span
                            class="mt-[0.55rem] h-1.5 w-1.5 shrink-0 bg-blue-700"
                            aria-hidden="true"
                        ></span>
                        <span><?php echo esc_html( (string) $feature ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ( $has_energy_information ) : ?>
        <div class="retail-purchase-panel__section retail-purchase-panel__energy border-t border-neutral-200 px-5 py-4 sm:px-6 lg:px-7">
            <div class="flex flex-wrap items-start justify-between gap-x-6 gap-y-4">
                <div class="min-w-0">
                    <?php if ( $energy_rating_image_id ) : ?>
                        <?php if ( '' !== $full_energy_label_url ) : ?>
                            <a
                                href="<?php echo esc_url( $full_energy_label_url ); ?>"
                                class="inline-flex focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
                                aria-label="<?php echo esc_attr( 'View the full energy label for ' . $product_title ); ?>"
                                <?php if ( $full_energy_label_is_image ) : ?>
                                    data-fancybox="product-energy-label"
                                    data-caption="<?php echo esc_attr( $product_title . ' full energy label' ); ?>"
                                <?php else : ?>
                                    target="_blank"
                                    rel="noopener"
                                <?php endif; ?>
                            >
                                <?php
                                echo wp_get_attachment_image(
                                    $energy_rating_image_id,
                                    'thumbnail',
                                    false,
                                    [
                                        'class' => 'h-12 w-auto object-contain',
                                        'alt'   => esc_attr( $product_title . ' energy rating' ),
                                    ]
                                );
                                ?>
                            </a>
                        <?php else : ?>
                            <?php
                            echo wp_get_attachment_image(
                                $energy_rating_image_id,
                                'thumbnail',
                                false,
                                [
                                    'class' => 'h-12 w-auto object-contain',
                                    'alt'   => esc_attr( $product_title . ' energy rating' ),
                                ]
                            );
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( '' !== $full_energy_label_url || '' !== $product_information_sheet_url ) : ?>
                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-semibold leading-snug">
                            <?php if ( '' !== $full_energy_label_url ) : ?>
                                <a
                                    href="<?php echo esc_url( $full_energy_label_url ); ?>"
                                    class="text-blue-700 underline decoration-blue-300 underline-offset-4 transition hover:text-neutral-950 hover:decoration-neutral-500"
                                    <?php if ( $full_energy_label_is_image ) : ?>
                                        data-fancybox="product-energy-label"
                                        data-caption="<?php echo esc_attr( $product_title . ' full energy label' ); ?>"
                                    <?php else : ?>
                                        target="_blank"
                                        rel="noopener"
                                    <?php endif; ?>
                                >View full energy label</a>
                            <?php endif; ?>

                            <?php if ( '' !== $product_information_sheet_url ) : ?>
                                <a
                                    href="<?php echo esc_url( $product_information_sheet_url ); ?>"
                                    class="text-blue-700 underline decoration-blue-300 underline-offset-4 transition hover:text-neutral-950 hover:decoration-neutral-500"
                                    target="_blank"
                                    rel="noopener"
                                >Product information sheet</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="retail-purchase-panel__section retail-purchase-panel__order border-t border-neutral-200 px-5 py-5 sm:px-6 sm:py-6 lg:px-7 lg:py-7">
        <div class="retail-purchase-panel__availability [&>*:first-child]:mt-0">
            <?php
            if ( function_exists( 'blaines_render_transactional_availability_message' ) ) {
                blaines_render_transactional_availability_message( $product, 'single' );
            }
            ?>
        </div>

        <div class="transactional-add-to-cart retail-purchase-panel__add-to-cart mt-5 bg-white [&_form.cart]:m-0 [&_form.cart]:w-full [&_.single_add_to_cart_button]:inline-flex [&_.single_add_to_cart_button]:min-h-14 [&_.single_add_to_cart_button]:w-full [&_.single_add_to_cart_button]:items-center [&_.single_add_to_cart_button]:justify-center [&_.single_add_to_cart_button]:px-6 [&_.single_add_to_cart_button]:text-base [&_.single_add_to_cart_button]:font-semibold">
            <?php
            $had_global_product      = array_key_exists( 'product', $GLOBALS );
            $previous_global_product = $GLOBALS['product'] ?? null;
            $GLOBALS['product']       = $product;

            woocommerce_template_single_add_to_cart();

            if ( $had_global_product ) {
                $GLOBALS['product'] = $previous_global_product;
            } else {
                unset( $GLOBALS['product'] );
            }
            ?>
        </div>

        <p class="mt-3 text-center text-xs leading-relaxed text-neutral-500">
            Local delivery and collection options are shown during checkout.
        </p>
    </div>

    <?php if ( '' !== $short_description ) : ?>
        <div class="retail-purchase-panel__section retail-purchase-panel__short-description border-t border-neutral-200 px-5 py-5 text-sm font-light leading-relaxed text-neutral-700 sm:px-6 lg:px-7">
            <?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $short_description ) ); ?>
        </div>
    <?php endif; ?>

    <?php
    get_template_part(
        'template-parts/woocommerce/single-transactional-information',
        null,
        [
            'product'    => $product,
            'services'   => $available_services,
            'promotions' => $product_promotions,
        ]
    );
    ?>
</section>
