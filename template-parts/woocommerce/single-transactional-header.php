<?php
/**
 * Transactional single product identity header.
 *
 * Displayed at the top of the left product column above the gallery.
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
$sku           = $product->get_sku();

$brand_logo = function_exists( 'get_field' )
    ? get_field( 'product_brand_logo', $product_id )
    : null;

$brand_logo_id = is_array( $brand_logo )
    ? (int) ( $brand_logo['ID'] ?? $brand_logo['id'] ?? 0 )
    : (int) $brand_logo;
?>

<header class="retail-product-identity max-w-[54rem]">

    <?php if ( $brand_logo_id ) : ?>
        <div class="retail-product-identity__brand max-w-[190px]">
            <?php
            echo wp_get_attachment_image(
                $brand_logo_id,
                'medium',
                false,
                [
                    'class' => 'retail-product-identity__brand-image h-auto max-h-14 w-auto object-contain',
                    'alt'   => esc_attr( $product_title . ' brand logo' ),
                ]
            );
            ?>
        </div>
    <?php endif; ?>

    <h1 class="<?php echo esc_attr( $brand_logo_id ? 'mt-4 ' : '' ); ?>retail-product-identity__title">
        <?php echo esc_html( $product_title ); ?>
    </h1>

    <?php if ( $sku ) : ?>
        <p class="retail-product-identity__code mt-5 text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
            <span class="text-neutral-700">Product code:</span>
            <?php echo esc_html( $sku ); ?>
        </p>
    <?php endif; ?>

</header>