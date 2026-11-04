<?php
/**
 * Transactional WooCommerce product row.
 *
 * Used for electrical archive/category product listings.
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

if (
    function_exists( 'blaines_is_transactional_product' )
    && ! blaines_is_transactional_product( $product )
) {
    return;
}

$product_id    = $product->get_id();
$title         = get_the_title( $product_id );
$permalink     = get_permalink( $product_id );
$image_id      = $product->get_image_id();
$sku           = $product->get_sku();
$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$is_on_sale    = (
    $product->is_on_sale()
    && $regular_price > $sale_price
);
$saving        = $is_on_sale
    ? max( 0, $regular_price - $sale_price )
    : 0;

$saving_decimals = (
    $saving > 0
    && floor( $saving ) === $saving
)
    ? 0
    : wc_get_price_decimals();

$short_desc = $product->get_short_description();

$brand_logo_value = function_exists( 'get_field' )
    ? get_field( 'product_brand_logo', $product_id )
    : null;

$brand_logo_id = 0;

if ( is_array( $brand_logo_value ) ) {
    $brand_logo_id = absint(
        $brand_logo_value['ID']
        ?? $brand_logo_value['id']
        ?? 0
    );
} else {
    $brand_logo_id = absint( $brand_logo_value );
}

$key_features = function_exists( 'blaines_get_product_key_features' )
    ? blaines_get_product_key_features( $product_id )
    : [];

$energy_rating_image_id = function_exists(
    'blaines_get_product_energy_rating_image_id'
)
    ? blaines_get_product_energy_rating_image_id( $product_id )
    : 0;

$active_promotions = function_exists( 'blaines_get_product_promotions' )
    ? blaines_get_product_promotions( $product_id, true )
    : [];

/*
 * Archive roundel: use the highest-priority active promotion whose central
 * promotion configuration has the roundel enabled. The promotion array is
 * already priority-sorted by the Promotions Manager.
 */
$archive_roundel = null;

foreach ( is_array( $active_promotions ) ? $active_promotions : [] as $promotion ) {
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

    $roundel_mode = (string) ( $roundel['mode'] ?? 'preset' );
    $roundel_image_url = 'image' === $roundel_mode
        ? esc_url_raw( (string) ( $roundel['image_url'] ?? '' ) )
        : '';

    if ( '' === $roundel_text && '' === $roundel_image_url ) {
        continue;
    }

    $archive_roundel = [
        'promotion_id' => absint( $promotion['id'] ?? 0 ),
        'title'        => trim( (string) ( $promotion['title'] ?? '' ) ),
        'mode'         => $roundel_mode,
        'text'         => $roundel_text,
        'label'        => trim( (string) ( $roundel['accessible_label'] ?? $roundel_text ?? $promotion['title'] ?? '' ) ),
        'image_url'    => $roundel_image_url,
        'background'   => sanitize_hex_color( (string) ( $roundel['background'] ?? '' ) ) ?: '#111111',
        'border'       => sanitize_hex_color( (string) ( $roundel['border'] ?? '' ) ) ?: '#f2c94c',
        'text_color'   => sanitize_hex_color( (string) ( $roundel['text_color'] ?? '' ) ) ?: '#f2c94c',
    ];

    break;
}
?>

<article
    <?php
    wc_product_class(
        'retail-product-row font-body',
        $product
    );
    ?>
>
    <div class="retail-product-row__inner grid gap-6 md:grid-cols-12 md:gap-7 lg:gap-8">

        <div class="retail-product-row__media md:col-span-4 lg:col-span-3">

            <a
                href="<?php echo esc_url( $permalink ); ?>"
                class="retail-product-row__image-link"
                aria-label="<?php echo esc_attr(
                    sprintf(
                        'View %s',
                        $title
                    )
                ); ?>"
            >

                <div class="retail-product-row__image-frame">

                    <?php if ( $image_id ) : ?>

                        <?php
                        echo wp_get_attachment_image(
                            $image_id,
                            'large',
                            false,
                            [
                                'class'    => 'retail-product-row__image',
                                'alt'      => esc_attr( $title ),
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]
                        );
                        ?>

                    <?php else : ?>

                        <div class="retail-product-row__image-placeholder">
                            <span>Product image coming soon</span>
                        </div>

                    <?php endif; ?>

                </div>

            </a>

            <?php if ( $energy_rating_image_id || $archive_roundel ) : ?>

                <div class="retail-product-row__media-badges">

                    <?php if ( $energy_rating_image_id ) : ?>

                        <div class="retail-product-row__media-energy">

                            <?php
                            echo wp_get_attachment_image(
                                $energy_rating_image_id,
                                'thumbnail',
                                false,
                                [
                                    'class' => 'retail-product-row__energy-image',
                                    'alt'   => esc_attr(
                                        $title . ' energy rating'
                                    ),
                                ]
                            );
                            ?>

                        </div>

                    <?php endif; ?>

                    <?php if ( $archive_roundel ) : ?>

                        <a
                            href="<?php echo esc_url( $permalink . '#product-promotions' ); ?>"
                            class="retail-product-row__promotion-roundel<?php echo '' !== $archive_roundel['image_url'] ? ' retail-product-row__promotion-roundel--image' : ''; ?>"
                            style="--retail-promotion-roundel-bg: <?php echo esc_attr( $archive_roundel['background'] ); ?>; --retail-promotion-roundel-border: <?php echo esc_attr( $archive_roundel['border'] ); ?>; --retail-promotion-roundel-text: <?php echo esc_attr( $archive_roundel['text_color'] ); ?>;"
                            aria-label="<?php echo esc_attr( 'Promotion: ' . ( $archive_roundel['label'] ?: $archive_roundel['title'] ?: 'Promotion' ) ); ?>"
                        >
                            <?php if ( '' !== $archive_roundel['image_url'] ) : ?>
                                <img
                                    src="<?php echo esc_url( $archive_roundel['image_url'] ); ?>"
                                    alt=""
                                    class="retail-product-row__promotion-roundel-image"
                                    loading="lazy"
                                    decoding="async"
                                >
                            <?php else : ?>
                                <span><?php echo esc_html( $archive_roundel['text'] ); ?></span>
                            <?php endif; ?>
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

        <div class="retail-product-row__information md:col-span-8 lg:col-span-6">

            <?php if ( $sku ) : ?>

                <p class="retail-product-row__code">
                    Product code: <?php echo esc_html( $sku ); ?>
                </p>

            <?php endif; ?>

            <h2 class="retail-product-row__title">

                <a href="<?php echo esc_url( $permalink ); ?>">
                    <?php echo esc_html( $title ); ?>
                </a>

            </h2>

            <?php if ( ! empty( $key_features ) ) : ?>

                <ul class="retail-product-row__features">

                    <?php foreach ( array_slice( $key_features, 0, 5 ) as $feature ) : ?>

                        <li>
                            <span
                                class="retail-product-row__feature-marker"
                                aria-hidden="true"
                            ></span>

                            <span>
                                <?php echo esc_html( $feature ); ?>
                            </span>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php elseif ( $short_desc ) : ?>

                <div class="retail-product-row__description">
                    <?php
                    echo wp_kses_post(
                        wp_trim_words(
                            wp_strip_all_tags( $short_desc ),
                            28
                        )
                    );
                    ?>
                </div>

            <?php endif; ?>

            <?php if ( ! empty( $active_promotions ) ) : ?>

                <div class="retail-product-row__supporting">

                    <?php
                    get_template_part(
                        'template-parts/woocommerce/row-transactional-promotion-titles',
                        null,
                        [
                            'product'    => $product,
                            'promotions' => $active_promotions,
                        ]
                    );
                    ?>

                </div>

            <?php endif; ?>

        </div>

        <div class="retail-product-row__purchase md:col-span-12 lg:col-span-3">

            <div
                class="retail-product-row__brand"
                <?php echo $brand_logo_id ? '' : 'aria-hidden="true"'; ?>
            >

                <?php if ( $brand_logo_id ) : ?>

                    <?php
                    echo wp_get_attachment_image(
                        $brand_logo_id,
                        'medium',
                        false,
                        [
                            'class'    => 'retail-product-row__brand-image',
                            'alt'      => esc_attr(
                                $title . ' brand logo'
                            ),
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                        ]
                    );
                    ?>

                <?php endif; ?>

            </div>

            <div class="retail-product-row__commercial">

                <div class="retail-product-row__pricing">

                    <?php if ( $is_on_sale ) : ?>

                        <p class="retail-product-row__was-price">
                            Was
                            <del>
                                <?php
                                echo wp_kses_post(
                                    wc_price( $regular_price )
                                );
                                ?>
                            </del>
                        </p>

                        <p class="retail-product-row__price retail-product-row__price--sale">
                            <?php
                            echo wp_kses_post(
                                wc_price( $sale_price )
                            );
                            ?>
                        </p>

                        <?php if ( $saving > 0 ) : ?>

                            <p class="retail-product-row__saving">

                                <span class="retail-product-row__saving-label">
                                    Save
                                </span>

                                <span class="retail-product-row__saving-value">
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

                            </p>

                        <?php endif; ?>

                    <?php else : ?>

                        <p class="retail-product-row__price">
                            <?php
                            echo wp_kses_post(
                                wc_price(
                                    (float) $product->get_price()
                                )
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>

                <div class="retail-product-row__availability">

                    <?php
                    if (
                        function_exists(
                            'blaines_render_transactional_availability_message'
                        )
                    ) {
                        blaines_render_transactional_availability_message(
                            $product,
                            'loop'
                        );
                    }
                    ?>

                </div>

                <a
                    href="<?php echo esc_url( $permalink ); ?>"
                    class="retail-product-row__action"
                >
                    View product
                </a>

            </div>

        </div>

    </div>
</article>
