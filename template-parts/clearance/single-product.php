<?php
/**
 * Single electrical clearance product layout.
 */

defined( 'ABSPATH' ) || exit;

$product_id = get_the_ID();
$product    = wc_get_product( $product_id );

if ( ! $product ) {
    return;
}

$showroom = get_field( 'showroom_page', $product_id );

if ( is_array( $showroom ) ) {
    $showroom = $showroom[0] ?? null;
} elseif ( is_numeric( $showroom ) ) {
    $showroom = get_post( (int) $showroom );
}

$status = get_field( 'stock_status', $product_id );
?>

<div class="container-wide py-3 text-sm">
    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
</div>

<section class="container-wide py-8 lg:py-12">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-12">

        <div class="order-2 lg:order-1 lg:col-span-7">
            <?php get_template_part( 'template-parts/clearance/single-gallery', null, [ 'product' => $product ] ); ?>

            <?php
            get_template_part(
                'template-parts/clearance/single-tabs',
                null,
                [
                    'product' => $product,
                ]
            );
            ?>
        </div>

        <aside class="order-1 lg:order-2 lg:col-span-5">
            <div class="space-y-6 lg:sticky lg:top-32">
                <?php
                get_template_part(
                    'template-parts/clearance/single-summary',
                    null,
                    [
                        'product'  => $product,
                        'showroom' => $showroom,
                        'status'   => $status,
                    ]
                );
                ?>

                <?php
                get_template_part(
                    'template-parts/clearance/single-showroom',
                    null,
                    [
                        'showroom' => $showroom,
                    ]
                );
                ?>
            </div>
        </aside>

    </div>
</section>
