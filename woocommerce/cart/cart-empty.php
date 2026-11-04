<?php
/**
 * Empty cart page.
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * Preserve WooCommerce's normal empty-cart message and extension hooks.
 *
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );

$return_product = function_exists(
    'blaines_get_empty_cart_return_product'
)
    ? blaines_get_empty_cart_return_product()
    : null;

$electrical_url = home_url( '/electrical/' );
?>

<section class="blaines-retail blaines-retail--empty-basket">

    <div class="retail-empty-basket">

        <div class="retail-empty-basket__content">

            <p class="retail-empty-basket__status">
                Your basket is ready when you are
            </p>

            <h2>
                Continue shopping
            </h2>

            <?php if ( $return_product instanceof WC_Product ) : ?>

                <p>
                    <?php
                    printf(
                        esc_html__(
                            'Return to %s, or continue browsing our electrical range.',
                            'blainestwentysix'
                        ),
                        esc_html( $return_product->get_name() )
                    );
                    ?>
                </p>

            <?php else : ?>

                <p>
                    <?php
                    esc_html_e(
                        'Browse our electrical departments to find the right appliance, television or home technology product.',
                        'blainestwentysix'
                    );
                    ?>
                </p>

            <?php endif; ?>

            <div class="retail-empty-basket__actions">

                <?php if ( $return_product instanceof WC_Product ) : ?>

                    <a
                        href="<?php echo esc_url(
                            $return_product->get_permalink()
                        ); ?>"
                        class="retail-empty-basket__primary"
                    >
                        <?php esc_html_e(
                            'Return to product',
                            'blainestwentysix'
                        ); ?>
                    </a>

                <?php endif; ?>

                <a
                    href="<?php echo esc_url( $electrical_url ); ?>"
                    class="retail-empty-basket__secondary"
                >
                    <?php esc_html_e(
                        'Shop electrical',
                        'blainestwentysix'
                    ); ?>
                </a>

            </div>

        </div>

        <aside class="retail-empty-basket__help">

            <h3>Need help choosing?</h3>

            <p>
                Speak to Web Sales for product, delivery or service advice.
            </p>

            <a href="tel:01760302111">
                01760 302111
            </a>

        </aside>

    </div>

</section>
