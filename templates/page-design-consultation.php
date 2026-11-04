<?php
/**
 * Template Name: Design Consultation
 */

get_header('consultation');

$hero_heading   = get_field('consult_hero_heading') ?: 'Request an Appointment';
$hero_intro     = get_field('consult_hero_intro') ?: 'Meet with one of our expert designers at home or in one of our beautiful showrooms and discover what’s possible. Complete your details below to begin your appointment request.';
$form_shortcode = get_field('consult_form_plugin_shortcode');
?>

    <main class="design-consultation-main min-h-screen bg-white">
        <section class="px-6 pt-4 pb-2 md:pt-5 md:pb-4">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-neutral-500">
                    Design Consultation
                </p>

                <h1 class="mt-2 text-[2rem] leading-[1.05] tracking-[-0.02em] text-neutral-900 md:text-[2.55rem]">
                    <?php echo esc_html($hero_heading); ?>
                </h1>

                <?php if ($hero_intro) : ?>
                    <p class="mx-auto mt-3 max-w-[38rem] text-[1rem] leading-7 text-neutral-600">
                        <?php echo esc_html($hero_intro); ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>

        <section class="px-6 pb-16 md:pb-24">
            <div class="mx-auto max-w-3xl border-t border-neutral-200 pt-6 md:pt-8">
                <?php if ($form_shortcode) : ?>
                    <?php echo do_shortcode($form_shortcode); ?>
                <?php else : ?>
                    <p class="text-center text-neutral-600">
                        Please add a WPForms shortcode in the Design Consultation page fields.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </main>

<?php get_footer('consultation'); ?>