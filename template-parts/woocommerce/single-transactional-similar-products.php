<?php
/**
 * Similar Products carousel for transactional products.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if (
    ! $product
    || ! function_exists(
        'blaines_get_similar_transactional_products'
    )
) {
    return;
}

$products = blaines_get_similar_transactional_products(
    $product,
    10
);

if ( count( $products ) < 3 ) {
    return;
}

$section_id = 'similar-products-' . $product->get_id();
?>

<section
    class="retail-similar-products"
    aria-labelledby="<?php echo esc_attr(
        $section_id
    ); ?>-heading"
    data-similar-products
>
    <header class="retail-similar-products__header">

        <div>

            <h2
                id="<?php echo esc_attr(
                    $section_id
                ); ?>-heading"
            >
                Similar products
            </h2>

            <p>
                Compare other products from the current Blaines electrical range.
            </p>

        </div>

        <div
            class="retail-similar-products__controls"
            data-similar-controls
        >
            <button
                type="button"
                class="retail-similar-products__control"
                aria-label="Previous similar products"
                data-similar-prev
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        d="M15 18l-6-6 6-6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>

            <button
                type="button"
                class="retail-similar-products__control"
                aria-label="Next similar products"
                data-similar-next
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        d="M9 6l6 6-6 6"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        </div>

    </header>

    <div
        class="retail-similar-products__track scrollbar-hide"
        tabindex="0"
        aria-label="Similar products"
        data-similar-track
    >

        <?php foreach ( $products as $similar_product ) : ?>

            <div
                class="retail-similar-products__item"
                data-similar-card
            >

                <?php
                get_template_part(
                    'template-parts/woocommerce/card-transactional-similar-product',
                    null,
                    [
                        'product' => $similar_product,
                    ]
                );
                ?>

            </div>

        <?php endforeach; ?>

    </div>

</section>
