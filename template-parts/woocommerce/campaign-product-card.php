<?php
/**
 * Reusable campaign product card.
 *
 * Expected args:
 * - product
 * - roundel_enabled
 * - roundel_text
 * - roundel_image_url
 * - roundel_label
 * - modifier
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$roundel_enabled   = ! empty( $args['roundel_enabled'] );
$roundel_text      = trim( (string) ( $args['roundel_text'] ?? '' ) );
$roundel_image_url = esc_url_raw( (string) ( $args['roundel_image_url'] ?? '' ) );
$roundel_label     = trim( (string) ( $args['roundel_label'] ?? '' ) );
$modifier          = sanitize_html_class( (string) ( $args['modifier'] ?? '' ) );

$product_id    = $product->get_id();
$product_url   = $product->get_permalink();
$product_title = $product->get_name();

$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$current_price = (float) $product->get_price();

$is_on_sale = $product->is_on_sale()
    && $sale_price > 0
    && $regular_price > $sale_price;

$saving = $is_on_sale
    ? max( 0, $regular_price - $sale_price )
    : 0;

$saving_decimals = (
    $saving > 0
    && floor( $saving ) === $saving
)
    ? 0
    : wc_get_price_decimals();

$brand_logo_id = 0;
$brand_name    = '';

if ( function_exists( 'get_field' ) ) {
    $brand_logo = get_field( 'product_brand_logo', $product_id );

    if ( is_array( $brand_logo ) && ! empty( $brand_logo['ID'] ) ) {
        $brand_logo_id = absint( $brand_logo['ID'] );
    } elseif ( is_numeric( $brand_logo ) ) {
        $brand_logo_id = absint( $brand_logo );
    }
}

if ( ! $brand_logo_id ) {
    $brand_name = trim(
        (string) get_post_meta(
            $product_id,
            '_euronics_brand_name',
            true
        )
    );
}
?>

<article class="blaines-promotion-campaign__product<?php echo $modifier ? ' blaines-promotion-campaign__product--' . esc_attr( $modifier ) : ''; ?>">
    <div class="blaines-promotion-campaign__product-media">

        <?php if ( $is_on_sale && $saving > 0 ) : ?>
            <span class="blaines-promotion-campaign__saving-badge">
                Save &nbsp;
                <?php
                echo wp_kses_post(
                    wc_price(
                        $saving,
                        [ 'decimals' => $saving_decimals ]
                    )
                );
                ?>
            </span>
        <?php endif; ?>

        <a
            class="blaines-promotion-campaign__product-image"
            href="<?php echo esc_url( $product_url ); ?>"
            aria-label="<?php echo esc_attr( sprintf( 'View %s', $product_title ) ); ?>"
        >
            <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
        </a>

        <?php if ( $roundel_enabled && ( '' !== $roundel_text || '' !== $roundel_image_url ) ) : ?>
            <div
                class="blaines-promotion-campaign__roundel<?php echo '' !== $roundel_image_url ? ' blaines-promotion-campaign__roundel--image' : ''; ?>"
                aria-label="<?php echo esc_attr( '' !== $roundel_label ? $roundel_label : 'Promotion' ); ?>"
            >
                <?php if ( '' !== $roundel_image_url ) : ?>
                    <img
                        src="<?php echo esc_url( $roundel_image_url ); ?>"
                        alt=""
                        class="blaines-promotion-campaign__roundel-image"
                        loading="lazy"
                        decoding="async"
                    >
                <?php else : ?>
                    <span><?php echo esc_html( $roundel_text ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="blaines-promotion-campaign__product-body">

        <?php if ( $brand_logo_id || '' !== $brand_name ) : ?>
            <div class="blaines-promotion-campaign__product-brand">
                <?php if ( $brand_logo_id ) : ?>
                    <?php
                    echo wp_get_attachment_image(
                        $brand_logo_id,
                        'medium',
                        false,
                        [
                            'class'    => 'blaines-promotion-campaign__product-brand-image',
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                            'alt'      => '',
                        ]
                    );
                    ?>
                <?php else : ?>
                    <span class="blaines-promotion-campaign__product-brand-name">
                        <?php echo esc_html( $brand_name ); ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h3>
            <a href="<?php echo esc_url( $product_url ); ?>">
                <?php echo esc_html( $product_title ); ?>
            </a>
        </h3>

        <div class="blaines-promotion-campaign__product-commercial">
            <?php if ( $is_on_sale ) : ?>
                <p class="blaines-promotion-campaign__product-was">
                    Was <del><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></del>
                </p>
            <?php else : ?>
                <p class="blaines-promotion-campaign__product-was blaines-promotion-campaign__product-was--placeholder" aria-hidden="true">
                    &nbsp;
                </p>
            <?php endif; ?>

            <p class="blaines-promotion-campaign__product-price<?php echo $is_on_sale ? ' blaines-promotion-campaign__product-price--sale' : ''; ?>">
                <?php echo wp_kses_post( wc_price( $current_price ) ); ?>
            </p>
        </div>

        <a
            class="blaines-promotion-campaign__product-action"
            href="<?php echo esc_url( $product_url ); ?>"
        >
            View product
        </a>
    </div>
</article>
