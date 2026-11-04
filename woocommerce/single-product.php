<?php
/**
 * Single WooCommerce product template router.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$product_id = get_the_ID();
$product    = wc_get_product( $product_id );

if ( ! $product ) {
    get_footer();
    return;
}

if ( has_term( 'kitchen-clearance', 'product_cat', $product_id ) ) {
    get_template_part(
        'template-parts/clearance/single-display-product',
        null,
        [
            'display_type'  => 'kitchen',
            'archive_label' => 'Kitchen Clearance',
            'archive_url'   => home_url( '/kitchen-clearance/' ),
        ]
    );
} elseif ( has_term( 'bathroom-clearance', 'product_cat', $product_id ) ) {
    get_template_part(
        'template-parts/clearance/single-display-product',
        null,
        [
            'display_type'  => 'bathroom',
            'archive_label' => 'Bathroom Clearance',
            'archive_url'   => home_url( '/bathroom-clearance/' ),
        ]
    );
} elseif (
    function_exists( 'blaines_is_transactional_product' )
    && blaines_is_transactional_product( $product )
) {
    get_template_part( 'template-parts/woocommerce/single-transactional-product' );
} else {
    get_template_part( 'template-parts/clearance/single-product' );
}

get_footer();