<?php
/**
 * Blaines Twenty-Six Theme Functions
 *
 * @package BlainesTwentySix
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_template_directory() . '/inc/load.php';

add_filter( 'aioseo_schema_disable', '__return_true' );

