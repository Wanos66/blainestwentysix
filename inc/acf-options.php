<?php
/**
 * ACF Options Pages
 *
 * Registers global Theme Settings pages using Advanced Custom Fields.
 * This file is only loaded if ACF is present, and is intentionally
 * separated from setup.php to keep plugin integration modular.
 *
 * @package BlainesTwentySix
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'acf/init', function () {

    // Bail early if ACF is not active
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    // Main Theme Settings Page
    acf_add_options_page([
        'page_title' => __( 'Theme Settings', 'blainestwentysix' ),
        'menu_title' => __( 'Theme Settings', 'blainestwentysix' ),
        'menu_slug'  => 'theme-settings',
        'capability' => 'manage_options',
        'redirect'   => false,
        'position'   => '61', // Just under Appearance (optional, but nice)
        'icon_url'   => 'dashicons-admin-generic', // Optional
    ]);

});

/**
 * Limit Relationship field 'other_showrooms' to Pages using Showroom template.
 */
add_filter('acf/fields/relationship/query/name=other_showrooms', function ($args, $field, $post_id) {

    // Only show Pages
    $args['post_type'] = ['page'];

    // Restrict to pages with this template file
    // IMPORTANT: must match the template filename saved in _wp_page_template
    $args['meta_query'] = [
        [
            'key'     => '_wp_page_template',
            'value'   => 'templates/page-showroom.php',
            'compare' => '=',
        ],
    ];

    // Optional: order them nicely
    $args['orderby'] = 'menu_order';
    $args['order']   = 'ASC';

    return $args;
}, 10, 3);
