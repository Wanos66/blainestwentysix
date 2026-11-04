<?php
/**
 * Navigation bootstrapping.
 * Loads walkers and registers menus / helpers related to site navigation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Walkers (active)
require_once get_template_directory() . '/inc/class-tailwind-nav-walker.php';
require_once get_template_directory() . '/inc/class-mobile-nav-walker.php';

/**
 * Args for the primary (desktop) menu.
 */
function blt_primary_menu_args(): array {
    return [
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class' => 'flex w-full items-center justify-between text-sm font-body',
        'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
        'walker'         => new Tailwind_Nav_Walker(),
        'fallback_cb'    => false,
    ];
}

/**
 * Args for the mobile menu.
 */
function blt_mobile_menu_args(): array {
    return [
        'theme_location' => 'mobile',
        'container'      => false,
        'menu_class'     => 'mobile-nav space-y-4 text-lg font-heading',
        'walker'         => new Mobile_Nav_Walker(),
        'fallback_cb'    => false,
    ];
}


/**
 * Register theme menus.
 */
function blt_register_menus(): void {
    register_nav_menus(
        [
            'primary' => __( 'Primary Menu', 'blainestwentysix' ),
            'mobile'  => __( 'Mobile Menu', 'blainestwentysix' ),
        ]
    );
}
add_action( 'after_setup_theme', 'blt_register_menus' );

