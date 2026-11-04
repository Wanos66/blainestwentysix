<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('text-gray-900 antialiased min-h-screen flex flex-col'); ?>>
<?php if (function_exists('wp_body_open')) {
    wp_body_open();
}

$blaines_account_url   = function_exists( 'blaines_get_customer_account_url' )
    ? blaines_get_customer_account_url()
    : wp_login_url();
$blaines_account_label = function_exists( 'blaines_get_customer_account_label' )
    ? blaines_get_customer_account_label()
    : ( is_user_logged_in() ? 'My account' : 'Account' );
$blaines_cart_count    = function_exists( 'blaines_get_cart_count' )
    ? blaines_get_cart_count()
    : 0;
?>

<header id="site-header" class="relative z-50">

    <!-- 1) TOP INFO BAR (white text on grey) -->
    <div class="bg-slate-700 text-white text-xs font-body hidden lg:block" id="top-info-bar">
        <div class="container-wide flex justify-between gap-4 py-2">
            <div class="flex justify-between gap-4">
                <?php echo blaines_icon('phone-filled', 'w-5 h-5 text-neutral-500 flex-shrink-0'); ?>
                <span><strong> Swaffham:</strong> 01760 724418</span> <span><strong> Dereham:</strong> 01362 692323</span> <span><strong> Watton:</strong> 01953 884005</span>
                <span><strong> Web Sales:</strong> 01760 302111</span>
            </div>
            <div class="flex justify-between gap-4">
                <?php echo blaines_icon('email-filled', 'w-5 h-5 text-neutral-500 flex-shrink-0'); ?>
                <span><strong> Email:</strong> sales@blaines.co.uk</span> <span><a href="<?php echo esc_url(home_url('/showrooms')); ?>"><?php echo blaines_icon('location-filled',
                            'w-5 h-5 text-neutral-500 flex-shrink-0'); ?> Our
                        Showrooms</a></span>
            </div>

        </div>
    </div>

    <!-- 2) LOGO +  -->
    <div class="bg-white">
        <div class="container-wide flex items-center justify-between gap-8 py-4">

            <!-- Main Logo -->
            <div class="flex-shrink-0 header-main-logo">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl font-semibold">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Header commerce tools – desktop only -->
            <div class="hidden lg:flex flex-1 items-center justify-end gap-5">

                <form
                        role="search"
                        method="get"
                        action="<?php echo esc_url( home_url( '/' ) ); ?>"
                        class="flex min-w-[420px] max-w-[560px] flex-1 items-stretch"
                >
                    <label class="sr-only" for="header-product-search">
                        Search products
                    </label>

                    <input
                            id="header-product-search"
                            type="search"
                            name="s"
                            value="<?php echo esc_attr( get_search_query() ); ?>"
                            placeholder="Search appliances, TVs, audio & more"
                            class="min-h-11 flex-1 border border-neutral-300 border-r-0 bg-white px-4 text-sm font-light text-neutral-900 placeholder:text-neutral-400 focus:border-blue-700 focus:outline-none"
                    >

                    <input type="hidden" name="post_type" value="product">

                    <button
                            type="submit"
                            class="inline-flex min-h-11 items-center justify-center bg-neutral-950 px-4 text-white transition hover:bg-neutral-800"
                            aria-label="Search"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10.75 18.5a7.75 7.75 0 1 1 0-15.5 7.75 7.75 0 0 1 0 15.5Z" stroke="currentColor" stroke-width="2"/>
                            <path d="m16.5 16.5 4.5 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>

                <div class="hidden xl:block border-l border-neutral-200 pl-5 text-right">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-neutral-500">
                        Web Sales
                    </p>

                    <a
                            href="tel:01760302111"
                            class="mt-1 block text-sm font-semibold text-neutral-950 transition hover:text-blue-700"
                    >
                        01760 302111
                    </a>
                </div>

                <a
                        href="<?php echo esc_url( $blaines_account_url ); ?>"
                        class="group inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-neutral-950 transition hover:text-blue-700"
                        aria-label="<?php echo esc_attr( $blaines_account_label ); ?>"
                >
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>

                    <span class="hidden xl:inline">
                        <?php echo esc_html( $blaines_account_label ); ?>
                    </span>
                </a>

                <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
                    <a
                            href="<?php echo esc_url( wc_get_cart_url() ); ?>"
                            class="group relative inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-neutral-950 transition hover:text-blue-700"
                            aria-label="<?php echo esc_attr( sprintf( 'View basket, %d items', $blaines_cart_count ) ); ?>"
                    >
                        <span class="relative inline-flex">
                            <img
                                    src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/cart.svg' ); ?>"
                                    alt=""
                                    class="h-8 w-8 object-contain"
                                    aria-hidden="true"
                            >

                            <span class="js-header-cart-count absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-700 px-1 text-[11px] font-semibold leading-none text-white">
                                <?php echo esc_html( $blaines_cart_count ); ?>
                            </span>
                        </span>
                    </a>
                <?php endif; ?>

            </div>

        </div>
    </div>



    <!-- 3) NAVIGATION ROW (desktop nav + mobile burger) -->
    <div class="bg-white border-y border-gray-200">
        <div class="container-wide flex items-center justify-between">

            <!-- Primary nav (desktop) -->
            <nav id="nav-row" class="relative hidden flex-1 lg:flex lg:justify-start" aria-label="<?php esc_attr_e('Primary menu', 'blainestwentysix'); ?>">

                <?php
                wp_nav_menu(blt_primary_menu_args());
                ?>
            </nav>

            <!-- Mobile commerce tools and burger -->
            <div class="flex w-full items-center justify-between lg:hidden">
                <button
                        id="menu-toggle"
                        class="p-2 text-3xl"
                        aria-expanded="false"
                        aria-controls="mobile-menu"
                        aria-label="Open menu"
                >
                    ☰
                </button>

                <div class="flex items-center gap-4">
                    <a
                            href="<?php echo esc_url( $blaines_account_url ); ?>"
                            class="inline-flex min-h-11 items-center justify-center text-neutral-950 transition hover:text-blue-700"
                            aria-label="<?php echo esc_attr( $blaines_account_label ); ?>"
                    >
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </a>

                    <?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
                        <a
                                href="<?php echo esc_url( wc_get_cart_url() ); ?>"
                                class="relative inline-flex min-h-11 items-center justify-center text-neutral-950 transition hover:text-blue-700"
                                aria-label="<?php echo esc_attr( sprintf( 'View basket, %d items', $blaines_cart_count ) ); ?>"
                        >
                            <span class="relative inline-flex">
                                <img
                                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/cart.svg' ); ?>"
                                        alt=""
                                        class="h-7 w-7 object-contain"
                                        aria-hidden="true"
                                >

                                <span class="js-header-cart-count absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-700 px-1 text-[11px] font-semibold leading-none text-white">
                                    <?php echo esc_html( $blaines_cart_count ); ?>
                                </span>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 4) BLACK RIBBON BLURB -->
    <div class="bg-black text-white text-xs hidden md:block" id="black-blurb-bar">
        <div class="container-wide py-1 flex justify-around">
            <div class="flex">
                <div class="pt-1 mr-2">
                    <img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/award-fill.svg') ?>" alt="" class="h-6">
                </div>
                <div>
                    <h5>Over 40 Years in Business</h5>
                    <p>Blaines Est. 1982</p>
                </div>
            </div>

            <div class="flex">
                <div class="pt-1 mr-2">
                    <img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/Euronics_star_and_e.svg') ?>" alt="" class="h-6">
                </div>
                <div>
                    <h5>Member of Euronics</h5>
                    <p>Serving Local Communities</p>
                </div>
            </div>
            <div class="flex">
                <div class="mr-2">
                    <img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/fast-delivery-truck-2.svg') ?>" alt="" class="h-8">
                </div>
                <div>
                    <h5>FREE Local Delivery</h5>
                    <p>On qualifying orders of £400 or more</p>
                </div>
            </div>
            <div class="flex">
                <div class="pt-1 mr-2">
                    <a href="https://www.facebook.com/Blaines.co.uk/" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/facebook.svg') ?>"
                                alt=""
                                class="h-6"></a>
                </div>
                <div>
                    <h5>Follow Us</h5>
                    <p>On Facebook</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 5) MOBILE MENU -->
    <div id="mobile-menu" class="fixed inset-0 z-40 hidden bg-white flex flex-col px-6 py-8 overflow-y-auto" aria-hidden="true">
        <?php
        wp_nav_menu(blt_mobile_menu_args());
        ?>

        <button id="menu-close" class="mt-10 text-3xl p-2 block mx-auto">✕</button>
    </div>


</header>

<main id="site-content" class="flex-1 font-body">
