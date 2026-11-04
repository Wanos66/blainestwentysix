<?php
/**
 * Display clearance product card.
 *
 * Shared by Kitchen Clearance and Bathroom Clearance archive pages.
 */

defined( 'ABSPATH' ) || exit;

$product_id   = $args['product_id'] ?? get_the_ID();
$display_type = $args['display_type'] ?? 'display';

$product = wc_get_product( $product_id );

if ( ! $product ) {
    return;
}

$display_subtitle = get_field( 'display_subtitle', $product_id );

$showroom = get_field( 'showroom_page', $product_id );

if ( is_array( $showroom ) ) {
    $showroom = $showroom[0] ?? null;
} elseif ( is_numeric( $showroom ) ) {
    $showroom = get_post( (int) $showroom );
}

$showroom_title = $showroom instanceof WP_Post ? get_the_title( $showroom ) : '';

$active_price  = $product->get_price();
$regular_price = $product->get_regular_price();

$item_label = match ( $display_type ) {
    'kitchen'  => 'Ex-display kitchen',
    'bathroom' => 'Ex-display bathroom',
    default    => 'Ex-display',
};

$product_url = get_permalink( $product_id );
$image_id    = $product->get_image_id();
?>

<article class="group flex h-full flex-col border border-neutral-200 bg-white">
    <a href="<?php echo esc_url( $product_url ); ?>" class="block overflow-hidden bg-neutral-100">
        <?php if ( $image_id ) : ?>
            <?php
            echo wp_get_attachment_image(
                $image_id,
                'large',
                false,
                [
                    'class' => 'aspect-[16/10] w-full object-cover transition duration-300 group-hover:scale-[1.02]',
                ]
            );
            ?>
        <?php else : ?>
            <div class="aspect-[16/10] w-full bg-neutral-100"></div>
        <?php endif; ?>
    </a>

    <div class="flex flex-1 flex-col p-6">
        <div>
            <p class="font-body text-xs font-semibold uppercase tracking-[0.12em] text-neutral-500">
                <?php echo esc_html( $item_label ); ?>
            </p>

            <h2 class="mt-3 font-heading text-2xl font-light leading-tight text-neutral-950">
                <a href="<?php echo esc_url( $product_url ); ?>" class="hover:underline hover:underline-offset-4">
                    <?php echo esc_html( get_the_title( $product_id ) ); ?>
                </a>
            </h2>

            <?php if ( $display_subtitle ) : ?>
                <p class="mt-2 font-body text-sm leading-relaxed text-neutral-700">
                    <?php echo esc_html( $display_subtitle ); ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ( $showroom_title ) : ?>
            <div class="mt-5 border-t border-neutral-200 pt-4">
                <p class="font-body text-xs font-semibold uppercase tracking-[0.1em] text-neutral-500">
                    Available to view at
                </p>

                <p class="mt-1 font-body text-base font-semibold text-neutral-950">
                    <?php echo esc_html( $showroom_title ); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-5">
            <?php if ( '' !== $active_price ) : ?>
                <p class="font-heading text-3xl font-semibold leading-none text-red-700">
                    <?php echo wp_kses_post( wc_price( $active_price, [ 'decimals' => 0 ] ) ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $regular_price && $regular_price !== $active_price ) : ?>
                <p class="mt-2 font-body text-sm text-neutral-600">
                    Original price <?php echo wp_kses_post( wc_price( $regular_price, [ 'decimals' => 0 ] ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mt-auto pt-6">
            <a
                    href="<?php echo esc_url( $product_url ); ?>"
                    class="inline-flex w-full items-center justify-center border border-neutral-900 px-4 py-3 font-body text-sm font-semibold text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
            >
                View display
            </a>
        </div>
    </div>
</article>