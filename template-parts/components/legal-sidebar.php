<?php
/**
 * Legal Information sidebar.
 *
 * Navigation is managed through
 * Appearance > Menus > Legal Information.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

$legal_menu = wp_get_nav_menu_object( 'Legal Information' );

if ( ! $legal_menu ) {
    return;
}
?>

<aside
    class="border border-neutral-200 bg-neutral-50 p-6"
    aria-labelledby="legal-sidebar-heading"
>
    <h2
        id="legal-sidebar-heading"
        class="mb-5 font-heading text-lg font-semibold text-brand-dark"
    >
        Legal Information
    </h2>

    <nav aria-label="Legal Information">
        <?php
        wp_nav_menu(
            array(
                'menu'            => $legal_menu->term_id,
                'container'       => false,
                'menu_class'      => 'blaines-legal-menu',
                'depth'           => 1,
                'fallback_cb'     => false,
            )
        );
        ?>
    </nav>
</aside>

