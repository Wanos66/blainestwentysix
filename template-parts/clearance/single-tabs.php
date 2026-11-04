<?php
$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id      = $product->get_id();
$description     = get_post_field( 'post_content', $product_id );
$has_description = ! empty( trim( wp_strip_all_tags( $description ) ) );
$attributes      = $product->get_attributes();
$has_attributes  = ! empty( $attributes );

$is_transactional = function_exists( 'blaines_is_transactional_product' )
                    && blaines_is_transactional_product( $product );

$has_delivery     = $is_transactional;
$has_installation = $is_transactional;

if ( $has_description ) {
    $default_tab = 'description';
} elseif ( $has_attributes ) {
    $default_tab = 'specifications';
} elseif ( $has_delivery ) {
    $default_tab = 'delivery';
} else {
    $default_tab = 'installation';
}
?>

<?php if ( $has_description || $has_attributes || $has_delivery || $has_installation ) : ?>
    <section class="mt-10 border-t border-neutral-200 pt-8" x-data="{ tab: '<?php echo esc_attr( $default_tab ); ?>' }">
        <div class="border-b border-neutral-200">
            <div class="flex gap-6 overflow-x-auto whitespace-nowrap scrollbar-hide">
                <?php if ( $has_description ) : ?>
                    <button
                            type="button"
                            class="border-b-2 pb-3 text-sm font-semibold uppercase tracking-[0.06em] transition"
                            :class="tab === 'description' ? 'border-black text-black' : 'border-transparent text-neutral-500 hover:text-black'"
                            @click="tab = 'description'"
                    >
                        Description
                    </button>
                <?php endif; ?>

                <?php if ( $has_attributes ) : ?>
                    <button
                            type="button"
                            class="border-b-2 pb-3 text-sm font-semibold uppercase tracking-[0.06em] transition"
                            :class="tab === 'specifications' ? 'border-black text-black' : 'border-transparent text-neutral-500 hover:text-black'"
                            @click="tab = 'specifications'"
                    >
                        Specifications
                    </button>
                <?php endif; ?>

                <?php if ( $has_delivery ) : ?>
                    <button
                            type="button"
                            class="border-b-2 pb-3 text-sm font-semibold uppercase tracking-[0.06em] transition"
                            :class="tab === 'delivery' ? 'border-black text-black' : 'border-transparent text-neutral-500 hover:text-black'"
                            @click="tab = 'delivery'"
                    >
                        Delivery
                    </button>
                <?php endif; ?>

                <?php if ( $has_installation ) : ?>
                    <button
                            type="button"
                            class="border-b-2 pb-3 text-sm font-semibold uppercase tracking-[0.06em] transition"
                            :class="tab === 'installation' ? 'border-black text-black' : 'border-transparent text-neutral-500 hover:text-black'"
                            @click="tab = 'installation'"
                    >
                        Services
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $has_description ) : ?>
            <div x-show="tab === 'description'" x-cloak class="pt-6">
                <div class="prose-site prose-news max-w-none">
                    <?php echo apply_filters( 'the_content', $description ); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $has_attributes ) : ?>
            <div x-show="tab === 'specifications'" x-cloak class="pt-6">
                <div class="woocommerce-product-attributes-wrapper">
                    <?php wc_display_product_attributes( $product ); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $has_delivery ) : ?>
            <div x-show="tab === 'delivery'" x-cloak class="pt-6">
                <div class="prose-site prose-news max-w-none">
                    <h2>Delivery information</h2>

                    <p>
                        We offer local delivery on selected electrical appliances, televisions and audio products within our approved delivery area.
                    </p>

                    <p>
                        Delivery options will be shown during checkout where available. Some products may also be available for collection from Blaines.
                    </p>

                    <p>
                        If you are unsure whether delivery is available to your address, please contact Web Sales on
                        <a href="tel:01760302111">01760 302111</a>
                        before placing your order.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $has_installation ) : ?>
            <div x-show="tab === 'installation'" x-cloak class="pt-6">
                <div class="prose-site prose-news max-w-none">
                    <h2>Services information</h2>

                    <p>
                        Additional services may be available on selected products, including installation,
                        disconnection, disposal of an old appliance, packaging removal, and selected TV
                        or audio setup services.
                    </p>

                    <p>
                        Available services depend on the product type, local service availability, site
                        access, and product suitability. Where applicable, service options and prices
                        will be shown during checkout.
                    </p>

                    <p>
                        If you are unsure which services you need, please contact Web Sales on
                        <a href="tel:01760302111">01760 302111</a> before placing your order.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>