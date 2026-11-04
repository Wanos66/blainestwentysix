<?php
/**
 * Transactional WooCommerce product card.
 *
 * Used for standard electrical appliances, TVs and audio products.
 *
 * @package Blaines
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

if (
    function_exists( 'blaines_is_transactional_product' )
    && ! blaines_is_transactional_product( $product )
) {
    return;
}

$title          = get_the_title( $product_id );
$permalink      = get_permalink( $product_id );
$image_id       = $product->get_image_id();
$sku            = $product->get_sku();
$regular_price  = (float) $product->get_regular_price();
$sale_price     = (float) $product->get_sale_price();
$is_on_sale     = $product->is_on_sale() && $regular_price > $sale_price;
$saving         = $is_on_sale ? $regular_price - $sale_price : 0;
$brand_logo_id  = function_exists( 'get_field' ) ? get_field( 'product_brand_logo', $product_id ) : null;

$is_agency_product = function_exists( 'get_field' )
    ? (bool) get_field( 'product_is_agency_product', $product_id )
    : (bool) get_post_meta( $product_id, 'product_is_agency_product', true );

$terms = get_the_terms( $product_id, 'product_cat' );
$category_label = '';

if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    usort(
        $terms,
        static function ( $a, $b ) {
            return count( get_ancestors( $b->term_id, 'product_cat' ) ) <=> count( get_ancestors( $a->term_id, 'product_cat' ) );
        }
    );

    foreach ( $terms as $term ) {
        if ( 'electrical' !== $term->slug ) {
            $category_label = $term->name;
            break;
        }
    }
}
?>

<article <?php wc_product_class( 'group flex h-full flex-col bg-white', $product ); ?>>
    <a href="<?php echo esc_url( $permalink ); ?>" class="block">
        <div class="aspect-[4/3] bg-white">
            <?php if ( $image_id ) : ?>
                <?php
                echo wp_get_attachment_image(
                    $image_id,
                    'large',
                    false,
                    [
                        'class' => 'h-full w-full object-contain transition duration-300 group-hover:scale-[1.02]',
                        'alt'   => esc_attr( $title ),
                    ]
                );
                ?>
            <?php else : ?>
                <div class="flex h-full items-center justify-center border border-neutral-200 text-sm text-neutral-500">
                    Product image coming soon
                </div>
            <?php endif; ?>
        </div>
    </a>

    <div class="flex flex-1 flex-col border-t border-neutral-200 pt-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <?php if ( $category_label ) : ?>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">
                        <?php echo esc_html( $category_label ); ?>
                    </p>
                <?php endif; ?>

                <h2 class="mt-2 text-lg font-light leading-snug text-neutral-950">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="transition hover:text-blue-700">
                        <?php echo esc_html( $title ); ?>
                    </a>
                </h2>
            </div>

            <?php if ( $brand_logo_id ) : ?>
                <div class="max-w-[92px] shrink-0">
                    <?php
                    echo wp_get_attachment_image(
                        $brand_logo_id,
                        'medium',
                        false,
                        [
                            'class' => 'h-auto max-h-8 w-auto object-contain',
                            'alt'   => esc_attr( $title . ' brand logo' ),
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $sku ) : ?>
            <p class="mt-3 text-xs text-neutral-500">
                SKU: <?php echo esc_html( $sku ); ?>
            </p>
        <?php endif; ?>

        <div class="mt-5">
            <?php if ( $is_on_sale ) : ?>
                <p class="text-sm text-neutral-500">
                    Was <span class="line-through"><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></span>
                </p>

                <div class="mt-1 flex flex-wrap items-end gap-x-3 gap-y-1">
                    <p class="text-2xl font-semibold leading-none text-red-700">
                        <?php echo wp_kses_post( wc_price( $sale_price ) ); ?>
                    </p>

                    <p class="pb-0.5 text-xs font-semibold text-neutral-950">
                        Save <?php echo wp_kses_post( wc_price( $saving ) ); ?>
                    </p>
                </div>
            <?php else : ?>
                <p class="text-2xl font-light leading-none text-neutral-950">
                    <?php echo wp_kses_post( wc_price( (float) $product->get_price() ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mt-4 flex items-center justify-between gap-4 border-t border-neutral-200 pt-4">
            <?php if ( $product->is_in_stock() ) : ?>
                <p class="text-sm font-semibold text-green-700">
                    In Stock
                </p>
            <?php else : ?>
                <p class="text-sm font-semibold text-red-700">
                    Out of Stock
                </p>
            <?php endif; ?>

            <?php if ( $is_agency_product ) : ?>
                <span class="text-[0.68rem] font-semibold uppercase tracking-[0.08em] text-neutral-500">
                    Euronics Agency
                </span>
            <?php endif; ?>
        </div>

        <div class="mt-5">
            <a
                href="<?php echo esc_url( $permalink ); ?>"
                class="inline-flex w-full items-center justify-center bg-neutral-950 px-5 py-3 text-center text-xs font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-black"
            >
                View product
            </a>
        </div>
    </div>
</article>
