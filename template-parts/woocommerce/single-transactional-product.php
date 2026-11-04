<?php
/**
 * Single transactional WooCommerce product layout.
 */

defined( 'ABSPATH' ) || exit;

$product_id = get_the_ID();
$product    = wc_get_product( $product_id );

if ( ! $product ) {
    return;
}

$brand_logo = function_exists( 'get_field' )
    ? get_field( 'product_brand_logo', $product_id )
    : null;

$brand_logo_id = is_array( $brand_logo )
    ? absint( $brand_logo['ID'] ?? $brand_logo['id'] ?? 0 )
    : absint( $brand_logo );

$purchase_panel_classes = [
    'retail-product-page__purchase',
    'order-2',
    'lg:col-span-5',
    'lg:row-span-2',
];

if ( $brand_logo_id ) {
    $purchase_panel_classes[] = 'retail-product-page__purchase--aligned-title';
}
?>

<div class="blaines-retail blaines-retail--product">

<div class="retail-product-breadcrumbs container-wide py-3 text-sm">
    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
</div>

<div class="retail-product-notices container-wide">
    <?php wc_print_notices(); ?>
</div>

<section class="retail-product-page container-wide py-6 lg:py-10">
    <div class="retail-product-page__layout grid grid-cols-1 items-start gap-y-8 lg:grid-cols-12 lg:gap-x-10 lg:gap-y-10 xl:gap-x-14">
        <div class="retail-product-page__primary order-1 min-w-0 lg:col-span-7">
            <?php
            get_template_part(
                'template-parts/woocommerce/single-transactional-header',
                null,
                [
                    'product' => $product,
                ]
            );
            ?>

            <div class="retail-product-page__gallery mt-7 lg:mt-9">
                <?php
                get_template_part(
                    'template-parts/woocommerce/single-transactional-gallery',
                    null,
                    [
                        'product' => $product,
                    ]
                );
                ?>
            </div>
        </div>

        <aside class="<?php echo esc_attr(
            implode( ' ', $purchase_panel_classes )
        ); ?>">
            <?php
            get_template_part(
                'template-parts/woocommerce/single-transactional-summary',
                null,
                [
                    'product' => $product,
                ]
            );
            ?>
        </aside>

        <div class="retail-product-page__information order-3 lg:col-span-7">
            <?php
            get_template_part(
                'template-parts/woocommerce/single-transactional-product-information',
                null,
                [
                    'product' => $product,
                ]
            );
            ?>
        </div>
    </div>

    <?php
    get_template_part(
        'template-parts/woocommerce/single-transactional-similar-products',
        null,
        [
            'product' => $product,
        ]
    );
    ?>
</section>

</div>
