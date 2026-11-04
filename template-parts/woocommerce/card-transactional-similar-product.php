<?php
/**
 * Compact product card used by the Similar Products carousel.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id    = $product->get_id();
$product_title = $product->get_name();
$product_url   = $product->get_permalink();
$image_id      = $product->get_image_id();
$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$current_price = (float) $product->get_price();
$is_on_sale    = $product->is_on_sale() && $sale_price > 0 && $regular_price > $sale_price;
$saving        = $is_on_sale ? max( 0, $regular_price - $sale_price ) : 0;

$saving_decimals = (
    $saving > 0
    && floor( $saving ) === $saving
)
    ? 0
    : wc_get_price_decimals();

$brand_logo_id = 0;

if ( function_exists( 'get_field' ) ) {
    $brand_logo = get_field( 'product_brand_logo', $product_id );

    if ( is_array( $brand_logo ) ) {
        $brand_logo_id = absint( $brand_logo['ID'] ?? $brand_logo['id'] ?? 0 );
    } else {
        $brand_logo_id = absint( $brand_logo );
    }
}

$brand_name = trim( (string) get_post_meta( $product_id, '_euronics_brand_name', true ) );
$promotions = function_exists( 'blaines_get_product_promotions' )
    ? blaines_get_product_promotions( $product_id, true )
    : [];

/*
 * Similar Products roundel: use the highest-priority active promotion whose
 * central configuration has a roundel enabled. The promotions array is already
 * priority-sorted by Blaines Promotions.
 */
$similar_roundel = null;

foreach ( is_array( $promotions ) ? $promotions : [] as $promotion ) {
    if ( ! is_array( $promotion ) || empty( $promotion['active'] ) ) {
        continue;
    }

    $roundel = isset( $promotion['roundel'] ) && is_array( $promotion['roundel'] )
        ? $promotion['roundel']
        : [];

    if ( empty( $roundel['enabled'] ) ) {
        continue;
    }

    $roundel_text = trim(
        (string) (
            $roundel['display_text']
            ?? $roundel['text']
            ?? $promotion['title']
            ?? ''
        )
    );

    $roundel_mode      = (string) ( $roundel['mode'] ?? 'preset' );
    $roundel_image_url = 'image' === $roundel_mode
        ? esc_url_raw( (string) ( $roundel['image_url'] ?? '' ) )
        : '';

    if ( '' === $roundel_text && '' === $roundel_image_url ) {
        continue;
    }

    $similar_roundel = [
        'title'      => trim( (string) ( $promotion['title'] ?? '' ) ),
        'text'       => $roundel_text,
        'label'      => trim( (string) ( $roundel['accessible_label'] ?? $roundel_text ?? $promotion['title'] ?? '' ) ),
        'image_url'  => $roundel_image_url,
        'background' => sanitize_hex_color( (string) ( $roundel['background'] ?? '' ) ) ?: '#111111',
        'border'     => sanitize_hex_color( (string) ( $roundel['border'] ?? '' ) ) ?: '#f2c94c',
        'text_color' => sanitize_hex_color( (string) ( $roundel['text_color'] ?? '' ) ) ?: '#f2c94c',
    ];

    break;
}

?>

<article class="flex h-full flex-col font-body">
    <a
        href="<?php echo esc_url( $product_url ); ?>"
        class="group block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
    >
        <div class="similar-product-card__media flex aspect-[4/3] items-center justify-center border border-neutral-200 bg-white p-4 transition group-hover:border-neutral-400">
            <?php if ( $is_on_sale && $saving > 0 ) : ?>
                <span class="retail-similar-card__saving">
                    <span>Save</span>
                    <span>
                        <?php
                        echo wp_kses_post(
                            wc_price(
                                $saving,
                                [
                                    'decimals' => $saving_decimals,
                                ]
                            )
                        );
                        ?>
                    </span>
                </span>
            <?php endif; ?>

            <?php if ( $image_id ) : ?>
                <?php
                echo wp_get_attachment_image(
                    $image_id,
                    'woocommerce_thumbnail',
                    false,
                    [
                        'class'   => 'h-full w-full object-contain',
                        'alt'     => esc_attr( $product_title ),
                        'loading' => 'lazy',
                    ]
                );
                ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>"
                    alt="<?php echo esc_attr( $product_title ); ?>"
                    class="h-full w-full object-contain"
                    loading="lazy"
                >
            <?php endif; ?>

            <?php if ( $similar_roundel ) : ?>
                <span
                    class="similar-product-card__promotion-roundel<?php echo '' !== $similar_roundel['image_url'] ? ' similar-product-card__promotion-roundel--image' : ''; ?>"
                    style="--similar-promotion-roundel-bg: <?php echo esc_attr( $similar_roundel['background'] ); ?>; --similar-promotion-roundel-border: <?php echo esc_attr( $similar_roundel['border'] ); ?>; --similar-promotion-roundel-text: <?php echo esc_attr( $similar_roundel['text_color'] ); ?>;"
                    aria-label="<?php echo esc_attr( 'Promotion: ' . ( $similar_roundel['label'] ?: $similar_roundel['title'] ?: 'Promotion' ) ); ?>"
                >
                    <?php if ( '' !== $similar_roundel['image_url'] ) : ?>
                        <img
                            src="<?php echo esc_url( $similar_roundel['image_url'] ); ?>"
                            alt=""
                            class="similar-product-card__promotion-roundel-image"
                            loading="lazy"
                            decoding="async"
                        >
                    <?php else : ?>
                        <span><?php echo esc_html( $similar_roundel['text'] ); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <div class="flex flex-1 flex-col pt-3">
        <div class="mb-2 flex h-7 items-center">
            <?php if ( $brand_logo_id ) : ?>
                <?php
                echo wp_get_attachment_image(
                    $brand_logo_id,
                    'medium',
                    false,
                    [
                        'class' => 'max-h-7 w-auto max-w-28 object-contain object-left',
                        'alt'   => esc_attr( $brand_name ?: $product_title . ' brand' ),
                    ]
                );
                ?>
            <?php elseif ( '' !== $brand_name ) : ?>
                <span class="text-xs font-semibold uppercase tracking-[0.12em] text-neutral-500">
                    <?php echo esc_html( $brand_name ); ?>
                </span>
            <?php endif; ?>
        </div>

        <h3 class="min-h-[3.75rem] line-clamp-3 text-sm font-semibold leading-snug text-neutral-950">
            <a class="transition hover:text-blue-700" href="<?php echo esc_url( $product_url ); ?>">
                <?php echo esc_html( $product_title ); ?>
            </a>
        </h3>

        <div class="pt-2">
            <div class="min-h-4">
                <?php if ( $is_on_sale ) : ?>
                    <p class="text-xs text-neutral-500">
                        Was
                        <span class="line-through">
                            <?php echo wp_kses_post( wc_price( $regular_price ) ); ?>
                        </span>
                    </p>
                <?php endif; ?>
            </div>

            <p class="retail-similar-card__price<?php echo $is_on_sale ? ' retail-similar-card__price--sale' : ''; ?>">
                <?php
                echo wp_kses_post(
                    wc_price(
                        $is_on_sale
                            ? $sale_price
                            : $current_price
                    )
                );
                ?>
            </p>

        </div>
    </div>
</article>
