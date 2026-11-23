<?php
/**
 * Unified site-wide search results for Blaines.
 *
 * Electrical products retain the existing transactional row. Interiors,
 * projects, pages and articles use a separate editorial card presentation.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

get_header();

$search_term = trim( (string) get_search_query( false ) );
$product_only = isset( $_GET['post_type'] )
    && 'product' === sanitize_key( wp_unslash( $_GET['post_type'] ) );

$product_ids = ( '' !== $search_term && function_exists( 'blaines_get_product_search_ids' ) )
    ? blaines_get_product_search_ids( $search_term )
    : [];

$content_ids = ( ! $product_only && '' !== $search_term && function_exists( 'blaines_get_editorial_search_ids' ) )
    ? blaines_get_editorial_search_ids( $search_term )
    : [];

// Separate pagination allows visitors to explore each section independently.
$product_page = isset( $_GET['products_page'] )
    ? max( 1, absint( wp_unslash( $_GET['products_page'] ) ) )
    : 1;
$content_page = isset( $_GET['content_page'] )
    ? max( 1, absint( wp_unslash( $_GET['content_page'] ) ) )
    : 1;

$products_per_page = (
    ! $product_only
    && function_exists( 'blaines_search_department_post_type' )
    && blaines_search_department_post_type( $search_term )
)
    ? 3
    : 8;
$content_per_page  = 12;
$product_count     = count( $product_ids );
$content_count     = count( $content_ids );
// For explicit Interiors department searches, show website content before appliances.
$interiors_first = ! $product_only && in_array( strtolower( $search_term ), [ 'kitchen', 'kitchens', 'bathroom', 'bathrooms', 'bedroom', 'bedrooms' ], true );
$product_pages     = max( 1, (int) ceil( $product_count / $products_per_page ) );
$content_pages     = max( 1, (int) ceil( $content_count / $content_per_page ) );
$product_page      = min( $product_page, $product_pages );
$content_page      = min( $content_page, $content_pages );

$visible_product_ids = array_slice( $product_ids, ( $product_page - 1 ) * $products_per_page, $products_per_page );
$visible_content_ids = array_slice( $content_ids, ( $content_page - 1 ) * $content_per_page, $content_per_page );

$product_query = new WP_Query(
    [
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'post__in'            => $visible_product_ids ?: [ 0 ],
        'posts_per_page'      => $products_per_page,
        'orderby'             => 'post__in',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]
);

$content_query = new WP_Query(
    [
        'post_type'           => [ 'page', 'post', 'kitchen', 'bathroom', 'bedroom', 'project' ],
        'post_status'         => 'publish',
        'post__in'            => $visible_content_ids ?: [ 0 ],
        'posts_per_page'      => $content_per_page,
        'orderby'             => 'post__in',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]
);

// Preserve the term and the other section's page when changing pagination.
$search_url = home_url( '/' );
$base_args  = [ 's' => $search_term ];
if ( $product_only ) {
    $base_args['post_type'] = 'product';
}

?>
<div class="container-wide py-3 text-sm">
    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
</div>

<section class="container-wide py-8 lg:py-10" aria-labelledby="blaines-search-heading">
    <div class="border-b border-neutral-200 pb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-blue-700">
            <?php echo $product_only ? esc_html__( 'Electrical product search', 'blainestwentysix' ) : esc_html__( 'Search Blaines', 'blainestwentysix' ); ?>
        </p>
        <h1 id="blaines-search-heading" class="mt-2 font-title text-3xl font-semibold text-neutral-950 md:text-4xl">
            <?php if ( '' !== $search_term ) : ?>
                <?php printf( esc_html__( 'Results for “%s”', 'blainestwentysix' ), esc_html( $search_term ) ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Search our website', 'blainestwentysix' ); ?>
            <?php endif; ?>
        </h1>
        <p class="mt-3 text-sm text-neutral-600">
            <?php
            printf(
                esc_html( _n( '%s matching result', '%s matching results', $product_count + $content_count, 'blainestwentysix' ) ),
                esc_html( number_format_i18n( $product_count + $content_count ) )
            );
            ?>
        </p>
    </div>

    <?php if ( '' === $search_term ) : ?>
        <div class="mt-6 border border-neutral-200 bg-neutral-50 p-6">
            <p class="text-neutral-700"><?php esc_html_e( 'Enter a product, range, service or topic in the search box above.', 'blainestwentysix' ); ?></p>
        </div>
    <?php elseif ( 0 === $product_count && 0 === $content_count ) : ?>
        <div class="mt-6 border border-neutral-200 bg-neutral-50 p-6 md:p-8">
            <h2 class="font-title text-xl font-semibold text-neutral-950"><?php esc_html_e( 'No matching results', 'blainestwentysix' ); ?></h2>
            <p class="mt-2 text-neutral-600"><?php esc_html_e( 'Try a shorter phrase, a product model number, a room type, or another keyword.', 'blainestwentysix' ); ?></p>
            <a class="mt-5 inline-flex items-center bg-[#143981] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0E295E]" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Blaines', 'blainestwentysix' ); ?></a>
        </div>
    <?php else : ?>

        <?php
        // Generate each result section once and display them in the most relevant order.
        ob_start();
        ?>
        <?php if ( $product_count > 0 ) : ?>
            <div class="mt-8" id="electrical-search-results">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3 border-b border-neutral-200 pb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.13em] text-blue-700"><?php esc_html_e( 'Shop electrical', 'blainestwentysix' ); ?></p>
                        <h2 class="mt-1 font-title text-2xl font-semibold text-neutral-950"><?php esc_html_e( 'Electrical products', 'blainestwentysix' ); ?></h2>
                    </div>
                    <p class="text-sm text-neutral-600">
                        <?php printf( esc_html( _n( '%d product', '%d products', $product_count, 'blainestwentysix' ) ), absint( $product_count ) ); ?>
                    </p>
                </div>
                <div class="blaines-retail--archive blaines-search-products flex flex-col gap-6">
                    <?php while ( $product_query->have_posts() ) : $product_query->the_post(); ?>
                        <?php
                        $product = wc_get_product( get_the_ID() );
                        if ( $product ) {
                            get_template_part( 'template-parts/woocommerce/row-transactional-product', null, [ 'product' => $product ] );
                        }
                        ?>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <?php if ( $product_pages > 1 ) : ?>
                    <nav
                            class="blaines-search-pagination mt-6"
                            aria-label="<?php esc_attr_e( 'Electrical products pages', 'blainestwentysix' ); ?>"
                    >
                        <?php
                        echo wp_kses_post(
                            paginate_links(
                                [
                                    'base'      => add_query_arg( array_merge( $base_args, [ 'content_page' => $content_page, 'products_page' => '%#%' ] ), $search_url ) . '#electrical-search-results',
                                    'format'    => '',
                                    'current'   => $product_page,
                                    'total'     => $product_pages,
                                    'prev_text' => '&larr; ' . __( 'Previous', 'blainestwentysix' ),
                                    'next_text' => __( 'Next', 'blainestwentysix' ) . ' &rarr;',
                                    'type'      => 'plain',
                                ]
                            )
                        );
                        ?>

                        <p class="blaines-search-pagination__status">
                            <?php
                            echo esc_html(
                                sprintf(
                                    __( 'Page %1$d of %2$d', 'blainestwentysix' ),
                                    $product_page,
                                    $product_pages
                                )
                            );
                            ?>
                        </p>

                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php $electrical_results_html = ob_get_clean(); ?>

        <?php ob_start(); ?>
        <?php if ( $content_count > 0 ) : ?>
            <div class="mt-10" id="website-search-results">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3 border-b border-neutral-200 pb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.13em] text-blue-700"><?php esc_html_e( 'Explore Blaines', 'blainestwentysix' ); ?></p>
                        <h2 class="mt-1 font-title text-2xl font-semibold text-neutral-950"><?php esc_html_e( 'Interiors, projects & information', 'blainestwentysix' ); ?></h2>
                    </div>
                    <p class="text-sm text-neutral-600">
                        <?php printf( esc_html( _n( '%d result', '%d results', $content_count, 'blainestwentysix' ) ), absint( $content_count ) ); ?>
                    </p>
                </div>
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <?php while ( $content_query->have_posts() ) : $content_query->the_post(); ?>
                        <?php
                        $type = get_post_type();
                        $labels = [
                            'kitchen'  => __( 'Kitchen range', 'blainestwentysix' ),
                            'bathroom' => __( 'Bathroom range', 'blainestwentysix' ),
                            'bedroom'  => __( 'Bedroom range', 'blainestwentysix' ),
                            'project'  => __( 'Completed project', 'blainestwentysix' ),
                            'post'     => __( 'News & advice', 'blainestwentysix' ),
                            'page'     => __( 'Website information', 'blainestwentysix' ),
                        ];
                        $label = $labels[ $type ] ?? __( 'Explore', 'blainestwentysix' );

                        if ( 'page' === $type ) {

                            $department_labels = [
                                'electrical'       => 'Electrical · Appliances & Entertainment',
                                'kitchens'         => 'Kitchens · Design & Installation',
                                'bathrooms'        => 'Bathrooms · Design & Installation',
                                'bedrooms'         => 'Bedrooms · Design & Installation',
                                'classic-kitchens' => 'Kitchen Styles · Classic',
                                'modern-kitchens'  => 'Kitchen Styles · Modern',
                            ];

                            $page_slug = get_post_field( 'post_name', get_the_ID() );

                            if ( isset( $department_labels[ $page_slug ] ) ) {
                                $label = __( $department_labels[ $page_slug ], 'blainestwentysix' );
                            }
                        }
                        $summary = get_the_excerpt();
                        ?>

                        <article <?php post_class( 'flex h-full flex-col overflow-hidden border border-neutral-200 bg-white' ); ?>>
                            <a class="block aspect-[4/3] overflow-hidden bg-neutral-100" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">

                                <?php

                                $search_image_id = get_post_thumbnail_id();

                                if (
                                    ! $search_image_id
                                    && 'page' === get_post_type()
                                    && function_exists( 'get_field' )
                                ) {
                                    $department_hero_fields = [
                                        'kitchens'  => 'kitchen_archive_hero_image',
                                        'bathrooms' => 'bathroom_archive_hero_image',
                                        'bedrooms'  => 'bedroom_archive_hero_image',
                                    ];

                                    $page_slug = get_post_field( 'post_name', get_the_ID() );

                                    if ( isset( $department_hero_fields[ $page_slug ] ) ) {
                                        $archive_hero = get_field(
                                            $department_hero_fields[ $page_slug ],
                                            'option'
                                        );

                                        if ( is_array( $archive_hero ) ) {
                                            $search_image_id = absint( $archive_hero['ID'] ?? 0 );
                                        }
                                    }
                                }


                                if ( $search_image_id ) {
                                    echo wp_get_attachment_image(
                                        $search_image_id,
                                        'medium_large',
                                        false,
                                        [
                                            'class'   => 'h-full w-full object-cover',
                                            'loading' => 'lazy',
                                            'alt'     => '',
                                        ]
                                    );
                                } else {
                                    echo '<span class="flex h-full items-center justify-center text-sm text-neutral-400">';
                                    esc_html_e( 'Explore Blaines', 'blainestwentysix' );
                                    echo '</span>';
                                }
                                ?>

                            </a>
                            <div class="flex flex-1 flex-col p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-700"><?php echo esc_html( $label ); ?></p>
                                <h3 class="mt-2 font-title text-xl font-semibold text-neutral-950"><a class="hover:text-blue-700" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <?php if ( '' !== trim( $summary ) ) : ?>
                                    <p class="mt-2 text-sm leading-relaxed text-neutral-600"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $summary ), 22 ) ); ?></p>
                                <?php endif; ?>
                                <a class="mt-auto pt-4 inline-flex text-sm font-semibold text-[#143981] hover:underline" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Explore', 'blainestwentysix' ); ?> <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <?php if ( $content_pages > 1 ) : ?>
                    <nav
                            class="blaines-search-pagination mt-6"
                            aria-label="<?php esc_attr_e( 'Website results pages', 'blainestwentysix' ); ?>"
                    >
                        <?php
                        echo wp_kses_post(
                            paginate_links(
                                [
                                    'base'      => add_query_arg(
                                                       array_merge(
                                                           $base_args,
                                                           [
                                                               'products_page' => $product_page,
                                                               'content_page'  => '%#%',
                                                           ]
                                                       ),
                                                       $search_url
                                                   ) . '#website-search-results',
                                    'format'    => '',
                                    'current'   => $content_page,
                                    'total'     => $content_pages,
                                    'mid_size'  => 1,
                                    'end_size'  => 1,
                                    'prev_text' => '&larr; ' . __( 'Previous', 'blainestwentysix' ),
                                    'next_text' => __( 'Next', 'blainestwentysix' ) . ' &rarr;',
                                    'type'      => 'plain',
                                ]
                            )
                        );
                        ?>
                        <p class="blaines-search-pagination__status">
                            <?php
                            echo esc_html(
                                sprintf(
                                    __( 'Page %1$d of %2$d', 'blainestwentysix' ),
                                    $content_page,
                                    $content_pages
                                )
                            );
                            ?>
                        </p>
                    </nav>
                <?php endif; ?>

            </div>
        <?php endif; ?>
        <?php
        $website_results_html = ob_get_clean();
        if ( $interiors_first ) {
            echo $website_results_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by section template.
            echo $electrical_results_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by section template.
        } else {
            echo $electrical_results_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by section template.
            echo $website_results_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by section template.
        }
        ?>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
