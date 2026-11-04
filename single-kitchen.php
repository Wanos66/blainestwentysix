<?php
get_header();

$show_prices = (bool) get_field('show_kitchen_prices', 'option');

$from_price  = get_field('kitchen_from_price');   // e.g. "£2,918"
$from_month  = get_field('kitchen_from_monthly'); // e.g. "£39*/month"
$breadcrumbs = $args['breadcrumbs'] ?? true;
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <?php
    $post_id = get_the_ID();

    /*
    |--------------------------------------------------------------------------
    | Freedom accessibility state
    |--------------------------------------------------------------------------
    */
    $is_freedom_available = (bool) get_field('is_freedom_available', $post_id);

    $requested_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
    $is_freedom_mode = $is_freedom_available && ('freedom' === $requested_view);

    $freedom_gallery = get_field('freedom_accessibility_gallery', $post_id);
    $freedom_content = get_field('freedom_accessibility_content', $post_id);

    $has_freedom_gallery = !empty($freedom_gallery) && is_array($freedom_gallery);

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    $standard_url = get_permalink($post_id);
    $freedom_url  = add_query_arg('view', 'freedom', $standard_url);

    /*
    |--------------------------------------------------------------------------
    | Resolve active gallery mode
    |--------------------------------------------------------------------------
    | Freedom mode only truly uses the Freedom gallery if:
    | - the kitchen is Freedom-enabled
    | - ?view=freedom is present
    | - the Freedom gallery has images
    |
    | Otherwise the gallery partial should fall back to the normal gallery.
    |--------------------------------------------------------------------------
    */
    $use_freedom_gallery = $is_freedom_mode && $has_freedom_gallery;
    ?>

    <div class="container-wide py-6">

        <!-- Breadcrumbs -->
        <?php if ($breadcrumbs) : ?>
            <div class="text-sm">
                <?php get_template_part('template-parts/components/breadcrumbs'); ?>
            </div>
        <?php endif; ?>

        <!-- Main layout grid -->
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">

            <!-- LEFT COLUMN (Gallery) -->
            <div class="lg:col-span-8">
                <?php
                get_template_part(
                    'template-parts/single-kitchens/gallery',
                    null,
                    [
                        'post_id'             => $post_id,
                        'is_freedom_mode'     => $is_freedom_mode,
                        'use_freedom_gallery' => $use_freedom_gallery,
                        'freedom_gallery'     => $freedom_gallery,
                    ]
                );
                ?>

            </div>

            <!-- RIGHT COLUMN (Meta + Freedom switch/content + swatches + showrooms) -->
            <div class="space-y-8 lg:col-span-4">

                <?php
                get_template_part(
                    'template-parts/single-kitchens/meta',
                    null,
                    [
                        'post_id'               => $post_id,
                        'is_freedom_available'  => $is_freedom_available,
                        'is_freedom_mode'       => $is_freedom_mode,
                        'standard_url'          => $standard_url,
                        'freedom_url'           => $freedom_url,
                        'freedom_content'       => $freedom_content,
                    ]
                );
                ?>

                <div class="<?php echo $show_prices ? 'border-2 border-slate-400 p-4' : ''; ?>">
                    <?php if ($show_prices && ($from_price || $from_month)) : ?>
                        <p class="text-base text-slate-800">
                            <span class="font-medium">From</span>

                            <?php if ($from_price) : ?>
                                <span><?php echo esc_html($from_price); ?></span>
                            <?php endif; ?>

                            <?php if ($from_month) : ?>
                                <span class="text-slate-600"> or </span>
                                <span><?php echo esc_html($from_month); ?></span>
                            <?php endif; ?>
                        </p>

                        <hr class="my-4">

                        <p class="mb-6">
                            <a href="#" class="underline">What's included in the price?</a>
                        </p>
                    <?php endif; ?>

                    <a
                            href="<?php echo esc_url(home_url('/design-consultation')); ?>"
                            class="block w-full rounded-md bg-slate-900 px-4 py-3 text-center text-sm font-body font-medium text-white hover:bg-black focus:outline-none
                            focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                    >
                        Book an appointment
                    </a>
                </div>

                <?php get_template_part(
                    'template-parts/single-kitchens/swatches',
                    null,
                    [
                        'post_id'          => get_the_ID(),
                        'is_freedom_mode'  => $is_freedom_mode,
                    ]
                ); ?>

            </div>

        </div>

    </div>

    <?php
    get_template_part(
        'template-parts/ranges/brand-features',
        null,
        [
            'post_id'             => $post_id,
            'is_freedom_mode'     => $is_freedom_mode,
            'is_freedom_available'=> $is_freedom_available,
        ]
    );
    ?>

    <?php get_template_part('template-parts/single-kitchens/showrooms'); ?>

<div class="container-wide">
    <?php
    get_template_part('template-parts/components/cta-strip', null, [
        'title' => 'Ready to start planning your new kitchen?',
        'text'  => 'Visit one of our Norfolk showrooms or book a free design appointment with our team.',
        'button' => [
            'url'   => '/design-consultation/',
            'label' => 'Book a free design appointment',
        ],
        'tone' => 'light',
    ]);
    ?>

    <?php get_template_part('template-parts/single-kitchens/related'); ?>
</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>