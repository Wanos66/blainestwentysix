<?php
/**
 * Commerce page header.
 *
 * Used for Basket, Checkout and Order Confirmation pages.
 *
 * @package Blaines
 *
 * @var array<string, mixed> $args
 */

defined( 'ABSPATH' ) || exit;

$context = isset( $args['context'] ) && is_array( $args['context'] )
    ? $args['context']
    : [];

$is_cart_page           = ! empty( $context['is_cart_page'] );
$is_checkout_page       = ! empty( $context['is_checkout_page'] );
$is_order_received_page = ! empty( $context['is_order_received_page'] );

$commerce_title = isset( $context['title'] )
    ? (string) $context['title']
    : '';

$commerce_intro = isset( $context['intro'] )
    ? (string) $context['intro']
    : '';

$commerce_order_card_link = isset( $context['order_card_link'] )
    ? (string) $context['order_card_link']
    : '';

$commerce_order_card_link_label = isset(
    $context['order_card_link_label']
)
    ? (string) $context['order_card_link_label']
    : '';

$commerce_steps = isset( $context['steps'] )
    && is_array( $context['steps'] )
    ? $context['steps']
    : [];

$page_modifier = 'commerce-page-header--default';

if ( $is_cart_page ) {
    $page_modifier = 'commerce-page-header--basket';
} elseif ( $is_checkout_page ) {
    $page_modifier = 'commerce-page-header--checkout';
} elseif ( $is_order_received_page ) {
    $page_modifier = 'commerce-page-header--confirmation';
}

$help_text = 'Delivery, collection and service advice from Web Sales.';

if ( $is_cart_page ) {
    $help_text = 'Questions about products, delivery or optional services?';
} elseif ( $is_checkout_page ) {
    $help_text = 'Need to confirm delivery, collection or your selected services?';
} elseif ( $is_order_received_page ) {
    $help_text = 'Quote your order number if you need to speak to Web Sales.';
}
?>


<?php if ( $is_checkout_page ) : ?>
    <style>
        .commerce-page-header--checkout .commerce-page-header__return-link {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            margin-top: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.62);
            padding: 0.65rem 0.8rem;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.35;
            letter-spacing: 0.055em;
            text-decoration: none;
            text-transform: uppercase;
            transition:
                background-color 160ms ease,
                border-color 160ms ease,
                color 160ms ease;
        }

        .commerce-page-header--checkout .commerce-page-header__return-link:hover,
        .commerce-page-header--checkout .commerce-page-header__return-link:focus {
            border-color: #fff;
            background: #fff;
            color: #143981;
        }

        .commerce-page-header--checkout .commerce-page-header__return-link-icon {
            flex: 0 0 auto;
            font-size: 1rem;
            line-height: 1;
        }
    </style>
<?php endif; ?>

<div class="commerce-page-breadcrumbs container-wide">
    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
</div>

<section class="commerce-page-header <?php echo esc_attr(
    $page_modifier
); ?>">

    <div class="container-wide">

        <?php if ( ! $is_order_received_page ) : ?>

            <div class="commerce-page-header__hero">

                <div class="commerce-page-header__content">

                    <?php if ( $commerce_title ) : ?>

                        <h1>
                            <?php echo esc_html( $commerce_title ); ?>
                        </h1>

                    <?php endif; ?>

                    <?php if ( $commerce_intro ) : ?>

                        <p>
                            <?php echo esc_html( $commerce_intro ); ?>
                        </p>

                    <?php endif; ?>

                </div>

                <aside class="commerce-page-header__help">

                    <p class="commerce-page-header__help-label">
                        Web Sales
                    </p>

                    <a href="tel:01760302111">
                        01760 302111
                    </a>

                    <p>
                        <?php echo esc_html( $help_text ); ?>
                    </p>

                    <?php if (
                        $commerce_order_card_link
                        && $commerce_order_card_link_label
                    ) : ?>

                        <a
                            href="<?php echo esc_url(
                                $commerce_order_card_link
                            ); ?>"
                            class="commerce-page-header__return-link"
                        >
                            <span
                                class="commerce-page-header__return-link-icon"
                                aria-hidden="true"
                            >←</span>
                            <span>
                                <?php echo esc_html(
                                    $commerce_order_card_link_label
                                ); ?>
                            </span>
                        </a>

                    <?php endif; ?>

                </aside>

            </div>

        <?php endif; ?>

        <?php if ( ! empty( $commerce_steps ) ) : ?>

            <nav
                class="commerce-progress"
                aria-label="Order progress"
            >

                <ol>

                    <?php foreach ( $commerce_steps as $index => $step ) : ?>

                        <?php
                        $step_number = $index + 1;
                        $step_label  = isset( $step['label'] )
                            ? (string) $step['label']
                            : '';

                        $is_active   = ! empty( $step['active'] );
                        $is_complete = ! empty( $step['complete'] );

                        $step_classes = [ 'commerce-progress__step' ];

                        if ( $is_active ) {
                            $step_classes[] = 'is-active';
                        } elseif ( $is_complete ) {
                            $step_classes[] = 'is-complete';
                        }
                        ?>

                        <li class="<?php echo esc_attr(
                            implode( ' ', $step_classes )
                        ); ?>"
                            <?php if ( $is_active ) : ?>
                                aria-current="step"
                            <?php endif; ?>
                        >

                            <span class="commerce-progress__number">
                                <?php if ( $is_complete ) : ?>
                                    <span aria-hidden="true">✓</span>
                                    <span class="sr-only">Complete</span>
                                <?php else : ?>
                                    <?php echo esc_html(
                                        (string) $step_number
                                    ); ?>
                                <?php endif; ?>
                            </span>

                            <?php if ( $step_label ) : ?>

                                <span class="commerce-progress__label">
                                    <?php echo esc_html( $step_label ); ?>
                                </span>

                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                </ol>

            </nav>

        <?php endif; ?>

    </div>

</section>
