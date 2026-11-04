<?php
$product  = $args['product'] ?? null;
$status   = $args['status'] ?? '';

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id       = $product->get_id();
$title            = get_the_title( $product_id );
$short_desc       = $product->get_short_description();
$regular_price    = $product->get_regular_price();
$active_price     = $product->get_price();
$product_code     = $product->get_sku();
$clearance_reason = get_field( 'clearance_reason', $product_id );
$measurements     = get_field( 'measurements', $product_id );

$has_discount = (
    '' !== $regular_price &&
    '' !== $active_price &&
    (float) $regular_price > (float) $active_price
);

$saving_amount = $has_discount ? ( (float) $regular_price - (float) $active_price ) : 0;
?>

<div class="border border-neutral-200 bg-white p-6">
    <div class="border-b border-neutral-200 pb-6">
        <?php
        $product_categories = get_the_terms( $product_id, 'product_cat' );
        ?>

        <?php if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) : ?>
            <p class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
                <?php echo esc_html( $product_categories[0]->name ); ?>
            </p>
        <?php endif; ?>

        <h1 class="mt-3 font-body text-2xl font-medium leading-tight tracking-[-0.02em] text-neutral-900 md:text-4xl">
            <?php echo esc_html( $title ); ?>
        </h1>

        <?php if ( $short_desc ) : ?>
            <div class="prose-site mt-4 max-w-none text-neutral-700">
                <?php echo wp_kses_post( wpautop( $short_desc ) ); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="border-b border-neutral-200 py-6">
        <?php if ( $clearance_reason ) : ?>
            <div>
                <p class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
                    Clearance reason
                </p>
                <p class="mt-2 font-body text-sm leading-relaxed text-neutral-900">
                    <?php echo esc_html( $clearance_reason ); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="<?php echo $clearance_reason ? 'mt-5' : ''; ?> flex flex-wrap items-end gap-x-3 gap-y-2">
            <?php if ( '' !== $active_price ) : ?>
                <p class="font-body text-3xl font-semibold leading-none text-red-700">
                    <?php echo wp_kses_post( wc_price( (float) $active_price ) ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $has_discount ) : ?>
                <p class="font-body text-base font-medium text-neutral-400 line-through">
                    <?php echo wp_kses_post( wc_price( (float) $regular_price ) ); ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ( $saving_amount > 0 ) : ?>
            <p class="mt-2 font-body text-sm font-semibold text-red-700">
                Save £<?php echo esc_html( number_format( round( $saving_amount ), 0 ) ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $product_code ) : ?>
            <p class="mt-4 font-body text-sm text-neutral-700">
                <span class="font-medium text-neutral-900">Product code:</span>
                <?php echo esc_html( $product_code ); ?>
            </p>
        <?php endif; ?>

        <?php if ( $status ) : ?>
            <p class="mt-2 font-body text-sm text-neutral-700">
                <span class="font-medium text-neutral-900">Status:</span>
                <?php echo esc_html( $status ); ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if ( have_rows( 'key_features', $product_id ) ) : ?>
        <div class="border-b border-neutral-200 py-6">
            <p class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
                Key features
            </p>

            <ul class="mt-3 list-disc space-y-1.5 pl-4 font-body text-sm leading-6 text-neutral-800 marker:text-neutral-400">
                <?php while ( have_rows( 'key_features', $product_id ) ) : the_row(); ?>
                    <?php $feature = get_sub_field( 'feature' ); ?>
                    <?php if ( $feature ) : ?>
                        <li><?php echo esc_html( $feature ); ?></li>
                    <?php endif; ?>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ( $measurements ) : ?>
        <div class="pt-6">
            <p class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
                Measurements
            </p>

            <p class="mt-2 font-body text-sm leading-6 text-neutral-800">
                <?php echo esc_html( $measurements ); ?>
            </p>
        </div>
    <?php endif; ?>
</div>
