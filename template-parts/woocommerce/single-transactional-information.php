<?php
/**
 * Expandable supporting information for transactional products.
 *
 * SP3 close-out: delivery, services, structured promotions, guarantee and support.
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

$services = isset( $args['services'] ) && is_array( $args['services'] )
    ? $args['services']
    : [];

$services = array_values(
    array_filter(
        $services,
        static function ( $service ) {
            return is_array( $service )
                && isset( $service['label'], $service['price'] )
                && '' !== trim( (string) $service['label'] )
                && is_numeric( $service['price'] );
        }
    )
);

$promotions = isset( $args['promotions'] ) && is_array( $args['promotions'] )
    ? $args['promotions']
    : [];

$promotions = array_values(
    array_filter(
        $promotions,
        static function ( $promotion ) {
            return is_array( $promotion )
                && ! empty( $promotion['active'] )
                && (
                    ! empty( $promotion['title'] )
                    || ! empty( $promotion['summary'] )
                    || ! empty( $promotion['details'] )
                );
        }
    )
);

$warranty_information = function_exists( 'blaines_get_product_warranty_information' )
    ? blaines_get_product_warranty_information( $product )
    : [];

$warranty_label = isset( $warranty_information['label'] )
    ? trim( (string) $warranty_information['label'] )
    : '';

$warranty_detail = isset( $warranty_information['detail'] )
    ? trim( (string) $warranty_information['detail'] )
    : '';

$warranty_registration_note = isset( $warranty_information['registration_note'] )
    ? trim( (string) $warranty_information['registration_note'] )
    : '';

$chevron = static function () {
    ?>
    <svg
        class="retail-purchase-information__chevron h-5 w-5 shrink-0 text-neutral-500 transition-transform duration-200 group-open:rotate-180"
        viewBox="0 0 20 20"
        fill="none"
        aria-hidden="true"
    >
        <path
            d="M5 7.5 10 12.5 15 7.5"
            stroke="currentColor"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
    </svg>
    <?php
};
?>

<section class="retail-purchase-information border-t border-neutral-200 bg-white font-body" aria-label="Product information">

    <details class="retail-purchase-information__item group">
        <summary class="retail-purchase-information__summary flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700 sm:px-5 [&::-webkit-details-marker]:hidden">
            <span class="text-sm font-semibold text-neutral-950">
                Delivery &amp; collection
            </span>

            <?php $chevron(); ?>
        </summary>

        <div class="retail-purchase-information__content border-t border-neutral-200 bg-neutral-50 px-4 py-4 text-sm font-light leading-relaxed text-neutral-700 sm:px-5">
            <p>
                Local delivery and collection options are shown in your basket and at checkout, and depend on your address, product availability and any services selected.
            </p>

            <p class="mt-3">
                When local delivery is selected, you can choose from the available preferred delivery dates shown at checkout.
            </p>
        </div>
    </details>

    <details class="retail-purchase-information__item group border-t border-neutral-200">
        <summary class="retail-purchase-information__summary flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700 sm:px-5 [&::-webkit-details-marker]:hidden">
            <span class="text-sm font-semibold text-neutral-950">
                Services available
            </span>

            <?php $chevron(); ?>
        </summary>

        <div class="retail-purchase-information__content border-t border-neutral-200 bg-neutral-50 px-4 py-4 sm:px-5">
            <?php if ( ! empty( $services ) ) : ?>
                <ul class="divide-y divide-neutral-200">
                    <?php foreach ( $services as $service ) : ?>
                        <li class="flex items-start justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="text-sm font-light leading-relaxed text-neutral-700">
                                    <?php echo esc_html( (string) $service['label'] ); ?>
                                </p>

                                <?php if ( ! empty( $service['description'] ) ) : ?>
                                    <p class="mt-1 text-xs font-light leading-relaxed text-neutral-500">
                                        <?php echo esc_html( (string) $service['description'] ); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <span class="shrink-0 text-sm font-semibold text-neutral-950">
                                <?php echo wp_kses_post( wc_price( (float) $service['price'] ) ); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="mt-4 border-t border-neutral-200 pt-4 text-xs font-light leading-relaxed text-neutral-500">
                    Select any required services in your basket. Options remain subject to product suitability and local availability.
                </p>
            <?php else : ?>
                <p class="text-sm font-light leading-relaxed text-neutral-700">
                    No additional installation, disconnection or disposal services are currently listed for this product.
                </p>
            <?php endif; ?>
        </div>
    </details>

    <?php if ( ! empty( $promotions ) ) : ?>
        <details id="product-promotions" class="retail-purchase-information__item group border-t border-neutral-200 scroll-mt-28">
            <summary class="retail-purchase-information__summary flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700 sm:px-5 [&::-webkit-details-marker]:hidden">
                <span class="text-sm font-semibold text-neutral-950">
                    Promotions
                    <?php if ( count( $promotions ) > 1 ) : ?>
                        <span class="font-normal text-neutral-500">
                            (<?php echo esc_html( (string) count( $promotions ) ); ?>)
                        </span>
                    <?php endif; ?>
                </span>

                <?php $chevron(); ?>
            </summary>

            <div class="retail-purchase-information__content border-t border-neutral-200 bg-neutral-50 px-4 py-4 sm:px-5">
                <div class="space-y-4">
                    <?php foreach ( $promotions as $promotion ) : ?>
                        <?php
                        $classes = function_exists( 'blaines_get_transactional_promotion_classes' )
                            ? blaines_get_transactional_promotion_classes( $promotion['style'] ?? 'blue' )
                            : [
                                'panel' => 'border-blue-200 bg-blue-50',
                                'label' => 'text-blue-700',
                                'title' => 'text-blue-950',
                                'text'  => 'text-blue-900',
                                'link'  => 'text-blue-950',
                            ];

                        $period_label = function_exists( 'blaines_get_transactional_promotion_period_label' )
                            ? blaines_get_transactional_promotion_period_label( $promotion )
                            : '';

                        $details = ! empty( $promotion['details'] )
                            ? (string) $promotion['details']
                            : wpautop( esc_html( (string) ( $promotion['summary'] ?? '' ) ) );
                        ?>

                        <article class="<?php echo esc_attr( $classes['panel'] ); ?> border px-4 py-4">
                            <?php if ( ! empty( $promotion['title'] ) ) : ?>
                                <h3 class="<?php echo esc_attr( $classes['title'] ); ?> text-sm font-semibold leading-snug">
                                    <?php echo esc_html( (string) $promotion['title'] ); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if ( '' !== trim( wp_strip_all_tags( $details ) ) ) : ?>
                                <div class="<?php echo esc_attr( $classes['text'] ); ?> mt-2 text-sm font-light leading-relaxed [&_a]:font-medium [&_a]:underline [&_a]:underline-offset-4 [&_li]:ml-5 [&_li]:list-disc [&_p+p]:mt-2">
                                    <?php echo wp_kses_post( $details ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $period_label || ! empty( $promotion['terms_url'] ) ) : ?>
                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-current/15 pt-3 text-xs">
                                    <?php if ( $period_label ) : ?>
                                        <span class="<?php echo esc_attr( $classes['text'] ); ?> font-medium">
                                            <?php echo esc_html( $period_label ); ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ( ! empty( $promotion['terms_url'] ) ) : ?>
                                        <a
                                            class="<?php echo esc_attr( $classes['link'] ); ?> font-semibold underline underline-offset-4 hover:no-underline"
                                            href="<?php echo esc_url( (string) $promotion['terms_url'] ); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        ><?php echo esc_html( (string) $promotion['terms_label'] ); ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <p class="mt-4 text-xs font-light leading-relaxed text-neutral-500">
                    Promotions remain subject to their stated eligibility, claim requirements, availability and closing dates.
                </p>
            </div>
        </details>
    <?php endif; ?>

    <details class="retail-purchase-information__item group border-t border-neutral-200">
        <summary class="retail-purchase-information__summary flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-4 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700 sm:px-5 [&::-webkit-details-marker]:hidden">
            <span class="text-sm font-semibold text-neutral-950">
                Guarantee &amp; support
            </span>

            <?php $chevron(); ?>
        </summary>

        <div class="retail-purchase-information__content border-t border-neutral-200 bg-neutral-50 px-4 py-4 text-sm font-light leading-relaxed text-neutral-700 sm:px-5">
            <?php if ( $warranty_label || $warranty_detail || $warranty_registration_note ) : ?>
                <?php if ( $warranty_label ) : ?>
                    <p>
            <span class="font-medium text-neutral-950">
                <?php echo esc_html( $warranty_label ); ?>:
            </span>

                        <?php if ( $warranty_detail ) : ?>
                            <?php echo wp_kses_post( wpautop( $warranty_detail ) ); ?>
                        <?php endif; ?>
                    </p>
                <?php elseif ( $warranty_detail ) : ?>
                    <div>
                        <?php echo wp_kses_post( wpautop( $warranty_detail ) ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $warranty_registration_note ) : ?>
                    <div class="mt-3 text-xs font-light leading-relaxed text-neutral-500">
                        <?php echo wp_kses_post( wpautop( $warranty_registration_note ) ); ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <p>
                    This product is supplied with the applicable manufacturer&rsquo;s guarantee. The duration and terms vary by manufacturer and model.
                </p>
            <?php endif; ?>

            <p class="mt-3">
                Payments are processed securely online. For product, delivery or service advice, call Web Sales on
                <a
                    class="font-medium text-neutral-950 underline-offset-4 hover:underline"
                    href="tel:01760302111"
                >01760 302111</a>.
            </p>
        </div>
    </details>

</section>
