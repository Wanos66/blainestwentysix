<?php
/**
 * Other Showrooms Strip
 */

$current_id   = get_the_ID();
$template_slug = get_page_template_slug($current_id);

$args = [
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'post__not_in'   => [$current_id],
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => '_wp_page_template',
            'value'   => $template_slug,
            'compare' => '=',
        ],
    ],
];

$showrooms = new WP_Query($args);

if ($showrooms->have_posts()) :
    ?>
    <section class="bg-gray-50 py-10">
        <div class="container-wide">

            <h2 class="font-heading text-xl font-semibold text-gray-900 mb-6">
                Visit our other showrooms
            </h2>

            <div class="grid gap-6 md:grid-cols-2">

                <?php while ($showrooms->have_posts()) : $showrooms->the_post();

                    $post_id  = get_the_ID();
                    $image_id = function_exists('get_field') ? (int) get_field('storefront_image', $post_id) : 0;
                    $address  = function_exists('get_field') ? (string) get_field('address', $post_id) : '';
                    $title    = get_the_title($post_id);
                    ?>

                    <a
                            href="<?php echo esc_url(get_permalink($post_id)); ?>"
                            class="group flex items-center gap-4 border border-gray-200 bg-white p-4 transition-shadow hover:shadow-sm"
                    >
                        <?php if ($image_id) : ?>
                            <div class="w-24 h-16 overflow-hidden bg-gray-200 shrink-0">
                                <?php
                                echo wp_get_attachment_image(
                                    $image_id,
                                    'medium',
                                    false,
                                    [
                                        'class'    => 'w-full h-full object-cover transition duration-300 group-hover:scale-[1.03]',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="min-w-0">
                            <div class="font-heading text-base font-semibold text-gray-900 group-hover:underline">
                                <?php echo esc_html($title); ?>
                            </div>

                            <?php if ($address) : ?>
                                <div class="mt-1 font-body text-sm leading-snug text-gray-600">
                                    <?php echo esc_html(str_replace("\n", ', ', $address)); ?>
                                </div>
                            <?php endif; ?>

                            <div class="text-xs text-gray-500 mt-1 group-hover:text-brand transition-colors">
                                View showroom →
                            </div>
                        </div>
                    </a>

                <?php endwhile; ?>

            </div>

        </div>
    </section>
<?php
endif;

wp_reset_postdata();
?>