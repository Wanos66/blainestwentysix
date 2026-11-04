<?php
/**
 * Theme include loader.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Core
require_once __DIR__ . '/utilities.php';
require_once __DIR__ . '/setup.php';
require_once __DIR__ . '/enqueue.php';
require_once __DIR__ . '/theme-hooks.php';
require_once __DIR__ . '/showrooms.php';

// Options / Admin
require_once __DIR__ . '/acf-options.php';

// WPForms
require_once __DIR__ . '/wpforms-showroom-routing.php';

// Navigation
require_once __DIR__ . '/navigation.php';

// Template routing (if used)
require_once __DIR__ . '/template-routing.php';

// REST (Filtering engine lives in MU-plugin. Theme should not load REST endpoints.)
if (defined('BLAINES_FILTERS_ENABLED') && BLAINES_FILTERS_ENABLED === false) {
    // Filters intentionally disabled — do nothing.
} else {
    // Filters enabled (or not defined). REST routes are provided by MU-plugin.
    // If you ever want a theme fallback, put it here.
    // require_once __DIR__ . '/rest/register-rest-endpoints.php';
}


require_once get_template_directory() . '/inc/schema/schema.php';

require_once get_template_directory() . '/inc/woocommerce/transactional-departments.php';
require_once get_template_directory() . '/inc/woocommerce/transactional-filters.php';
require_once get_template_directory() . '/inc/woocommerce/product-behaviour.php';
require_once get_template_directory() . '/inc/woocommerce/customer-accounts.php';
require_once get_template_directory() . '/inc/woocommerce/commerce-notices.php';
require_once get_template_directory() . '/inc/woocommerce/empty-cart-navigation.php';
require_once get_template_directory() . '/inc/woocommerce/product-availability.php';

require_once get_template_directory() . '/inc/acf/transactional-archive-promotions.php';
require_once get_template_directory() . '/inc/acf/transactional-energy-information.php';
require get_template_directory() . '/inc/acf/transactional-warranty-information.php';
require_once get_template_directory() . '/inc/acf/electrical-department-images.php';

require_once get_template_directory() . '/inc/woocommerce/transactional-promotions.php';
require_once get_template_directory() . '/inc/woocommerce/transactional-similar-products.php';
require_once get_template_directory() . '/inc/woocommerce/product-postcode-checker.php';
require_once get_template_directory() . '/inc/woocommerce/fulfilment-services.php';
require_once get_template_directory() . '/inc/woocommerce/fulfilment-rules.php';
require_once get_template_directory() . '/inc/woocommerce/cart-fulfilment-services.php';
require_once get_template_directory() . '/inc/woocommerce/shipping-collection-rules.php';
require_once get_template_directory() . '/inc/woocommerce/checkout-customisations.php';
require get_template_directory() . '/inc/woocommerce/checkout-collection.php';
require_once get_template_directory() . '/inc/woocommerce/preferred-delivery-date.php';
require_once get_template_directory() . '/inc/woocommerce/commerce-page-context.php';
require_once get_template_directory() . '/inc/woocommerce/order-display-customisations.php';
require_once get_template_directory() . '/inc/woocommerce/order-statuses.php';
require_once get_template_directory() . '/inc/woocommerce/campaign-catalogue.php';


/**
 * Register the Blaines Order Confirmed customer email.
 */
add_filter( 'woocommerce_email_classes', function ( $emails ) {

    require_once __DIR__
                 . '/woocommerce/emails/class-wc-email-blaines-order-confirmed.php';

    $emails['WC_Email_Blaines_Order_Confirmed'] =
        new WC_Email_Blaines_Order_Confirmed();

    return $emails;
} );

/**
 * Register the Blaines Ready for Collection email.
 */
add_filter( 'woocommerce_email_classes', function ( $emails ) {

    require_once __DIR__
                 . '/woocommerce/emails/class-wc-email-blaines-ready-for-collection.php';

    $emails['WC_Email_Blaines_Ready_For_Collection'] =
        new WC_Email_Blaines_Ready_For_Collection();

    return $emails;
} );

/**
 * Ready for Collection order action.
 */
require_once __DIR__ . '/woocommerce/ready-for-collection.php';

/**
 * Cromwell EPOS order reference.
 */
require_once __DIR__ . '/woocommerce/order-epos-reference.php';


/**
 * Trustpilot AFS integration.
 */
require_once __DIR__ . '/woocommerce/trustpilot-invitations.php';

require_once get_template_directory() . '/inc/privacy/tawk-consent.php';


