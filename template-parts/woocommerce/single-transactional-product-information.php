<?php
/**
 * Transactional product information.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id  = $product->get_id();
$description = (string) get_post_field(
    'post_content',
    $product_id
);

$has_description = '' !== trim(
    wp_strip_all_tags( $description )
);

$attributes     = $product->get_attributes();
$specifications = [];

foreach ( is_array( $attributes ) ? $attributes : [] as $attribute ) {
    if (
        ! $attribute instanceof WC_Product_Attribute
        || ! $attribute->get_visible()
    ) {
        continue;
    }

    $values = [];

    if ( $attribute->is_taxonomy() ) {
        $terms = wc_get_product_terms(
            $product_id,
            $attribute->get_name(),
            [ 'fields' => 'all' ]
        );

        $attribute_taxonomy = $attribute->get_taxonomy_object();

        foreach ( is_array( $terms ) ? $terms : [] as $term ) {
            if ( ! $term instanceof WP_Term ) {
                continue;
            }


            $values[] = esc_html( $term->name );
        }
    } else {
        foreach ( $attribute->get_options() as $option ) {
            $option = trim(
                wp_strip_all_tags( (string) $option )
            );

            if ( '' !== $option ) {
                $values[] = $option;
            }
        }
    }

    $values = array_values(
        array_unique(
            array_filter( $values )
        )
    );

    if ( empty( $values ) ) {
        continue;
    }

    $specifications[] = [
        'label' => wc_attribute_label(
            $attribute->get_name(),
            $product
        ),
        'value' => implode( ', ', $values ),
    ];
}

$ean = trim( (string) $product->get_global_unique_id() );

if ( '' === $ean ) {
    $ean = trim(
        (string) get_post_meta(
            $product_id,
            '_euronics_ean',
            true
        )
    );
}

if ( '' !== $ean ) {
    $specifications[] = [
        'label' => 'EAN',
        'value' => esc_html( $ean ),
    ];
}

$warranty = trim(
    (string) get_post_meta(
        $product_id,
        '_euronics_warranty',
        true
    )
);

if ( '' !== $warranty ) {
    $specifications[] = [
        'label' => 'Manufacturer’s guarantee',
        'value' => esc_html( $warranty ),
    ];
}

$has_specifications = ! empty( $specifications );

if ( ! $has_description && ! $has_specifications ) {
    return;
}

$default_tab = $has_description
    ? 'description'
    : 'specifications';

$has_tabs = (
    $has_description
    && $has_specifications
);
?>

<section
    class="retail-product-information"
    aria-labelledby="transactional-product-information-title"
    x-data="{ tab: '<?php echo esc_attr( $default_tab ); ?>' }"
>
    <header class="retail-product-information__header">

        <div>

            <h2
                id="transactional-product-information-title"
                class="retail-product-information__title"
            >
                Product information
            </h2>

            <p class="retail-product-information__intro">
                Detailed product information and manufacturer specifications.
            </p>

        </div>

        <?php if ( $has_tabs ) : ?>

            <div
                class="retail-product-information__tabs"
                role="tablist"
                aria-label="Product information sections"
            >
                <button
                    type="button"
                    id="transactional-tab-description"
                    role="tab"
                    aria-controls="transactional-panel-description"
                    :aria-selected="tab === 'description'"
                    @click="tab = 'description'"
                    class="retail-product-information__tab"
                    :class="tab === 'description' ? 'is-active' : ''"
                >
                    Description
                </button>

                <button
                    type="button"
                    id="transactional-tab-specifications"
                    role="tab"
                    aria-controls="transactional-panel-specifications"
                    :aria-selected="tab === 'specifications'"
                    @click="tab = 'specifications'"
                    class="retail-product-information__tab"
                    :class="tab === 'specifications' ? 'is-active' : ''"
                >
                    Specifications
                </button>
            </div>

        <?php endif; ?>

    </header>

    <?php if ( $has_description ) : ?>

        <div
            id="transactional-panel-description"
            role="tabpanel"
            <?php if ( $has_tabs ) : ?>
                aria-labelledby="transactional-tab-description"
            <?php else : ?>
                aria-labelledby="transactional-description-heading"
            <?php endif; ?>
            x-show="tab === 'description'"
            x-cloak
            class="retail-product-information__panel"
        >

            <?php if ( ! $has_tabs ) : ?>

                <h3
                    id="transactional-description-heading"
                    class="retail-product-information__panel-title"
                >
                    Description
                </h3>

            <?php endif; ?>

            <div class="retail-product-information__description prose-site prose-news">
                <?php
                echo wp_kses_post(
                    apply_filters(
                        'the_content',
                        $description
                    )
                );
                ?>
            </div>

        </div>

    <?php endif; ?>

    <?php if ( $has_specifications ) : ?>

        <div
            id="transactional-panel-specifications"
            role="tabpanel"
            <?php if ( $has_tabs ) : ?>
                aria-labelledby="transactional-tab-specifications"
            <?php else : ?>
                aria-labelledby="transactional-specifications-heading"
            <?php endif; ?>
            x-show="tab === 'specifications'"
            x-cloak
            class="retail-product-information__panel"
        >

            <?php if ( ! $has_tabs ) : ?>

                <h3
                    id="transactional-specifications-heading"
                    class="retail-product-information__panel-title"
                >
                    Specifications
                </h3>

            <?php endif; ?>

            <div class="retail-product-specifications">

                <table>
                    <tbody>

                        <?php foreach ( $specifications as $specification ) : ?>

                            <tr>

                                <th scope="row">
                                    <?php echo esc_html(
                                        $specification['label']
                                    ); ?>
                                </th>

                                <td>
                                    <?php echo wp_kses_post(
                                        $specification['value']
                                    ); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div>

        </div>

    <?php endif; ?>

</section>
