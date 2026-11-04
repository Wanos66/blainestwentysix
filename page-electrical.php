<?php
/**
 * Electrical landing page.
 *
 * Customer-facing landing page for Blaines transactional
 * electrical departments and featured products.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

get_header();

$homepage_id = (int) get_option( 'page_on_front' );

if ( $homepage_id <= 0 ) {
    $homepage_id = get_queried_object_id();
}

$electrical_page_id = get_queried_object_id();

$department_images = function_exists( 'get_field' )
    ? get_field( 'electrical_department_images', $electrical_page_id )
    : [];

if ( ! is_array( $department_images ) ) {
    $department_images = [];
}

$departments = [
    [
        'title'       => 'Laundry',
        'description' => 'Washing machines, tumble dryers and washer dryers available with local delivery or collection.',
        'url'         => home_url( '/electrical/laundry/' ),
        'image_id'    => absint( $department_images['laundry'] ?? 0 ),
        'label'       => 'Washing machines · Dryers',
    ],
    [
        'title'       => 'Refrigeration',
        'description' => 'Fridges, freezers, fridge freezers, wine coolers and American style refrigeration.',
        'url'         => home_url( '/electrical/refrigeration/' ),
        'image_id'    => absint( $department_images['refrigeration'] ?? 0 ),
        'label'       => 'Cooling · Freezing',
    ],
    [
        'title'       => 'Dishwashers',
        'description' => 'Full size and slimline dishwashers from trusted appliance brands.',
        'url'         => home_url( '/electrical/dishwashers/' ),
        'image_id'    => absint( $department_images['dishwashers'] ?? 0 ),
        'label'       => 'Full size · Slimline',
    ],
    [
        'title'       => 'Cooking',
        'description' => 'Cookers, ovens, hobs, microwaves and range cookers.',
        'url'         => home_url( '/electrical/cooking/' ),
        'image_id'    => absint( $department_images['cooking'] ?? 0 ),
        'label'       => 'Ovens · Hobs · Cookers',
    ],
    [
        'title'       => 'Small Appliances',
        'description' => 'Everyday essentials including kettles, toasters, food preparation and small cooking appliances.',
        'url'         => home_url( '/electrical/small-appliances/' ),
        'image_id'    => absint( $department_images['small_appliances'] ?? 0 ),
        'label'       => 'Everyday essentials',
    ],
    [
        'title'       => 'Floorcare',
        'description' => 'Vacuum cleaners, steam cleaners and carpet washers for everyday home care.',
        'url'         => home_url( '/electrical/floorcare/' ),
        'image_id'    => absint( $department_images['floorcare'] ?? 0 ),
        'label'       => 'Vacuum cleaners · Floorcare',
    ],
    [
        'title'       => 'TV & Entertainment',
        'description' => 'Televisions, soundbars, home cinema, audio and TV accessories.',
        'url'         => home_url( '/electrical/tv-entertainment/' ),
        'image_id'    => absint( $department_images['tv_entertainment'] ?? 0 ),
        'label'       => 'TV · Audio · Cinema',
    ],
];
?>

<div class="blaines-retail blaines-retail--landing">

    <div class="container-wide retail-landing__breadcrumb">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>

    <section class="container-wide retail-hero">
        <div class="retail-hero__frame">
            <?php
            get_template_part(
                'template-parts/main-slider',
                null,
                [
                    'aria_label' => 'Electrical promotions',
                    'source_id'  => $homepage_id,
                    'context'    => 'electrical',
                ]
            );
            ?>
        </div>
    </section>

    <section
            class="retail-intro"
            aria-labelledby="electrical-page-title"
    >
        <div class="container-wide">

            <div class="retail-intro__grid">

                <header class="retail-intro__content">

                    <h1
                            id="electrical-page-title"
                            class="retail-intro__title"
                    >
                        Electrical appliances, TVs &amp; audio
                    </h1>

                    <p class="retail-intro__copy">
                        Shop leading appliances, televisions and audio online, backed by local delivery, installation options and expert support from Blaines.
                    </p>

                    <div class="retail-intro__actions">

                        <a
                                href="#electrical-departments"
                                class="retail-button retail-button--primary"
                        >
                            Shop by department
                        </a>

                    </div>

                </header>

                <aside
                        class="retail-intro__support"
                        aria-labelledby="electrical-web-sales-heading"
                >

                    <h2 id="electrical-web-sales-heading">
                        Need help choosing?
                    </h2>

                    <p>
                        Speak directly to our Web Sales team for practical product advice before you order.
                    </p>

                    <a
                            href="tel:01760302111"
                            class="retail-intro__phone"
                    >
                        01760 302111
                    </a>

                    <p class="retail-intro__phone-note">
                        Speak to Web Sales
                    </p>

                </aside>

            </div>

        </div>
    </section>

    <section
            class="retail-assurance-section"
            aria-label="Electrical shopping benefits"
    >
        <div class="container-wide">
            <div class="retail-assurance">
                <div class="retail-assurance__grid">

            <article class="retail-assurance__item">

                <span
                        class="retail-assurance__icon"
                        aria-hidden="true"
                >
                    <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                    >
                        <path d="M3 6h11v10H3z"></path>
                        <path d="M14 9h4l3 3v4h-7z"></path>
                        <circle cx="7" cy="18" r="2"></circle>
                        <circle cx="18" cy="18" r="2"></circle>
                    </svg>
                </span>

                <div>
                    <h2>Free local delivery</h2>
                    <p>On qualifying orders of £400 or more</p>
                </div>

            </article>

            <article class="retail-assurance__item">

                <span
                        class="retail-assurance__icon"
                        aria-hidden="true"
                >
                    <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                    >
                        <path d="M14.7 6.3a4 4 0 0 0-5-5L7.3 3.7l3 3 2.4-2.4"></path>
                        <path d="m10.3 6.7-8.1 8.1a2.1 2.1 0 0 0 3 3l8.1-8.1"></path>
                        <path d="m15 13 6 6"></path>
                        <path d="m18 10 3 3"></path>
                    </svg>
                </span>

                <div>
                    <h2>Installation services</h2>
                    <p>Available for selected appliances</p>
                </div>

            </article>

            <article class="retail-assurance__item">

                <span
                        class="retail-assurance__icon"
                        aria-hidden="true"
                >
                    <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                    >
                        <path d="M3 10h18"></path>
                        <path d="M5 10v9h14v-9"></path>
                        <path d="m4 4 2-2h12l2 2 1 6H3z"></path>
                        <path d="M9 19v-5h6v5"></path>
                    </svg>
                </span>

                <div>
                    <h2>Collection available</h2>
                    <p>Choose collection where offered</p>
                </div>

            </article>

            <article class="retail-assurance__item">

                <span
                        class="retail-assurance__icon"
                        aria-hidden="true"
                >
                    <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                    >
                        <path d="M4 13a8 8 0 0 1 16 0"></path>
                        <path d="M4 13v5h3v-5z"></path>
                        <path d="M17 13v5h3v-5z"></path>
                        <path d="M17 20c-1 1-2 1-4 1"></path>
                    </svg>
                </span>

                <div>
                    <h2>Expert product advice</h2>
                    <p>Speak directly to Blaines Web Sales</p>
                </div>

            </article>

                </div>
            </div>
        </div>
    </section>

<?php
/*
 * 4. Electrical department grid.
 */
?>
    <section
            class="retail-departments-section"
            aria-labelledby="electrical-departments-heading"
    >
        <div class="container-wide">

            <div class="retail-section-heading retail-departments-heading">

                <h2 id="electrical-departments-heading">
                    Shop by department
                </h2>

                <p>
                    Browse appliances, televisions and audio by department, compare products and view current prices.
                </p>

            </div>

            <div
                    id="electrical-departments"
                    class="retail-department-grid scroll-mt-32"
            >

                <?php foreach ( $departments as $department ) : ?>

                    <article class="retail-department-card">

                        <a
                                href="<?php echo esc_url( $department['url'] ); ?>"
                                class="retail-department-card__link"
                        >

                            <div class="retail-department-card__media">

                                <?php if ( ! empty( $department['image_id'] ) ) : ?>

                                    <?php
                                    echo wp_get_attachment_image(
                                        $department['image_id'],
                                        'large',
                                        false,
                                        [
                                            'class'    => 'retail-department-card__image',
                                            'alt'      => esc_attr( $department['title'] ),
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ]
                                    );
                                    ?>

                                <?php else : ?>

                                    <div class="retail-department-card__fallback">

                                        <span aria-hidden="true">
                                            <?php echo esc_html( mb_substr( $department['title'], 0, 1 ) ); ?>
                                        </span>

                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="retail-department-card__content">

                                <div>

                                    <h3>
                                        <?php echo esc_html( $department['title'] ); ?>
                                    </h3>

                                    <p>
                                        <?php echo esc_html( $department['label'] ); ?>
                                    </p>

                                </div>

                                <span
                                        class="retail-department-card__arrow"
                                        aria-hidden="true"
                                >
                                    <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            focusable="false"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="m13 6 6 6-6 6"></path>
                                    </svg>
                                </span>

                            </div>

                        </a>

                    </article>

                <?php endforeach; ?>

            </div>

        </div>
    </section>

<?php
/*
 * 5. Featured transactional WooCommerce products.
 */
get_template_part(
    'template-parts/deals-slider',
    null,
    [
        'title' => 'Featured electrical products',
        'limit' => 12,
    ]
);
?>

<?php
/*
 * 6. Electrical promotion campaigns.
 *
 * The promotions_grid ACF field is read from the configured front page,
 * while the retail variant controls presentation on this page only.
 */
get_template_part(
    'template-parts/components/promotions-grid',
    null,
    [
        'variant'   => 'retail',
        'title'     => 'Current offers &amp; inspiration',
        'intro'     => 'Explore selected appliance campaigns, brand promotions and ideas for your home.',
        'source_id' => $homepage_id,
    ]
);
?>

<?php
/*
 * 7. Klarna information.
 *
 * Keep this conditional before staging so it can be hidden when Klarna is
 * not enabled as a payment method.
 */
?>
    <section
            class="retail-finance"
            aria-labelledby="retail-finance-heading"
    >
        <div class="container-wide">

            <div class="retail-finance__shell">

                <header class="retail-finance__header">

                    <h2 id="retail-finance-heading">
                        Flexible ways to pay
                    </h2>

                    <p>
                        Klarna payment options are shown at checkout where available, helping you choose the payment route that suits your purchase.
                    </p>

                </header>

                <div class="retail-finance__artwork">

                    <img
                            src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/KLARNA-HOMEPAGE-BANNER-2220X300.webp' ); ?>"
                            alt="Klarna available at checkout: pay in 3, pay in 30 days or pay now"
                            loading="lazy"
                            decoding="async"
                    >

                </div>

            </div>

        </div>
    </section>

<?php
/*
 * 8. Local retail service and advice.
 */
?>
    <section
            class="retail-service"
            aria-labelledby="retail-service-heading"
    >
        <div class="container-wide">

            <div class="retail-service__shell">

                <div class="retail-service__content">

                    <h2 id="retail-service-heading">
                        More than simply delivering a box
                    </h2>

                    <p>
                        Blaines combines online convenience with practical local service. Our team can help you compare products and explain the delivery, installation and removal options available for your order.
                    </p>

                    <a
                            href="tel:01760302111"
                            class="retail-button retail-button--light"
                    >
                        Call Web Sales
                    </a>

                </div>

                <div class="retail-service__features">

                    <article class="retail-service__feature">

                        <span
                                class="retail-service__icon"
                                aria-hidden="true"
                        >
                            <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.9"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                            >
                                <path d="M3 6h11v10H3z"></path>
                                <path d="M14 9h4l3 3v4h-7z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>
                        </span>

                        <div>
                            <h3>Local delivery</h3>

                            <p>
                                Delivery dates are calculated around product availability and your local postcode.
                            </p>
                        </div>

                    </article>

                    <article class="retail-service__feature">

                        <span
                                class="retail-service__icon"
                                aria-hidden="true"
                        >
                            <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.9"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                            >
                                <path d="M14.7 6.3a4 4 0 0 0-5-5L7.3 3.7l3 3 2.4-2.4"></path>
                                <path d="m10.3 6.7-8.1 8.1a2.1 2.1 0 0 0 3 3l8.1-8.1"></path>
                                <path d="m15 13 6 6"></path>
                            </svg>
                        </span>

                        <div>
                            <h3>Installation options</h3>

                            <p>
                                Select installation and connection services where they are available for the product.
                            </p>
                        </div>

                    </article>

                    <article class="retail-service__feature">

                        <span
                                class="retail-service__icon"
                                aria-hidden="true"
                        >
                            <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.9"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                            >
                                <path d="M4 13a8 8 0 0 1 16 0"></path>
                                <path d="M4 13v5h3v-5z"></path>
                                <path d="M17 13v5h3v-5z"></path>
                                <path d="M17 20c-1 1-2 1-4 1"></path>
                            </svg>
                        </span>

                        <div>
                            <h3>Product advice</h3>

                            <p>
                                Speak directly to someone who understands the products before placing your order.
                            </p>
                        </div>

                    </article>

                </div>

            </div>

        </div>
    </section>

<?php
/*
 * 9. Customer reviews.
 */
get_template_part(
    'template-parts/reviews',
    null,
    [
        'variant' => 'retail',
        'title'   => 'What our customers say',
        'intro'   => 'Recent feedback about our advice, service and delivery.',
    ]
);
?>

</div>

<?php
get_footer();