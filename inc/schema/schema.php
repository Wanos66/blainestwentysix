<?php
/**
 * Schema bootstrapper.
 * Outputs JSON-LD blocks in wp_head.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/organization.php';
require_once __DIR__ . '/website.php';
require_once __DIR__ . '/breadcrumb.php';
require_once __DIR__ . '/article.php';
require_once __DIR__ . '/local-business.php';

/**
 * Echo a JSON-LD script tag safely.
 */
function blaines_schema_print_jsonld( array $data ): void {
    if ( empty( $data ) ) return;

    echo "\n" . '<script type="application/ld+json">' .
         wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) .
         "</script>\n";
}

/**
 * Master schema hook. Keep this single-source-of-truth.
 */
add_action( 'wp_head', function () {

    // Don’t output schema in admin screens.
    if ( is_admin() ) return;

    // Sitewide (always).
    if ( function_exists( 'blaines_schema_organization' ) ) {
        $org = blaines_schema_organization();
        if ( $org ) blaines_schema_print_jsonld( $org );
    }

    if ( function_exists( 'blaines_schema_website' ) ) {
        $site = blaines_schema_website();
        if ( $site ) blaines_schema_print_jsonld( $site );
    }

// Conditional.
    if ( function_exists( 'blaines_schema_breadcrumbs' ) ) {
        $crumbs = blaines_schema_breadcrumbs();
        if ( $crumbs ) blaines_schema_print_jsonld( $crumbs );
    }

    if ( function_exists( 'blaines_schema_article' ) ) {
        $article = blaines_schema_article();
        if ( $article ) blaines_schema_print_jsonld( $article );
    }

    if ( function_exists( 'blaines_schema_local_business' ) ) {
        $local_business = blaines_schema_local_business();
        if ( $local_business ) blaines_schema_print_jsonld( $local_business );
    }

}, 5 );

