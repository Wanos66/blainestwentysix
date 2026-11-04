<?php
/**
 * Enqueues front-end and editor styles/scripts for the theme.
 *
 * @package BlainesTwentySix
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Safe file versioning (prevents filemtime warnings on fresh installs / missing build artifacts).
 *
 * @param string $relative_path Path relative to theme root, e.g. '/assets/css/style.css'.
 * @return int|null
 */
function blainestwentysix_file_version( $relative_path ) {
    $path = get_theme_file_path( $relative_path );
    return file_exists( $path ) ? filemtime( $path ) : null;
}

/**
 * Enqueue front-end assets.
 */
function blainestwentysix_enqueue_assets() {

    // --- CSS (Tailwind output) ---
    wp_enqueue_style(
        'blainestwentysix-style',
        get_theme_file_uri( '/assets/css/style.css' ),
        [],
        blainestwentysix_file_version( '/assets/css/style.css' )
    );

    // --- JS (wp-scripts bundle) ---
    $index_js        = get_theme_file_path( '/build/index.js' );
    $asset_file_path = get_theme_file_path( '/build/index.asset.php' );

    // Prefer wp-scripts generated deps/version.
    if ( file_exists( $asset_file_path ) ) {
        $asset = require $asset_file_path;
    } else {
        // Fallback if build artifacts aren't present yet.
        $asset = [
            'dependencies' => [],
            'version'      => file_exists( $index_js ) ? filemtime( $index_js ) : null,
        ];
    }

    wp_enqueue_script(
        'blainestwentysix-scripts',
        get_theme_file_uri( '/build/index.js' ),
        isset( $asset['dependencies'] ) ? $asset['dependencies'] : [],
        isset( $asset['version'] ) ? $asset['version'] : null,
        true
    );

    // Option A: Use WP "strategy" data to defer (WP 6.3+).
    wp_script_add_data( 'blainestwentysix-scripts', 'strategy', 'defer' );

    // --- Localized data for JS ---
    $filters_enabled = function_exists( 'blainestwentysix_filters_enabled' )
        ? (bool) blainestwentysix_filters_enabled()
        : true;

    wp_localize_script(
        'blainestwentysix-scripts',
        'blainestwentysix',
        [
            'filtersEnabled' => $filters_enabled,
            'restBase'       => esc_url_raw( rest_url( 'blt/v1' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
        ]
    );

    // --- Third-party: Trustpilot (enqueue globally, or conditionally if desired) ---
    wp_enqueue_script(
        'trustpilot',
        'https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js',
        [],
        null,
        true
    );

    // Defer Trustpilot too (WP 6.3+).
    wp_script_add_data( 'trustpilot', 'strategy', 'defer' );

    // --- Fancybox (kept commented as per your current setup) ---
    // wp_enqueue_style(
    // 	'fancybox',
    // 	'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css',
    // 	[],
    // 	null
    // );
    //
    // wp_enqueue_script(
    // 	'fancybox',
    // 	'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js',
    // 	[],
    // 	null,
    // 	true
    // );
    // wp_script_add_data( 'fancybox', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'blainestwentysix_enqueue_assets' );
