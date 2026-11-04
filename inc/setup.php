<?php
/**
 * Theme Setup File
 *
 * Registers theme supports, menus, editor styles,
 * and global configuration.
 *
 * @package BlainesTwentySix
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', function() {

    /**
     * -------------------------------------------------------
     * Core Theme Supports
     * -------------------------------------------------------
     */

    // Dynamically set <title> tag
    add_theme_support( 'title-tag' );

    // Featured images
    add_theme_support( 'post-thumbnails' );

    // Output valid HTML5 markup
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style'
    ] );

    // RSS feed links
    add_theme_support( 'automatic-feed-links' );

    // Selective refresh in Customizer
    add_theme_support( 'customize-selective-refresh-widgets' );

    /**
     * -------------------------------------------------------
     * Custom Logo
     * -------------------------------------------------------
     */

    add_theme_support( 'custom-logo', [
        'height'               => 120,
        'width'                => 400,
        'flex-height'          => true,
        'flex-width'           => true,
        'header-text'          => [ 'site-title', 'site-description' ],
    ] );


    /**
     * -------------------------------------------------------
     * Menus
     * -------------------------------------------------------
     */

    register_nav_menus([
        'primary'         => __( 'Primary Menu', 'blainestwentysix' ),
        'footer_about'    => __( 'Footer – About', 'blainestwentysix' ),
        'footer_more_info'=> __( 'Footer – More Info', 'blainestwentysix' ),
        'footer_services' => __( 'Footer – Services', 'blainestwentysix' ),
        'footer_info'     => __( 'Footer – Info / Legal', 'blainestwentysix' ),
    ]);


    /**
     * -------------------------------------------------------
     * Editor Styles
     * -------------------------------------------------------
     * You selected:
     *   Q3 = A → enable Tailwind-based editor stylesheet
     */

    add_theme_support( 'editor-styles' );

    // Loads /assets/css/editor.css (you will create this file)
    add_editor_style( 'assets/css/editor.css' );

    add_action('customize_register', function ($wp_customize) {

        $wp_customize->add_section('blaines_footer', [
            'title'    => __('Footer', 'blainestwentysix'),
            'priority' => 160,
        ]);

        $wp_customize->add_setting('footer_logo', [
            'sanitize_callback' => 'absint',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Media_Control(
                $wp_customize,
                'footer_logo',
                [
                    'label'    => __('Footer Logo', 'blainestwentysix'),
                    'section'  => 'blaines_footer',
                    'mime_type'=> 'image',
                ]
            )
        );
    });


    add_theme_support( 'woocommerce' );


});


