<?php
/**
 * Shared single ex-display kitchen/bathroom product layout.
 */

defined( 'ABSPATH' ) || exit;

$product_id = get_the_ID();
$product    = wc_get_product( $product_id );

if ( ! $product ) {
    return;
}

$display_type  = $args['display_type'] ?? 'display';
$archive_label = $args['archive_label'] ?? 'Clearance';
$archive_url   = $args['archive_url'] ?? home_url( '/clearance/' );

$display_subtitle      = get_field( 'display_subtitle', $product_id );
$display_specification = get_field( 'display_specification', $product_id );
$display_note          = get_field( 'display_note', $product_id );

$showroom = get_field( 'showroom_page', $product_id );

if ( is_array( $showroom ) ) {
    $showroom = $showroom[0] ?? null;
} elseif ( is_numeric( $showroom ) ) {
    $showroom = get_post( (int) $showroom );
}

$status = get_field( 'stock_status', $product_id );

$sale_price    = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$active_price  = $product->get_price();
?>

<div class="container-wide py-3 text-sm">
    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
</div>

<section class="container-wide py-8 lg:py-12">

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-12">

        <div class="order-1 lg:order-1 lg:col-span-7">
            <div class="lg:sticky lg:top-32">
                <?php
                get_template_part(
                    'template-parts/clearance/display-gallery',
                    null,
                    [
                        'product' => $product,
                    ]
                );
                ?>
            </div>
        </div>

        <aside class="order-2 lg:order-2 lg:col-span-5">
            <div class="space-y-6">

                <div>
                    <p class="font-body text-sm uppercase tracking-[0.12em] text-neutral-500">
                        <a href="<?php echo esc_url( $archive_url ); ?>" class="hover:text-neutral-900">
                            <?php echo esc_html( $archive_label ); ?>
                        </a>
                    </p>

                    <h1 class="mt-4 font-heading text-3xl font-light leading-tight text-neutral-950 md:text-5xl">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $display_subtitle ) : ?>
                        <p class="mt-3 font-body text-lg leading-snug text-neutral-800">
                            <?php echo esc_html( $display_subtitle ); ?>
                        </p>
                    <?php endif; ?>

                    <div class="mt-6">
                        <p class="font-body text-lg font-semibold text-red-700">
                            Ex-Display
                        </p>

                        <?php if ( '' !== $active_price ) : ?>
                            <p class="mt-1 font-heading text-5xl font-semibold leading-none text-red-700 md:text-5xl">
                                <?php echo wp_kses_post( wc_price( $active_price, [ 'decimals' => 0 ] ) ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $regular_price && $regular_price !== $active_price ) : ?>
                            <p class="mt-3 font-body text-base text-neutral-700">
                                Original Price <?php echo wp_kses_post( wc_price( $regular_price, [ 'decimals' => 0 ] ) ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ( $display_note ) : ?>
                        <div class="mt-6 border-l-2 border-neutral-300 pl-4">
                            <p class="font-body text-sm leading-relaxed text-neutral-700">
                                <?php echo esc_html( $display_note ); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                get_template_part(
                    'template-parts/clearance/single-showroom',
                    null,
                    [
                        'showroom' => $showroom,
                    ]
                );
                ?>

                <div class="border-t border-neutral-300 pt-5">
                    <details
                            class="group"
                            x-data="{ open: window.matchMedia('(min-width: 1024px)').matches }"
                            x-bind:open="open"
                    >
                        <summary
                                class="flex cursor-pointer list-none items-center justify-between gap-4"
                                @click.prevent="open = !open"
                        >
                            <h2 class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-900">
                                Specification
                            </h2>

                            <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                                ↓
                            </span>
                        </summary>

                        <div class="mt-5 font-body text-sm leading-relaxed text-neutral-800">
                            <?php if ( $display_specification ) : ?>
                                <div class="prose-news max-w-none">
                                    <?php echo wp_kses_post( $display_specification ); ?>
                                </div>
                            <?php else : ?>
                                <p>No display specification has been added yet.</p>
                            <?php endif; ?>
                        </div>
                    </details>
                </div>

            </div>
        </aside>

    </div>

    <?php
    get_template_part(
        'template-parts/clearance/display-faqs',
        null,
        [
            'display_type' => $display_type,
        ]
    );
    ?>

</section>