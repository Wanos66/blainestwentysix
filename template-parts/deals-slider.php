<?php
/**
 * Featured transactional WooCommerce products slider.
 *
 * Optional arguments:
 * - title: Heading displayed above the slider.
 * - limit: Maximum number of products displayed.
 *
 * Products are selected using WooCommerce's native Featured flag.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
    return;
}

$args = wp_parse_args(
    $args ?? [],
    [
        'title' => 'Featured electrical products',
        'limit' => 12,
    ]
);

$default_subheading = 'A selection of current products, promotions and new arrivals available from Blaines.';

$feature_payload = function_exists( 'blaines_promotions_get_major_electrical_feature' )
    ? blaines_promotions_get_major_electrical_feature()
    : [];

$is_campaign_feature = false;
$feature_config       = [];
$campaign_config      = [];
$feature_hero         = [];
$feature_roundel      = [];
$campaign_url         = '';
$feature_title        = (string) $args['title'];
$feature_subheading   = $default_subheading;
$products             = [];

$product_is_eligible = static function ( $product ): bool {
    if ( ! $product instanceof WC_Product ) {
        return false;
    }

    $product_id = $product->get_id();

    if ( ! $product->is_visible() || ! $product->is_purchasable() ) {
        return false;
    }

    if (
        function_exists( 'blaines_is_transactional_product' )
        && ! blaines_is_transactional_product( $product_id )
    ) {
        return false;
    }

    return true;
};

if ( ! empty( $feature_payload['promotion_id'] ) ) {
    $feature_config  = (array) ( $feature_payload['feature'] ?? [] );
    $campaign_config = (array) ( $feature_payload['campaign'] ?? [] );
    $feature_hero    = (array) ( $feature_payload['hero'] ?? [] );
    $feature_roundel = (array) ( $feature_payload['roundel'] ?? [] );
    $campaign_url    = esc_url_raw( (string) ( $feature_payload['campaign_url'] ?? '' ) );
    $limit           = max( 1, min( 24, (int) ( $feature_config['product_limit'] ?? 10 ) ) );

    foreach ( (array) ( $feature_payload['product_ids'] ?? [] ) as $product_id ) {
        $product = wc_get_product( absint( $product_id ) );

        if ( $product_is_eligible( $product ) ) {
            $products[] = $product;
        }

        if ( count( $products ) >= $limit ) {
            break;
        }
    }

    if ( ! empty( $products ) ) {
        $is_campaign_feature = true;
        $feature_title       = trim( (string) ( $feature_config['heading'] ?? '' ) );
        $feature_subheading  = trim( (string) ( $feature_config['subheading'] ?? '' ) );

        if ( '' === $feature_title ) {
            $feature_title = (string) $args['title'];
        }
    }
}

/*
 * No active major campaign, or the selected campaign currently has no
 * displayable products: retain the normal WooCommerce Featured fallback.
 */
if ( ! $is_campaign_feature ) {
    $limit = max( 1, min( 24, (int) $args['limit'] ) );

    $products = wc_get_products(
        [
            'status'   => 'publish',
            'featured' => true,
            'limit'    => 30,
            'orderby'  => 'date',
            'order'    => 'DESC',
        ]
    );

    $products = array_values(
        array_filter(
            $products,
            $product_is_eligible
        )
    );

    $products = array_slice( $products, 0, $limit );
}

if ( empty( $products ) ) {
    return;
}

$product_count = count( $products );
$header_mode   = $is_campaign_feature
    ? (string) ( $feature_config['resolved_header_mode'] ?? 'styled' )
    : 'styled';
$show_campaign_cta = $is_campaign_feature
    && ! empty( $feature_config['show_cta'] )
    && '' !== $campaign_url;
$campaign_cta_label = trim( (string) ( $feature_config['cta_label'] ?? 'View all deals' ) );

$feature_background = (string) ( $feature_hero['background'] ?? '#0e295e' );
$feature_border     = (string) ( $feature_hero['border'] ?? '#0e295e' );
$feature_title_color = (string) ( $feature_hero['title'] ?? '#ffffff' );
$feature_subtitle_color = (string) ( $feature_hero['subtitle'] ?? '#ffffff' );

$banner_desktop_id  = absint( $campaign_config['banner_desktop_id'] ?? 0 );
$banner_mobile_id   = absint( $campaign_config['banner_mobile_id'] ?? 0 );
$banner_desktop_url = esc_url_raw( (string) ( $campaign_config['banner_desktop_url'] ?? '' ) );
$banner_mobile_url  = esc_url_raw( (string) ( $campaign_config['banner_mobile_url'] ?? '' ) );
$banner_alt         = $banner_desktop_id
    ? trim( (string) get_post_meta( $banner_desktop_id, '_wp_attachment_image_alt', true ) )
    : '';

if ( '' === $banner_alt ) {
    $banner_alt = $feature_title;
}

$roundel_enabled = $is_campaign_feature && ! empty( $feature_roundel['enabled'] );
$roundel_mode    = (string) ( $feature_roundel['mode'] ?? 'preset' );
$roundel_text    = trim( (string) ( $feature_roundel['display_text'] ?? $feature_roundel['text'] ?? '' ) );
$roundel_image   = 'image' === $roundel_mode
    ? esc_url_raw( (string) ( $feature_roundel['image_url'] ?? '' ) )
    : '';
?>

<section
        id="dealsSection"
        class="retail-featured-products<?php echo $is_campaign_feature ? ' retail-featured-products--campaign' : ''; ?>"
        aria-labelledby="featured-products-heading"
        <?php if ( $is_campaign_feature ) : ?>
            style="--blp-feature-background: <?php echo esc_attr( $feature_background ); ?>; --blp-feature-border: <?php echo esc_attr( $feature_border ); ?>; --blp-feature-title: <?php echo esc_attr( $feature_title_color ); ?>; --blp-feature-subtitle: <?php echo esc_attr( $feature_subtitle_color ); ?>;"
        <?php endif; ?>
>
    <div class="container-wide">

        <div class="retail-featured-products__shell">

            <?php if ( $is_campaign_feature && 'campaign_banner' === $header_mode && $banner_desktop_url ) : ?>

                <div class="retail-featured-products__campaign-banner">
                    <h2 id="featured-products-heading" class="screen-reader-text">
                        <?php echo esc_html( $feature_title ); ?>
                    </h2>

                    <picture>
                        <?php if ( $banner_mobile_url ) : ?>
                            <source media="(max-width: 767px)" srcset="<?php echo esc_url( $banner_mobile_url ); ?>">
                        <?php endif; ?>
                        <img
                                src="<?php echo esc_url( $banner_desktop_url ); ?>"
                                alt="<?php echo esc_attr( $banner_alt ); ?>"
                                loading="lazy"
                                decoding="async"
                        >
                    </picture>
                </div>

                <?php if ( $show_campaign_cta || $product_count > 1 ) : ?>
                    <div class="retail-featured-products__campaign-toolbar">
                        <?php if ( $show_campaign_cta ) : ?>
                            <a class="retail-featured-products__campaign-cta" href="<?php echo esc_url( $campaign_url ); ?>">
                                <?php echo esc_html( $campaign_cta_label ); ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( $product_count > 1 ) : ?>
                            <div class="retail-featured-products__controls">
                                <button id="prevDeal" type="button" aria-label="Show previous featured product" class="retail-featured-products__control">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m15 18-6-6 6-6"></path>
                                    </svg>
                                </button>
                                <button id="nextDeal" type="button" aria-label="Show next featured product" class="retail-featured-products__control">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>

                <header class="retail-featured-products__header">

                    <div>

                        <h2 id="featured-products-heading">
                            <?php echo esc_html( $feature_title ); ?>
                        </h2>

                        <?php if ( '' !== $feature_subheading ) : ?>
                            <p>
                                <?php echo esc_html( $feature_subheading ); ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <?php if ( $show_campaign_cta ) : ?>

                        <div class="retail-featured-products__campaign-actions">
                            <a class="retail-featured-products__campaign-cta" href="<?php echo esc_url( $campaign_url ); ?>">
                                <?php echo esc_html( $campaign_cta_label ); ?>
                            </a>

                            <?php if ( $product_count > 1 ) : ?>
                                <div class="retail-featured-products__controls">
                                    <button id="prevDeal" type="button" aria-label="Show previous featured product" class="retail-featured-products__control">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                            <path d="m15 18-6-6 6-6"></path>
                                        </svg>
                                    </button>
                                    <button id="nextDeal" type="button" aria-label="Show next featured product" class="retail-featured-products__control">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                            <path d="m9 18 6-6-6-6"></path>
                                        </svg>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php elseif ( $product_count > 1 ) : ?>

                        <div class="retail-featured-products__controls">
                            <button id="prevDeal" type="button" aria-label="Show previous featured product" class="retail-featured-products__control">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                    <path d="m15 18-6-6 6-6"></path>
                                </svg>
                            </button>
                            <button id="nextDeal" type="button" aria-label="Show next featured product" class="retail-featured-products__control">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                        </div>

                    <?php endif; ?>

                </header>

            <?php endif; ?>

            <div class="retail-featured-products__body">

                <div class="relative overflow-hidden">

                    <div
                            id="dealTrack"
                            class="retail-featured-products__track"
                            aria-live="off"
                    >

                        <?php foreach ( $products as $product ) : ?>

                            <?php
                            $product_url  = $product->get_permalink();
                            $product_name = $product->get_name();
                            $product_id   = $product->get_id();

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

                            $regular_price = $product->get_regular_price();
                            $sale_price    = $product->get_sale_price();
                            $current_price = $product->get_price();

                            $has_sale = (
                                $product->is_on_sale()
                                && '' !== $regular_price
                                && '' !== $sale_price
                                && (float) $regular_price > (float) $sale_price
                            );

                            $regular_display_price = $has_sale
                                ? (float) wc_get_price_to_display(
                                    $product,
                                    [ 'price' => (float) $regular_price ]
                                )
                                : 0;

                            $sale_display_price = $has_sale
                                ? (float) wc_get_price_to_display(
                                    $product,
                                    [ 'price' => (float) $sale_price ]
                                )
                                : 0;

                            $current_display_price = (float) wc_get_price_to_display(
                                $product,
                                [ 'price' => (float) $current_price ]
                            );

                            $saving = $has_sale
                                ? max(
                                    0,
                                    $regular_display_price - $sale_display_price
                                )
                                : 0;

                            $saving_decimals = (
                                $saving > 0
                                && floor( $saving ) === $saving
                            )
                                ? 0
                                : wc_get_price_decimals();
                            ?>

                            <article class="retail-featured-card">

                                <a
                                        href="<?php echo esc_url( $product_url ); ?>"
                                        class="retail-featured-card__link"
                                        aria-label="<?php echo esc_attr(
                                            sprintf(
                                                'View %s',
                                                $product_name
                                            )
                                        ); ?>"
                                >

                                    <div class="retail-featured-card__image-frame">

                                        <?php if ( $has_sale && $saving > 0 ) : ?>

                                            <span class="retail-featured-card__saving">

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

                                        <?php if ( $roundel_enabled && ( '' !== $roundel_text || '' !== $roundel_image ) ) : ?>
                                            <span
                                                    class="blp-electrical-feature-roundel<?php echo '' !== $roundel_image ? ' blp-electrical-feature-roundel--image' : ''; ?>"
                                                    style="--blp-roundel-bg: <?php echo esc_attr( (string) ( $feature_roundel['background'] ?? '#111111' ) ); ?>; --blp-roundel-border: <?php echo esc_attr( (string) ( $feature_roundel['border'] ?? '#f2c94c' ) ); ?>; --blp-roundel-text: <?php echo esc_attr( (string) ( $feature_roundel['text_color'] ?? '#f2c94c' ) ); ?>;"
                                                    aria-hidden="true"
                                            >
                                                <?php if ( '' !== $roundel_image ) : ?>
                                                    <img src="<?php echo esc_url( $roundel_image ); ?>" alt="">
                                                <?php else : ?>
                                                    <span><?php echo esc_html( $roundel_text ); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>

                                        <div class="retail-featured-card__image">

                                            <?php
                                            echo $product->get_image(
                                                'woocommerce_thumbnail',
                                                [
                                                    'class'    => 'retail-featured-card__product-image',
                                                    'alt'      => esc_attr( $product_name ),
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                ]
                                            );
                                            ?>

                                        </div>

                                    </div>

                                    <div class="retail-featured-card__content">

                                        <div class="retail-featured-card__brand">
                                            <?php if ( $brand_logo_id ) : ?>
                                                <?php
                                                echo wp_get_attachment_image(
                                                    $brand_logo_id,
                                                    'medium',
                                                    false,
                                                    [
                                                        'class' => 'retail-featured-card__brand-image',
                                                        'alt'   => esc_attr( $brand_name ?: $product_name . ' brand' ),
                                                    ]
                                                );
                                                ?>
                                            <?php elseif ( '' !== $brand_name ) : ?>
                                                <span class="retail-featured-card__brand-name">
                                                    <?php echo esc_html( $brand_name ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="retail-featured-card__title">
                                            <?php echo esc_html( $product_name ); ?>
                                        </h3>

                                        <div class="retail-featured-card__price-block">

                                            <?php if ( $has_sale ) : ?>

                                                <p class="retail-featured-card__was-price">
                                                    Was
                                                    <del>
                                                        <?php
                                                        echo wp_kses_post(
                                                            wc_price(
                                                                $regular_display_price
                                                            )
                                                        );
                                                        ?>
                                                    </del>
                                                </p>

                                                <p class="retail-featured-card__price retail-featured-card__price--sale">
                                                    <?php
                                                    echo wp_kses_post(
                                                        wc_price(
                                                            $sale_display_price
                                                        )
                                                    );
                                                    ?>
                                                </p>

                                            <?php else : ?>

                                                <p class="retail-featured-card__price">
                                                    <?php
                                                    echo wp_kses_post(
                                                        wc_price(
                                                            $current_display_price
                                                        )
                                                    );
                                                    ?>
                                                </p>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>
