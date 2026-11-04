<?php
/**
 * Simple theme breadcrumbs (plugin independent)
 */

if ( is_front_page() ) {
    return;
}

$crumbs = [];
$crumbs[] = [ 'label' => 'Home', 'url' => home_url( '/' ) ];

// Blog index (News)
if ( is_home() ) {
    $blog_id = (int) get_option( 'page_for_posts' );
    $crumbs[] = [
        'label' => $blog_id ? get_the_title( $blog_id ) : 'News',
        'url'   => $blog_id ? get_permalink( $blog_id ) : home_url( '/' ),
    ];
}

// Post type archive
elseif ( is_post_type_archive() ) {
    $pt = get_query_var( 'post_type' );
    $pt = is_array( $pt ) ? reset( $pt ) : $pt;
    $obj = $pt ? get_post_type_object( $pt ) : null;

    $crumbs[] = [
        'label' => $obj ? $obj->labels->name : post_type_archive_title( '', false ),
        'url'   => $pt ? get_post_type_archive_link( $pt ) : home_url( '/' ),
    ];
}

// Taxonomy archive
elseif ( is_tax() || is_category() || is_tag() ) {
    $term = get_queried_object();

    if ( $term && ! is_wp_error( $term ) ) {

        // Standard blog taxonomies should point back to the posts page / News index.
        if ( is_category() || is_tag() ) {
            $blog_id = (int) get_option( 'page_for_posts' );

            $crumbs[] = [
                'label' => $blog_id ? get_the_title( $blog_id ) : 'News',
                'url'   => $blog_id ? get_permalink( $blog_id ) : home_url( '/news/' ),
            ];
        } else {
            // If taxonomy belongs to a CPT with an archive, include it.
            $tax = get_taxonomy( $term->taxonomy );

            if ( $tax && ! empty( $tax->object_type ) ) {
                $post_type = $tax->object_type[0];
                $pt_obj    = get_post_type_object( $post_type );

                if ( $pt_obj && ! empty( $pt_obj->has_archive ) ) {
                    $crumbs[] = [
                        'label' => $pt_obj->labels->name,
                        'url'   => get_post_type_archive_link( $post_type ),
                    ];
                }
            }
        }

        $crumbs[] = [
            'label' => single_term_title( '', false ),
            'url'   => get_term_link( $term ),
        ];
    }
}

// Singular
elseif ( is_singular() ) {
    $post_type = get_post_type();
    $pt_obj    = $post_type ? get_post_type_object( $post_type ) : null;

// WooCommerce products should point back to the correct curated product route,
// not the default WooCommerce product archive.
    if ( is_singular( 'product' ) ) {
        $product_id = get_the_ID();
        $product    = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;

        if (
            $product
            && function_exists( 'blaines_is_transactional_product' )
            && blaines_is_transactional_product( $product )
        ) {
            $electrical_page = get_page_by_path( 'electrical' );

            $crumbs[] = [
                'label' => $electrical_page ? get_the_title( $electrical_page->ID ) : 'Electrical',
                'url'   => $electrical_page ? get_permalink( $electrical_page->ID ) : home_url( '/electrical/' ),
            ];

            $terms          = get_the_terms( $product_id, 'product_cat' );
            $category_trail = [];

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                usort(
                    $terms,
                    static function ( $a, $b ) {
                        return count( get_ancestors( $b->term_id, 'product_cat' ) ) <=> count( get_ancestors( $a->term_id, 'product_cat' ) );
                    }
                );

                $deepest_term = $terms[0] ?? null;

                if ( $deepest_term instanceof WP_Term ) {
                    $ancestors = array_reverse( get_ancestors( $deepest_term->term_id, 'product_cat' ) );

                    foreach ( $ancestors as $ancestor_id ) {
                        $ancestor = get_term( $ancestor_id, 'product_cat' );

                        if (
                            $ancestor instanceof WP_Term
                            && ! is_wp_error( $ancestor )
                            && 'electrical' !== $ancestor->slug
                        ) {
                            $category_trail[] = $ancestor;
                        }
                    }

                    if ( 'electrical' !== $deepest_term->slug ) {
                        $category_trail[] = $deepest_term;
                    }
                }
            }

            $department_term = $category_trail[0] ?? null;

            foreach ( $category_trail as $index => $term ) {
                if ( ! $term instanceof WP_Term ) {
                    continue;
                }

                if ( 0 === $index ) {
                    $term_url = home_url( '/electrical/' . $term->slug . '/' );
                } elseif ( $department_term instanceof WP_Term ) {
                    $term_url = add_query_arg(
                        'type',
                        $term->slug,
                        home_url( '/electrical/' . $department_term->slug . '/' )
                    );
                } else {
                    $term_url = '';
                }

                $crumbs[] = [
                    'label' => $term->name,
                    'url'   => $term_url,
                ];
            }
        } elseif ( has_term( 'kitchen-clearance', 'product_cat', $product_id ) ) {
            $kitchen_clearance_page = get_page_by_path( 'kitchen-clearance' );

            $crumbs[] = [
                'label' => $kitchen_clearance_page ? get_the_title( $kitchen_clearance_page->ID ) : 'Kitchen Clearance',
                'url'   => $kitchen_clearance_page ? get_permalink( $kitchen_clearance_page->ID ) : home_url( '/kitchen-clearance/' ),
            ];
        } elseif ( has_term( 'bathroom-clearance', 'product_cat', $product_id ) ) {
            $bathroom_clearance_page = get_page_by_path( 'bathroom-clearance' );

            $crumbs[] = [
                'label' => $bathroom_clearance_page ? get_the_title( $bathroom_clearance_page->ID ) : 'Bathroom Clearance',
                'url'   => $bathroom_clearance_page ? get_permalink( $bathroom_clearance_page->ID ) : home_url( '/bathroom-clearance/' ),
            ];
        } else {
            $clearance_page = get_page_by_path( 'clearance' );

            $crumbs[] = [
                'label' => $clearance_page ? get_the_title( $clearance_page->ID ) : 'Clearance',
                'url'   => $clearance_page ? get_permalink( $clearance_page->ID ) : home_url( '/clearance/' ),
            ];
        }
    } elseif ( $pt_obj && ! empty( $pt_obj->has_archive ) ) {
        $crumbs[] = [
            'label' => $pt_obj->labels->name,
            'url'   => get_post_type_archive_link( $post_type ),
        ];
    } elseif ( is_singular( 'post' ) ) {
        $blog_id = (int) get_option( 'page_for_posts' );
        if ( $blog_id ) {
            $crumbs[] = [
                'label' => get_the_title( $blog_id ),
                'url'   => get_permalink( $blog_id ),
            ];
        }
    }

    // Add hierarchical page ancestors.
    if ( is_page() ) {
        $id = get_queried_object_id();
        if ( $id ) {
            $ancestors = array_reverse( get_post_ancestors( $id ) );
            foreach ( $ancestors as $ancestor_id ) {
                $crumbs[] = [
                    'label' => get_the_title( $ancestor_id ),
                    'url'   => get_permalink( $ancestor_id ),
                ];
            }
        }
    }

    // Current.
// On controlled Electrical department pages, support product type query crumbs.
// Example: /electrical/laundry/?type=laundry-tumble-dryers
    if ( is_page() && isset( $_GET['type'] ) ) {
        $type_slug = sanitize_title( wp_unslash( $_GET['type'] ) );
        $type_term = get_term_by( 'slug', $type_slug, 'product_cat' );

        $page_id   = get_queried_object_id();
        $page_slug = $page_id ? get_post_field( 'post_name', $page_id ) : '';
        $dept_term = $page_slug ? get_term_by( 'slug', $page_slug, 'product_cat' ) : null;

        $is_valid_type_for_page = false;

        if (
            $type_term instanceof WP_Term
            && ! is_wp_error( $type_term )
            && $dept_term instanceof WP_Term
            && ! is_wp_error( $dept_term )
        ) {
            $type_ancestors = get_ancestors( $type_term->term_id, 'product_cat' );

            $is_valid_type_for_page = in_array(
                (int) $dept_term->term_id,
                array_map( 'intval', $type_ancestors ),
                true
            );
        }

        if ( $is_valid_type_for_page ) {
            $type_label = $type_term->name;

            // If terms are named like "Laundry Tumble Dryers", show "Tumble Dryers".
            $department_prefix = get_the_title();

            if ( 0 === stripos( $type_label, $department_prefix . ' ' ) ) {
                $type_label = trim( substr( $type_label, strlen( $department_prefix ) ) );
            }

            $crumbs[] = [
                'label' => get_the_title(),
                'url'   => get_permalink(),
            ];

            $crumbs[] = [
                'label' => $type_label,
                'url'   => '',
            ];
        } else {
            $crumbs[] = [
                'label' => get_the_title(),
                'url'   => get_permalink(),
            ];
        }
    } else {
        $crumbs[] = [
            'label' => get_the_title(),
            'url'   => get_permalink(),
        ];
    }
}


if ( count( $crumbs ) < 2 ) {
    return;
}
?>

<div class="container mx-auto mb-6">
    <nav class="text-sm text-gray-600" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-2">
            <?php foreach ( $crumbs as $i => $c ) : ?>
                <?php $is_last = $i === array_key_last( $crumbs ); ?>
                <li class="flex items-center gap-2">
                    <?php if ( $i > 0 ) : ?>
                        <span aria-hidden="true">›</span>
                    <?php endif; ?>

                    <?php if ( ! $is_last && ! empty( $c['url'] ) ) : ?>
                        <a class="font-body font-light text-gray-600 hover:text-gray-900" href="<?php echo esc_url( $c['url'] ); ?>">
                            <?php echo esc_html( $c['label'] ); ?>
                        </a>
                    <?php else : ?>
                        <span aria-current="page" class="font-body font-semibold text-gray-900">
                <?php echo esc_html( $c['label'] ); ?>
            </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
</div>

