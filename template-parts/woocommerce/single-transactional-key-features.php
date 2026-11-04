<?php
/**
 * Transactional product key features.
 *
 * Displayed in the left product column beneath the product identity.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
    return;
}

$product_id = $product->get_id();

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

if ( empty( $key_features ) ) {
    return;
}
?>

<section class="mt-6 border-t border-neutral-200 pt-5 font-body" aria-labelledby="transactional-key-features-title">
    <h2 id="transactional-key-features-title" class="text-base font-semibold text-neutral-950">
        Key features
    </h2>

    <ul class="mt-3 grid gap-x-8 gap-y-2 text-sm font-light leading-relaxed text-neutral-700 sm:grid-cols-2">
        <?php foreach ( array_slice( $key_features, 0, 6 ) as $feature ) : ?>
            <li class="flex gap-2.5">
                <span class="mt-[0.55rem] h-1.5 w-1.5 shrink-0 bg-blue-700" aria-hidden="true"></span>
                <span><?php echo esc_html( (string) $feature ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
