</main>

<footer aria-labelledby="site-footer-heading">
    <h2 id="site-footer-heading" class="sr-only">
        <?php esc_html_e('Site footer', 'blainestwentysix'); ?>
    </h2>

    <?php
    // Newsletter content from ACF.
    $nl_heading   = function_exists('get_field') ? get_field('newsletter_footer_newsletter_heading', 'option') : '';
    $nl_intro     = function_exists('get_field') ? get_field('newsletter_footer_newsletter_intro', 'option') : '';
    $nl_privacy   = function_exists('get_field') ? get_field('newsletter_footer_newsletter_privacy_text', 'option') : '';
    $nl_extra     = function_exists('get_field') ? get_field('newsletter_footer_newsletter_extra_note', 'option') : '';
    $nl_shortcode = function_exists('get_field') ? get_field('newsletter_footer_newsletter_form_shortcode', 'option') : '';
    ?>

    <?php
    // Head office details.
    $hq_heading     = function_exists('get_field') ? get_field('head_office_footer_hq_heading', 'option') : '';
    $hq_address     = function_exists('get_field') ? get_field('head_office_footer_hq_address', 'option') : '';
    $hq_phone_label = function_exists('get_field') ? get_field('head_office_footer_hq_phone_label', 'option') : '';
    $hq_phone       = function_exists('get_field') ? get_field('head_office_footer_hq_phone', 'option') : '';
    $hq_email_label = function_exists('get_field') ? get_field('head_office_footer_hq_email_label', 'option') : '';
    $hq_email       = function_exists('get_field') ? get_field('head_office_footer_hq_email', 'option') : '';

    $trust_text  = function_exists('get_field') ? get_field('footer_trustpilot_text', 'option') : '';
    $trust_brand = function_exists('get_field') ? get_field('footer_trustpilot_brand', 'option') : '';
    ?>

    <section class="border-t border-white/10 bg-slate-700">
        <a
                href="#site-header"
                class="flex min-h-[52px] items-center justify-center font-body text-sm font-medium text-white transition hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-white/40"
        >
            Back to top
        </a>
    </section>

    <!-- Main footer content (5-column layout, 1280px) -->
    <section class="bg-black border-t border-b-neutral-400">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 lg:py-14 space-y-10">

            <?php
            // Newsletter content from ACF (already defined above in your file)
            // HQ content from ACF (already defined above in your file)
            ?>

            <!-- Row 1: 5 columns -->
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">

                <!-- Col 1: Logo + Head Office -->
                <div class="lg:col-span-3 space-y-5 text-center lg:text-left">
                    <div>
                        <?php $footer_logo_id = get_theme_mod('footer_logo'); ?>

                        <?php if ($footer_logo_id) : ?>
                            <div class="mb-4 flex justify-center lg:justify-start">
                                <?php echo wp_get_attachment_image(
                                    $footer_logo_id,
                                    'full',
                                    false,
                                    ['class' => 'h-16 w-auto']
                                ); ?>
                            </div>
                        <?php elseif (function_exists('the_custom_logo') && has_custom_logo()) : ?>
                            <div class="mb-4 flex justify-center lg:justify-start">
                                <?php the_custom_logo(); ?>
                            </div>
                        <?php else : ?>
                            <span class="font-body text-xl font-semibold tracking-[-0.02em] text-white">
              <?php bloginfo('name'); ?>
            </span>
                        <?php endif; ?>

                        <?php if ($hq_heading) : ?>
                            <p class="mt-1 font-body text-base font-semibold tracking-[0.04em] text-white">
                                <?php echo esc_html($hq_heading); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($hq_address) : ?>
                            <p class="mt-2 font-body text-sm leading-relaxed text-white/80">
                                <?php echo nl2br(esc_html($hq_address)); ?>
                            </p>
                        <?php endif; ?>

                        <div class="mt-3 space-y-1 font-body text-sm text-white/80">
                            <?php if ($hq_phone) : ?>
                                <p class="flex flex-col items-center gap-0.5 lg:flex-row lg:items-center lg:gap-2">
                                    <?php if ($hq_phone_label) : ?>
                                        <span class="font-body font-semibold text-white"><?php echo esc_html($hq_phone_label); ?>:</span>
                                    <?php endif; ?>
                                    <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $hq_phone)); ?>" class="hover:text-white transition">
                                        <?php echo esc_html($hq_phone); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if ($hq_email) : ?>
                                <p class="flex flex-col items-center gap-0.5 lg:flex-row lg:items-center lg:gap-2">
                                    <?php if ($hq_email_label) : ?>
                                        <span class="font-body font-semibold text-white"><?php echo esc_html($hq_email_label); ?>:</span>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo esc_attr($hq_email); ?>" class="hover:text-white transition break-all">
                                        <?php echo esc_html($hq_email); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Social icons -->
                    <?php if (function_exists('have_rows') && have_rows('footer_social_links', 'option')) : ?>
                        <div class="flex flex-wrap justify-center gap-2 pt-1 lg:justify-start">
                            <?php while (have_rows('footer_social_links', 'option')) : the_row();
                                $platform = get_sub_field('platform');
                                $url      = get_sub_field('url');
                                if (!$platform || !$url) continue;
                                $icon_url = get_template_directory_uri() . "/assets/icons/social/{$platform}.svg";
                                ?>
                                <a
                                        href="<?php echo esc_url($url); ?>"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/80 bg-white/80 hover:bg-white
                                        hover:border-slate-900
                                         transition"
                                >
                                    <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr(ucfirst($platform)); ?>" class="h-4 w-4">
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                    <?php $trust_widget = get_field('footer_trustpilot_widget', 'option'); ?>
                    <?php if ($trust_widget) : ?>
                        <div class="border-t border-white/10 pt-8">
                            <?php echo $trust_widget; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Col 2: About Us -->
                <div class="lg:col-span-2 text-center lg:text-left">
                    <h3 class="mb-3 font-body text-base font-semibold tracking-[-0.02em] text-white">
                        <?php esc_html_e('About Us', 'blainestwentysix'); ?>
                    </h3>
                    <?php
                    if (has_nav_menu('footer_about')) {
                        wp_nav_menu([
                            'theme_location' => 'footer_about',
                            'container'      => false,
                            'menu_class' => 'space-y-1 font-body text-sm text-white/80 text-center lg:text-left',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                            'link_before' => '<span class="inline-block py-0.5 hover:text-white transition">',
                            'link_after'     => '</span>',
                        ]);
                    }
                    ?>
                </div>

                <!-- Col 3: Our Services -->
                <div class="lg:col-span-2 text-center lg:text-left">
                    <h3 class="mb-3 font-body text-base font-semibold tracking-[-0.02em] text-white">
                        <?php esc_html_e('Our Services', 'blainestwentysix'); ?>
                    </h3>
                    <?php
                    if (has_nav_menu('footer_services')) {
                        wp_nav_menu([
                            'theme_location' => 'footer_services',
                            'container'      => false,
                            'menu_class' => 'space-y-1 font-body text-sm text-white/80 text-center lg:text-left',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                            'link_before' => '<span class="inline-block py-0.5 hover:text-white transition">',
                            'link_after'     => '</span>',
                        ]);
                    }
                    ?>
                </div>

                <!-- Col 4: More Info -->
                <div class="lg:col-span-2 text-center lg:text-left">
                    <h3 class="mb-3 font-body text-base font-semibold tracking-[-0.02em] text-white">
                        <?php esc_html_e('More Info', 'blainestwentysix'); ?>
                    </h3>
                    <?php
                    if (has_nav_menu('footer_more_info')) {
                        wp_nav_menu([
                            'theme_location' => 'footer_more_info',
                            'container'      => false,
                            'menu_class' => 'space-y-1 font-body text-sm text-white/80 text-center lg:text-left',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                            'link_before' => '<span class="inline-block py-0.5 hover:text-white transition">',
                            'link_after'     => '</span>',
                        ]);
                    }
                    ?>
                </div>

                <!-- Col 5: Newsletter -->
                <div id="newsletter" class="lg:col-span-3 text-center lg:text-left">
                    <h3 class="mb-3 font-body text-base font-semibold tracking-[-0.02em] text-white">
                        <?php echo esc_html($nl_heading ?: 'Newsletter'); ?>
                    </h3>

                    <?php if ($nl_intro) : ?>
                        <p class="mx-auto max-w-[32ch] font-body text-center text-sm leading-relaxed text-white/80 lg:mx-0 lg:text-left">
                            <?php echo esc_html($nl_intro); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($nl_shortcode) : ?>
                        <div class="mt-4 font-body max-w-sm mx-auto lg:mx-0">
                            <?php echo do_shortcode(wp_kses_post($nl_shortcode)); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($nl_privacy) : ?>
                        <p class="mt-3 font-body text-xs text-white/60 leading-relaxed max-w-[32ch] mx-auto text-center lg:mx-0 lg:text-left">
                            <?php echo esc_html($nl_privacy); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($nl_extra) : ?>
                        <p class="mt-2 font-body text-xs text-white/60 leading-relaxed max-w-[32ch] mx-auto text-center lg:mx-0 lg:text-left">
                            <?php echo esc_html($nl_extra); ?>
                        </p>
                    <?php endif; ?>
                </div>

            </div>


        </div>
    </section>

    <!-- Bottom legal bar -->
    <?php
    $legal_text = function_exists('get_field') ? get_field('footer_legal_text', 'option') : '';
    ?>
    <section class="bg-slate-950 text-white text-xs border-t border-slate-400">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between gap-4">
                <p class="font-body text-[11px] leading-relaxed">
                    <?php
                    if ($legal_text) {
                        echo esc_html($legal_text);
                    } else {
                        printf(
                            '© %1$s %2$s. %3$s',
                            esc_html(date_i18n('Y')),
                            esc_html(get_bloginfo('name')),
                            esc_html__('All rights reserved.', 'blainestwentysix')
                        );
                    }
                    ?>
                </p>

                <?php if (has_nav_menu('footer_info')) : ?>
                    <div class="hidden md:flex flex-wrap gap-x-4 gap-y-1">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer_info',
                                'container'      => false,
                                'menu_class' => 'flex flex-wrap gap-x-4 gap-y-1 font-body text-[11px]',
                                'depth'          => 1,
                                'fallback_cb'    => false,
                                'link_before'    => '<span class="hover:text-slate-100 transition">',
                                'link_after'     => '</span>',
                            )
                        );
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</footer>


<?php wp_footer(); ?>

</body></html>
