<?php
/**
 * Template Name: About Us
 * Description: Custom About Us page for Blaines.
 */

get_header();

// HERO ACF fields
$subtitle      = get_field('page_subtitle', 'option');
$hero_image    = get_field('page_hero_image', 'option');

// MISSION ACF fields
$mission_image   = get_field('mission_image', 'option');
$mission_title   = get_field('mission_title', 'option');
$mission_content = get_field('mission_content', 'option');
?>

<!-- ============================
     HERO SECTION (HubSpot style)
     ============================ -->
<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- LEFT: Title + Subtitle -->
            <div class="space-y-6">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 lg:text-5xl">
                    <?php the_title(); ?>
                </h1>

                <?php if ($subtitle): ?>
                    <p class="text-lg text-slate-700 leading-relaxed max-w-prose">
                        <?php echo esc_html($subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Hero Image -->
            <?php if ($hero_image): ?>
                <div class="flex justify-center lg:justify-end">
                    <img src="<?php echo esc_url($hero_image['url']); ?>"
                            alt=""
                            class="w-full max-w-lg h-auto object-cover rounded-xl">
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>


<!-- ============================
     MISSION SECTION (HubSpot style)
     ============================ -->
<section class="py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Mission Title + Content -->
            <div class="space-y-6 lg:order-2">

                <?php if ($mission_title): ?>
                    <h2 class="text-2xl font-semibold text-slate-900 lg:text-3xl">
                        <?php echo esc_html($mission_title); ?>
                    </h2>
                <?php endif; ?>

                <?php if ($mission_content): ?>
                    <div class="prose prose-slate max-w-none">
                        <?php echo wp_kses_post($mission_content); ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Mission Image -->
            <?php if ($mission_image): ?>
                <div class="flex justify-center lg:justify-start lg:order-1">
                    <img src="<?php echo esc_url($mission_image['url']); ?>"
                            alt="<?php echo esc_attr($mission_title); ?>"
                            class="w-full max-w-lg h-auto object-cover rounded-xl">
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- ============================
     SECTION 3 (HubSpot style)
     ============================ -->
<?php
$section3_title   = get_field('section3_title', 'option');
$section3_content = get_field('section3_content', 'option');
$section3_image   = get_field('section3_image', 'option');
?>

<?php if ($section3_title || $section3_content || $section3_image) : ?>
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- LEFT: TEXT -->
                <div class="space-y-6">

                    <?php if ($section3_title): ?>
                        <h2 class="text-2xl font-semibold text-slate-900 lg:text-3xl">
                            <?php echo esc_html($section3_title); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ($section3_content): ?>
                        <div class="prose prose-slate max-w-none">
                            <?php echo wp_kses_post($section3_content); ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- RIGHT: IMAGE -->
                <?php if ($section3_image): ?>
                    <div class="flex justify-center lg:justify-end">
                        <img src="<?php echo esc_url($section3_image['url']); ?>"
                                alt="<?php echo esc_attr($section3_title); ?>"
                                class="w-full max-w-lg h-auto object-cover rounded-xl">
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
<?php endif; ?>

<!-- ============================
     SECTION 4 (HubSpot style, reversed)
     ============================ -->
<?php
$section4_title   = get_field('section4_title', 'option');
$section4_content = get_field('section4_content', 'option');
$section4_image   = get_field('section4_image', 'option');
?>

<?php if ($section4_title || $section4_content || $section4_image) : ?>
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">

            <!-- Grid: reverse columns on desktop, normal flow on mobile -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- TEXT (always first on mobile, second on desktop) -->
                <div class="space-y-6 lg:order-2">

                    <?php if ($section4_title): ?>
                        <h2 class="text-2xl font-semibold text-slate-900 lg:text-3xl">
                            <?php echo esc_html($section4_title); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ($section4_content): ?>
                        <div class="prose prose-slate max-w-none">
                            <?php echo wp_kses_post($section4_content); ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- IMAGE (always second on mobile, first on desktop) -->
                <?php if ($section4_image): ?>
                    <div class="flex justify-center lg:justify-start lg:order-1">
                        <img src="<?php echo esc_url($section4_image['url']); ?>"
                                alt="<?php echo esc_attr($section4_title); ?>"
                                class="w-full max-w-lg h-auto object-cover rounded-xl">
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </section>
<?php endif; ?>

<!-- ============================
     SECTION 5 (Full-width Statement)
     ============================ -->
<?php
$section5_title   = get_field('section5_title', 'option');
$section5_content = get_field('section5_content', 'option');
?>

<?php if ($section5_title || $section5_content) : ?>
    <section class="bg-slate-50 py-24">
        <div class="mx-auto max-w-4xl px-4 text-center">

            <?php if ($section5_title): ?>
                <h2 class="text-3xl font-semibold text-slate-900 sm:text-4xl mb-6">
                    <?php echo esc_html($section5_title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($section5_content): ?>
                <div class="prose prose-slate mx-auto text-lg">
                    <?php echo wp_kses_post($section5_content); ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>

<?php get_template_part('template-parts/reviews'); ?>

<!-- ============================
     SECTION 6 (Final CTA - HubSpot Style)
     ============================ -->
<?php
$section6_title        = get_field('section6_title', 'option');
$section6_content      = get_field('section6_content', 'option');
$section6_button_text  = get_field('section6_button_text', 'option') ?: 'Learn More';
$section6_button_url   = get_field('section6_button_url', 'option') ?: '/showrooms';
?>

<?php if ($section6_title || $section6_content) : ?>
    <section class="bg-slate-50 py-24">
        <div class="mx-auto max-w-3xl px-4 text-center space-y-8">

            <?php if ($section6_title): ?>
                <h2 class="text-3xl font-semibold sm:text-4xl">
                    <?php echo esc_html($section6_title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($section6_content): ?>
                <p class="text-lg opacity-90 max-w-2xl mx-auto leading-relaxed">
                    <?php echo wp_kses_post($section6_content); ?>
                </p>
            <?php endif; ?>

            <?php if ($section6_button_text && $section6_button_url): ?>
                <a href="<?php echo esc_url($section6_button_url); ?>"
                        class="inline-block rounded-md bg-white text-brand font-semibold px-10 py-3 shadow-sm transition hover:bg-slate-100">
                    <?php echo esc_html($section6_button_text); ?>
                </a>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>


<?php get_footer(); ?>

