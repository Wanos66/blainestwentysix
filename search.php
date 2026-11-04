<?php get_header(); ?>

<?php
$product_search_requested = is_search()
                            && isset( $_GET['post_type'] )
                            && 'product' === sanitize_key( wp_unslash( $_GET['post_type'] ) );

if ( $product_search_requested ) :
    $search_term = get_search_query();
    $product_ids = function_exists( 'blaines_get_product_search_ids' )
        ? blaines_get_product_search_ids( $search_term )
        : [];

    $products_query = new WP_Query(
        [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 24,
            'post__in'       => ! empty( $product_ids ) ? $product_ids : [ 0 ],
            'orderby'        => 'post__in',
        ]
    );
    ?>

    <div class="container-wide py-3 text-sm">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>

    <section class="container-wide py-10 lg:py-14">
        <div class="border-b border-neutral-200 pb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-700">
                Product search
            </p>

            <h1 class="mt-3 text-4xl font-light text-neutral-950 md:text-5xl">
                Search results for “<?php echo esc_html( $search_term ); ?>”
            </h1>

            <p class="mt-5 max-w-3xl text-lg font-light leading-relaxed text-neutral-600">
                Browse matching appliances, televisions and audio products available from Blaines.
            </p>
        </div>

        <div class="mt-8 flex flex-col gap-4 border-b border-neutral-200 pb-5 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-neutral-600">
                <?php
                printf(
                    esc_html( _n( '%d product found', '%d products found', count( $product_ids ), 'blainestwentysix' ) ),
                    count( $product_ids )
                );
                ?>
            </p>

            <a
                    href="<?php echo esc_url( home_url( '/electrical/' ) ); ?>"
                    class="inline-flex text-sm font-semibold uppercase tracking-[0.1em] text-neutral-500 transition hover:text-neutral-950"
            >
                Browse all electrical departments
            </a>
        </div>

        <?php if ( $products_query->have_posts() ) : ?>
            <div class="mt-6 divide-y divide-neutral-200">
                <?php
                while ( $products_query->have_posts() ) :
                    $products_query->the_post();

                    $product = wc_get_product( get_the_ID() );

                    if ( ! $product ) {
                        continue;
                    }

                    get_template_part(
                        'template-parts/woocommerce/row-transactional-product',
                        null,
                        [
                            'product' => $product,
                        ]
                    );
                endwhile;

                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <div class="mt-10 border border-neutral-200 bg-neutral-50 p-8 text-center">
                <p class="text-2xl font-light text-neutral-950">
                    No products found for “<?php echo esc_html( $search_term ); ?>”
                </p>

                <p class="mt-3 text-sm leading-relaxed text-neutral-600">
                    Try searching by product name, brand, model code, product type, or call Web Sales for help.
                </p>

                <a
                        href="<?php echo esc_url( home_url( '/electrical/' ) ); ?>"
                        class="mt-6 inline-flex bg-neutral-950 px-5 py-3 text-sm font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-neutral-800"
                >
                    Browse electrical departments
                </a>
            </div>
        <?php endif; ?>
    </section>

    <?php
    get_footer();
    return;
endif;
?>

<div class="container-wide mx-auto w-full py-20 px-4">

    <?php get_template_part( 'template-parts/components/filter-dropdown' ); ?>

    <?php get_template_part( 'template-parts/loop/content-area' ); ?>

</div>

<?php get_footer(); ?>
