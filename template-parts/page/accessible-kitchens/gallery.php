<?php
/* -------------------------------------------------------------------------- */
/* File: template-parts/page/accessible-kitchens/gallery.php                  */
/* -------------------------------------------------------------------------- */
?>

<section id="freedom-ranges" class="border-t border-neutral-200 bg-neutral-50 py-12 lg:py-16">
    <div class="prose-site mx-auto w-full max-w-7xl px-6 lg:px-8">

        <div class="max-w-3xl">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-neutral-500">
                Freedom kitchen ranges
            </p>

            <h2 class="mt-3 text-3xl tracking-wide text-neutral-900 lg:text-[2.2rem]">
                Explore kitchen ranges available with Freedom accessibility options
            </h2>

            <p class="mt-6 leading-7 text-neutral-700">
                These selected ranges are available with Freedom accessibility options, combining practical design support with the same considered style and quality found across our wider kitchen collection.
            </p>
        </div>

        <?php
        $freedom_kitchens = new WP_Query(
            [
                'post_type'      => 'kitchen',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'meta_query'     => [
                    [
                        'key'     => 'is_freedom_available',
                        'value'   => '1',
                        'compare' => '=',
                    ],
                ],
            ]
        );

        $freedom_count = (int) $freedom_kitchens->post_count;
        ?>

        <div
                class="mt-10"
                x-data="{ expanded: false }"
        >
            <?php if ( $freedom_kitchens->have_posts() ) : ?>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <?php
                    $card_index = 0;

                    while ( $freedom_kitchens->have_posts() ) :
                        $freedom_kitchens->the_post();

                        $post_id = get_the_ID();
                        $card_index++;

                        $brand      = get_the_terms( $post_id, 'kitchen_brand' );
                        $brand_name = $brand && ! is_wp_error( $brand ) ? $brand[0]->name : '';

                        $features = get_field( 'features', $post_id );

                        $show_prices = (bool) get_field( 'show_kitchen_prices', 'option' );
                        $from_price  = get_field( 'kitchen_from_price', $post_id );
                        $from_month  = get_field( 'kitchen_from_monthly', $post_id );

                        $freedom_return_url = home_url( '/accessible-kitchens/#freedom-ranges' );

                        $freedom_return_url = home_url( '/accessible-kitchens/#freedom-ranges' );

                        $return_target = '/accessible-kitchens/#freedom-ranges';

                        $freedom_url = add_query_arg(
                            [
                                'view'   => 'freedom',
                                'return' => rawurlencode( $return_target ),
                            ],
                            get_permalink( $post_id )
                        );
                        /*
                         * Resolve Freedom card image
                         * 1. freedom_archive_image
                         * 2. first image from freedom_accessibility_gallery
                         * 3. featured image
                         */
                        $freedom_archive_image = get_field( 'freedom_archive_image', $post_id );
                        $freedom_gallery       = get_field( 'freedom_accessibility_gallery', $post_id );

                        $card_image_id = 0;

                        if ( ! empty( $freedom_archive_image ) ) {
                            if ( is_array( $freedom_archive_image ) && ! empty( $freedom_archive_image['ID'] ) ) {
                                $card_image_id = (int) $freedom_archive_image['ID'];
                            } elseif ( is_numeric( $freedom_archive_image ) ) {
                                $card_image_id = (int) $freedom_archive_image;
                            }
                        }

                        if ( ! $card_image_id && ! empty( $freedom_gallery ) && is_array( $freedom_gallery ) ) {
                            $first_gallery_image = $freedom_gallery[0] ?? null;

                            if ( is_array( $first_gallery_image ) && ! empty( $first_gallery_image['ID'] ) ) {
                                $card_image_id = (int) $first_gallery_image['ID'];
                            }
                        }

                        if ( ! $card_image_id && has_post_thumbnail( $post_id ) ) {
                            $card_image_id = get_post_thumbnail_id( $post_id );
                        }

                        $is_hidden_initially = $card_index > 3;
                        ?>
                        <article
                                class="group flex flex-col overflow-hidden border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
                            <?php if ( $is_hidden_initially ) : ?>
                                x-show="expanded"
                                x-transition.opacity.duration.250ms
                                style="display: none;"
                            <?php endif; ?>
                        >
                            <div class="relative block">
                                <?php if ( $card_image_id ) : ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                        $card_image_id,
                                        'large',
                                        false,
                                        [
                                            'class'   => 'aspect-[16/10] w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]',
                                            'loading' => 'lazy',
                                            'alt'     => get_the_title( $post_id ),
                                        ]
                                    );
                                    ?>
                                <?php else : ?>
                                    <div class="aspect-[16/10] bg-slate-100"></div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-1 flex-col px-5 py-6">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="text-3xl leading-snug tracking-wide text-slate-900">
                                        <a
                                                href="<?php echo esc_url( $freedom_url ); ?>"
                                                class="rounded-sm decoration-1 underline-offset-4 group-hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                                                aria-label="View <?php echo esc_attr( get_the_title( $post_id ) ); ?> kitchen range in Freedom mode"
                                        >
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    <span class="ml-3 shrink-0 rounded-sm border border-slate-300 px-2 py-0.5 text-[10px] uppercase tracking-wide text-slate-700">
                                        Freedom
                                    </span>
                                </div>

                                <?php if ( $show_prices && ( $from_price || $from_month ) ) : ?>
                                    <p class="mt-2 text-slate-800">
                                        <span class="font-medium">From</span>
                                        <?php if ( $from_price ) : ?>
                                            <span><?php echo esc_html( $from_price ); ?></span>
                                        <?php endif; ?>

                                        <?php if ( $from_month ) : ?>
                                            <span class="text-slate-600"> or </span>
                                            <span><?php echo esc_html( $from_month ); ?></span>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ( $features ) : ?>
                                    <ul class="mb-6 mt-3 space-y-1.5">
                                        <?php foreach ( $features as $row ) : ?>
                                            <?php if ( ! empty( $row['feature_text'] ) ) : ?>
                                                <li class="flex items-start gap-2 text-sm leading-relaxed text-slate-700">
                                                    <span class="mt-0.5 text-green-700">✓</span>
                                                    <span><?php echo esc_html( $row['feature_text'] ); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <?php if ( has_excerpt() ) : ?>
                                        <p class="mb-6 mt-2 text-sm leading-relaxed text-slate-700">
                                            <?php echo esc_html( get_the_excerpt() ); ?>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <a
                                        href="<?php echo esc_url( $freedom_url ); ?>"
                                        class="mt-auto block w-full border border-[var(--freedom-accent)] bg-[var(--freedom-accent)] px-4 py-3 text-center text-sm font-medium text-white transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--freedom-accent)] focus-visible:ring-offset-2"
                                >
                                    View <?php echo esc_html( get_the_title( $post_id ) ); ?> kitchen collection
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php if ( $freedom_count > 3 ) : ?>
                    <div class="mt-8 text-center">
                        <button
                                type="button"
                                class="inline-flex items-center justify-center bg-[var(--freedom-accent)] px-6 py-3 text-sm font-body font-medium text-white transition
                                hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--freedom-accent)] focus:ring-offset-2"
                                @click="expanded = !expanded"
                                x-text="expanded ? 'Show fewer ranges' : 'Show more ranges'"
                                :aria-expanded="expanded.toString()"
                        >
                            Show more ranges
                        </button>
                    </div>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="border border-dashed border-neutral-300 p-8 text-sm text-neutral-600">
                    No Freedom kitchen ranges have been added yet.
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>