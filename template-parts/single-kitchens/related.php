<?php
$post_id = get_the_ID();

$is_freedom_available = (bool) get_field('is_freedom_available', $post_id);
$requested_view       = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
$is_freedom_mode      = $is_freedom_available && ('freedom' === $requested_view);

if ($is_freedom_mode) :
    $related_freedom = new WP_Query([
        'post_type'      => 'kitchen',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        'post__not_in'   => [$post_id],
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'is_freedom_available',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
    ]);
    ?>

    <?php if ($related_freedom->have_posts()) : ?>
    <section class="prose-site my-14 border-t border-slate-200 pt-10">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--freedom-accent)]">
                    Freedom kitchen ranges
                </p>
                <h2 class="mt-2 font-heading text-3xl font-light tracking-tight text-slate-900 sm:text-4xl">
                    Related Freedom kitchen ranges
                </h2>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <?php while ($related_freedom->have_posts()) : $related_freedom->the_post(); ?>
                <?php
                $related_id = get_the_ID();

                $freedom_url = add_query_arg(
                    'view',
                    'freedom',
                    get_permalink($related_id)
                );

                $freedom_archive_image = get_field('freedom_archive_image', $related_id);
                $freedom_gallery       = get_field('freedom_accessibility_gallery', $related_id);

                $card_image_id = 0;

                if (! empty($freedom_archive_image)) {
                    if (is_array($freedom_archive_image) && ! empty($freedom_archive_image['ID'])) {
                        $card_image_id = (int) $freedom_archive_image['ID'];
                    } elseif (is_numeric($freedom_archive_image)) {
                        $card_image_id = (int) $freedom_archive_image;
                    }
                }

                if (! $card_image_id && ! empty($freedom_gallery) && is_array($freedom_gallery)) {
                    $first_gallery_image = $freedom_gallery[0] ?? null;

                    if (is_array($first_gallery_image) && ! empty($first_gallery_image['ID'])) {
                        $card_image_id = (int) $first_gallery_image['ID'];
                    }
                }

                if (! $card_image_id && has_post_thumbnail($related_id)) {
                    $card_image_id = get_post_thumbnail_id($related_id);
                }

                $features    = get_field('features', $related_id);
                $show_prices = (bool) get_field('show_kitchen_prices', 'option');
                $from_price  = get_field('kitchen_from_price', $related_id);
                $from_month  = get_field('kitchen_from_monthly', $related_id);
                ?>
                <article class="group flex flex-col overflow-hidden border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md">
                    <div class="relative block">
                        <?php if ($card_image_id) : ?>
                            <?php
                            echo wp_get_attachment_image(
                                $card_image_id,
                                'large',
                                false,
                                [
                                    'class'   => 'aspect-[16/10] w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]',
                                    'loading' => 'lazy',
                                    'alt'     => get_the_title($related_id),
                                ]
                            );
                            ?>
                        <?php else : ?>
                            <div class="aspect-[16/10] bg-slate-100"></div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-1 flex-col px-5 py-6">
                        <div class="mb-2 flex items-start justify-between">
                            <h3 class="text-3xl font-medium leading-snug tracking-wide text-slate-900">
                                <a
                                    href="<?php echo esc_url($freedom_url); ?>"
                                    class="rounded-sm decoration-1 underline-offset-4 group-hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                                    aria-label="View <?php echo esc_attr(get_the_title($related_id)); ?> kitchen range in Freedom mode"
                                >
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <span class="ml-3 shrink-0 rounded-sm border border-slate-300 px-2 py-0.5 text-[10px] uppercase tracking-wide text-slate-700">
                                    Freedom
                                </span>
                        </div>

                        <?php if ($show_prices && ($from_price || $from_month)) : ?>
                            <p class="mt-2 text-base text-slate-800">
                                <span class="font-medium">From</span>
                                <?php if ($from_price) : ?>
                                    <span><?php echo esc_html($from_price); ?></span>
                                <?php endif; ?>

                                <?php if ($from_month) : ?>
                                    <span class="text-slate-600"> or </span>
                                    <span><?php echo esc_html($from_month); ?></span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($features) : ?>
                            <ul class="mb-6 mt-3 space-y-1.5">
                                <?php foreach ($features as $row) : ?>
                                    <?php if (! empty($row['feature_text'])) : ?>
                                        <li class="flex items-start gap-2 text-sm font-lexend-extralight leading-relaxed text-slate-700">
                                            <span class="mt-0.5 text-green-700">✓</span>
                                            <span><?php echo esc_html($row['feature_text']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <?php if (has_excerpt()) : ?>
                                <p class="mb-6 mt-2 text-sm leading-relaxed text-slate-700">
                                    <?php echo esc_html(get_the_excerpt()); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <a
                            href="<?php echo esc_url($freedom_url); ?>"
                            class="mt-auto block w-full border border-[var(--freedom-accent)] bg-[var(--freedom-accent)] px-4 py-3 text-center text-sm font-medium text-white transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--freedom-accent)] focus-visible:ring-offset-2"
                        >
                            View <?php echo esc_html(get_the_title($related_id)); ?> kitchen collection
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>

    <?php wp_reset_postdata(); ?>
<?php endif; ?>

<?php else : ?>

    <?php
    get_template_part('template-parts/components/related-by-style-brand', null, [
        'post_type' => 'kitchen',
        'style_tax' => 'kitchen_style',
        'brand_tax' => 'kitchen_brand',
        'heading'   => 'Related Kitchens',
        'limit'     => 3,
    ]);
    ?>

<?php endif; ?>