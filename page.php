<?php
/**
 * Default page template.
 */

get_header();

$hero_heading = get_field( 'contact_hero_heading' ) ?: get_the_title();
$hero_intro   = get_field( 'contact_hero_intro' );
$thumb_id     = get_post_thumbnail_id( get_the_ID() );

$hero_image = null;

if ( $thumb_id ) {
    $hero_image = [
        'ID'  => $thumb_id,
        'url' => wp_get_attachment_url( $thumb_id ),
        'alt' => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
    ];
}

$commerce_context = function_exists( 'blaines_get_commerce_page_context' )
    ? blaines_get_commerce_page_context()
    : [
        'is_commerce_page'       => false,
        'is_cart_page'           => false,
        'is_checkout_page'       => false,
        'is_order_received_page' => false,
        'is_account_page'        => false,
    ];

$is_commerce_like_page = ! empty( $commerce_context['is_commerce_page'] );
$is_cart_like_page     = ! empty( $commerce_context['is_cart_page'] );
$is_account_like_page  = ! empty( $commerce_context['is_account_page'] );

$is_transaction_flow_page = (
    $is_commerce_like_page
    && ! $is_account_like_page
);

$page_content_spacing = $is_account_like_page
    ? 'py-8 lg:py-12'
    : 'py-6';

$has_page_sidebar = (
    ! $is_commerce_like_page
    && is_active_sidebar( 'page-sidebar' )
);

/**
 * Pages using compact legal/policy typography.
 */
$legal_page_slugs = array(
    'electrical-returns-refunds-cancellations',
    'electrical-terms-of-sale',
    'delivery-installation-collection',
    'interiors-terms-and-conditions',
    'privacy-policy',
    'cookie-policy',
    'terms-and-conditions',
);

$is_legal_page = is_page( $legal_page_slugs );

/**
 * Legal pages have their own dedicated sidebar.
 * This takes priority over the standard page sidebar.
 */
$has_legal_sidebar = (
    $is_legal_page
    && ! $is_commerce_like_page
);


?>

<?php if ( $is_transaction_flow_page ) : ?>

    <?php
    get_template_part(
        'template-parts/woocommerce/commerce-page-header',
        null,
        [
            'context' => $commerce_context,
        ]
    );
    ?>

<?php elseif ( $is_account_like_page ) : ?>

    <div class="container-wide py-3 text-sm">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>

<?php else : ?>

    <?php
    get_template_part(
        'template-parts/components/page-header',
        null,
        [
            'hero'        => $hero_image,
            'title'       => $hero_heading,
            'intro'       => $hero_intro ?: '',
            'breadcrumbs' => true,
            'align'       => 'center',
        ]
    );
    ?>

<?php endif; ?>

<?php
while ( have_posts() ) :
    the_post();
    ?>

    <div class="container-wide <?php echo esc_attr( $page_content_spacing ); ?>">

        <div class="grid grid-cols-1 <?php
        echo ( $has_legal_sidebar || $has_page_sidebar )
            ? 'gap-10 lg:grid-cols-12'
            : '';
        ?>">

            <div class="<?php
            echo esc_attr(
                ( $has_legal_sidebar || $has_page_sidebar )
                    ? 'lg:col-span-8'
                    : (
                $is_commerce_like_page
                    ? 'w-full'
                    : 'mx-auto w-full max-w-6xl'
                )
            );
            ?>">

                <?php
                $content_classes = $is_commerce_like_page
                    ? 'max-w-none'
                    : 'prose-site '
                      . ( $is_legal_page ? 'prose-legal' : 'prose-news' )
                      . ' mt-8 max-w-none';
                ?>

                <?php if ( $has_legal_sidebar ) : ?>
                    <div class="mb-6 lg:hidden">
                        <details class="border border-neutral-200 bg-neutral-50">
                            <summary class="cursor-pointer px-5 py-4 font-heading text-base font-semibold text-brand-dark">
                                Legal Information
                            </summary>

                            <div class="border-t border-neutral-200 px-5 py-4">
                                <?php
                                wp_nav_menu(
                                    array(
                                        'menu'        => 'Legal Information',
                                        'container'   => false,
                                        'menu_class'  => 'blaines-legal-menu',
                                        'depth'       => 1,
                                        'fallback_cb' => false,
                                    )
                                );
                                ?>
                            </div>
                        </details>
                    </div>
                <?php endif; ?>

                <div class="<?php echo esc_attr( $content_classes ); ?>">

                <?php the_content(); ?>
                </div>

                <?php
                $cart_services_blocked_by_postcode = (
                    function_exists( 'blaines_fulfilment_services_blocked_by_checked_postcode' )
                    && blaines_fulfilment_services_blocked_by_checked_postcode()
                );
                ?>
                <?php if ( $is_cart_like_page && ! $cart_services_blocked_by_postcode ) : ?>
                    <section class="mt-2 border-t border-neutral-200 pt-8">
                        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

                            <div class="border border-neutral-200 bg-neutral-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">
                                    Local delivery
                                </p>

                                <p class="mt-2 text-sm font-light leading-relaxed text-neutral-600">
                                    Selected products are available for local delivery within the Blaines service area.
                                </p>
                            </div>

                            <div class="border border-neutral-200 bg-neutral-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">
                                    Collection available
                                </p>

                                <p class="mt-2 text-sm font-light leading-relaxed text-neutral-600">
                                    Collection options will be shown at checkout where available.
                                </p>
                            </div>

                            <div class="border border-neutral-200 bg-neutral-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">
                                    Secure payment
                                </p>

                                <p class="mt-2 text-sm font-light leading-relaxed text-neutral-600">
                                    Online payments are handled securely through the WooCommerce checkout.
                                </p>
                            </div>

                            <div class="border border-neutral-200 bg-neutral-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">
                                    Web Sales support
                                </p>

                                <p class="mt-2 text-sm font-light leading-relaxed text-neutral-600">
                                    Need help before checking out? Call Web Sales on 01760 302111.
                                </p>
                            </div>

                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <?php if ( $has_legal_sidebar ) : ?>

                <div class="hidden lg:col-span-4 lg:block">
                    <?php
                    get_template_part(
                        'template-parts/components/legal-sidebar'
                    );
                    ?>
                </div>

            <?php elseif ( $has_page_sidebar ) : ?>

                <div class="lg:col-span-4">
                    <?php get_sidebar( 'page' ); ?>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>