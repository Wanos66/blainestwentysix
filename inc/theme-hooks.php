<?php
/**
 * Custom theme functions
 *
 * @package BlainesTwentySix
 */

/**
 * Add custom body classes
 */
function blainestwentysix_body_classes($classes)
{
    // Add page slug to body class
    if (is_singular()) {
        global $post;
        $classes[] = 'page-'.$post->post_name;
    }

    // Add has-sidebar class if sidebar is active
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    }

    return $classes;
}

add_filter('body_class', 'blainestwentysix_body_classes');

/**
 * Custom excerpt length
 */
function blainestwentysix_excerpt_length($length)
{
    return 25;
}

add_filter('excerpt_length', 'blainestwentysix_excerpt_length');

/**
 * Custom excerpt more
 */
function blainestwentysix_excerpt_more($more)
{
    return '...';
}

add_filter('excerpt_more', 'blainestwentysix_excerpt_more');

/**
 * Add schema.org structured data
 */
function blainestwentysix_schema_markup()
{
    if (is_single() && 'post' === get_post_type()) {
        $schema = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'BlogPosting',
            'headline'      => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified'  => get_the_modified_date('c'),
            'author'        => array(
                '@type' => 'Person',
                'name'  => get_the_author()
            ),
            'publisher'     => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => get_site_icon_url()
                )
            )
        );

        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url(null, 'full');
        }

        echo '<script type="application/ld+json">'.wp_json_encode($schema, JSON_UNESCAPED_SLASHES).'</script>';
    }
}

add_action('wp_head', 'blainestwentysix_schema_markup');

/**
 * Register widget areas
 */
function blainestwentysix_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Sidebar', 'blainestwentysix'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'blainestwentysix'),
        'before_widget' => '<section id="%1$s" class="widget mb-8 %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-2xl font-heading font-bold mb-4">',
        'after_title'   => '</h2>',
    ));

    register_sidebar([
        'name'          => __('Page Sidebar', 'blainestwentysix'),
        'id'            => 'page-sidebar',
        'description'   => __('Widgets shown in the right column on standard pages.', 'blainestwentysix'),
        'before_widget' => '<section id="%1$s" class="widget %2$s border border-neutral-200 bg-white p-6 shadow-sm">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="mb-4 text-lg font-semibold text-neutral-900">',
        'after_title'   => '</h2>',
    ]);

    register_sidebar(array(
        'name'          => __('Footer', 'blainestwentysix'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'blainestwentysix'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-semibold mb-4">',
        'after_title'   => '</h3>',
    ));
}

add_action('widgets_init', 'blainestwentysix_widgets_init');

/**
 * Add security headers
 */
function blainestwentysix_security_headers()
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

add_action('send_headers', 'blainestwentysix_security_headers');


add_filter( 'wp_nav_menu_objects', function( $items ) {
    $parents = [];

    foreach ( $items as $item ) {
        if ( $item->menu_item_parent != 0 ) {
            $parents[$item->menu_item_parent][] = $item;
        }
    }

    foreach ( $items as &$item ) {
        $item->children = $parents[$item->ID] ?? [];
    }

    return $items;
});

add_action( 'init', function() {
    load_theme_textdomain(
        'blainestwentysix',
        get_template_directory() . '/languages'
    );
});

add_filter( 'get_custom_logo', function ( $html ) {

    // If there's no <img, bail early.
    if ( strpos( $html, '<img' ) === false ) {
        return $html;
    }

    // If an id is already present, don’t force another.
    if ( strpos( $html, 'id=' ) !== false ) {
        return $html;
    }

    // Inject id="main-logo" into the first <img ...> only.
    return preg_replace( '/<img\b(?![^>]*\bid=)/', '<img id="main-logo"', $html, 1 );
} );

add_action('pre_get_posts', 'blaines_adjust_archive_ordering');

function blaines_adjust_archive_ordering($query) {
    if (is_admin() || ! $query->is_main_query()) {
        return;
    }

    $room_post_types = [
        'kitchen',
        'bathroom',
        'bedroom',
    ];

    $room_taxonomies = [
        'kitchen_style', 'kitchen_finish', 'kitchen_brand',
        'bathroom_style', 'bathroom_finish', 'bathroom_brand',
        'bedroom_style', 'bedroom_finish', 'bedroom_brand',
    ];

    if ($query->is_post_type_archive('project') || $query->is_tax('project_type')) {
        $query->set('meta_key', 'completion_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'DESC');

        $meta_query = (array) $query->get('meta_query');
        $meta_query[] = [
            'key'     => 'completion_date',
            'compare' => 'EXISTS',
        ];

        $query->set('meta_query', $meta_query);
        return;
    }

    if ($query->is_post_type_archive($room_post_types) || $query->is_tax($room_taxonomies)) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }
}

function blaines_get_showrooms_parent_id() {
    $showrooms_page = get_page_by_path( 'showrooms' );
    return $showrooms_page ? (int) $showrooms_page->ID : 0;
}

add_filter( 'acf/fields/post_object/query/name=showroom_page', 'blaines_limit_showroom_page_choices', 10, 3 );

function blaines_limit_showroom_page_choices( $args, $field, $post_id ) {
    $args['post_type']      = [ 'page' ];
    $args['post_status']    = [ 'publish' ];
    $args['posts_per_page'] = -1;
    $args['post_parent']    = 552; // Showrooms parent page ID.
    $args['orderby']        = 'menu_order';
    $args['order']          = 'ASC';

    return $args;
}








