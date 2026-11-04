<?php

// Legacy: custom-logo support moved to inc/setup.php (canonical).

function blainestwentysix_custom_logo_setup() {
    $defaults = array(
        'height'               => 100,
        'width'                => 400,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => array( 'site-title', 'site-description' ),
        'unlink-homepage-logo' => true,
    );
    add_theme_support( 'custom-logo', $defaults );
}
//add_action( 'after_setup_theme', 'blainestwentysix_custom_logo_setup' );
