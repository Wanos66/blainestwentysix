<?php
$product_id = $args['product_id'] ?? get_the_ID();

$brand    = get_field( 'product_brand', $product_id );
$showroom = get_field( 'showroom_page', $product_id );
$status   = get_field( 'stock_status', $product_id );

$product = wc_get_product( $product_id );

$regular_price = $product ? $product->get_regular_price() : '';
$sale_price    = $product ? $product->get_sale_price() : '';
$active_price  = $product ? $product->get_price() : '';

$saving_amount = ( $regular_price > $active_price ) ? ( $regular_price - $active_price ) : 0;

$product_permalink = get_permalink( $product_id );
$product_title     = get_the_title( $product_id );
?>

<article class="group flex h-full flex-col border border-neutral-200 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
    <a href="<?php echo esc_url( $product_permalink ); ?>" class="relative block bg-white">
        <div class="relative aspect-[4/3] w-full bg-neutral-50 p-4 flex items-center justify-center overflow-hidden">

            <div class="absolute left-3 top-3 z-20 bg-yellow-400 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-black">
                Clearance
            </div>

            <?php if ( has_post_thumbnail( $product_id ) ) : ?>
                <?php
                echo get_the_post_thumbnail(
                    $product_id,
                    'large',
                    [
                        'class' => 'relative z-10 max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-[1.02]',
                    ]
                );
                ?>
            <?php else : ?>
                <div class="flex h-full w-full items-center justify-center text-sm text-neutral-400">
                    No image available
                </div>
            <?php endif; ?>

        </div>
    </a>

    <div class="flex flex-1 flex-col p-4">
        <?php if ( $brand ) : ?>
            <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-neutral-500">
                <?php echo esc_html( $brand ); ?>
            </p>
        <?php endif; ?>

        <h3 class="mb-3 line-clamp-2 text-base font-medium leading-snug text-neutral-900">
            <a href="<?php echo esc_url( $product_permalink ); ?>" class="transition-colors duration-200 hover:text-neutral-700">
                <?php echo esc_html( $product_title ); ?>
            </a>
        </h3>

        <div class="mb-4">
            <div class="flex items-end gap-3">
                <?php if ( $active_price > 0 ) : ?>
                    <p class="text-xl font-semibold leading-none text-neutral-950">
                        <?php echo wp_kses_post( wc_price( $active_price ) ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $sale_price > 0 && $regular_price > 0 ) : ?>
                    <p class="text-sm font-medium text-neutral-400 line-through">
                        <?php echo wp_kses_post( wc_price( $regular_price ) ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ( $saving_amount > 0 ) : ?>
                <p class="mt-2 text-sm font-bold uppercase tracking-[0.04em] text-red-700">
                    Save £<?php echo esc_html( number_format( round( $saving_amount ), 0 ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mt-auto border-t border-neutral-200 pt-3">
            <?php if ( $showroom instanceof WP_Post ) : ?>
                <p class="font-body mb-3 text-sm text-neutral-600">
                    Available at
                    <a href="<?php echo esc_url( get_permalink( $showroom->ID ) ); ?>" class="font-medium text-neutral-900 underline underline-offset-2
                    hover:text-neutral-700">
                        <?php echo esc_html( get_the_title( $showroom->ID ) ); ?>
                    </a>
                </p>
            <?php endif; ?>

            <a href="<?php echo esc_url( $product_permalink ); ?>" class="inline-flex items-center text-sm font-body font-semibold text-neutral-900 transition-colors duration-200
            hover:text-neutral-700">
                View details
                <span class="ml-2 transition-transform duration-200 group-hover:translate-x-1">→</span>
            </a>
        </div>
    </div>
</article>