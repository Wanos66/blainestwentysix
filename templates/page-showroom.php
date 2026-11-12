<?php
/**
 * Template Name: Showroom
 */

declare(strict_types=1);

get_header();

function blaines_get_field(string $key, $default = null)
{
    if (function_exists('get_field')) {
        $value = get_field($key);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    return $default;
}

$esc_tel = static function (?string $tel): string {
    if ( ! $tel) {
        return '';
    }

    return (string)preg_replace('/[^0-9+]/', '', $tel);
};

while (have_posts()) : the_post();

    $post_id = get_the_ID();

    // Core
    $hero_image_id   = (int)get_post_thumbnail_id($post_id);
    $hero_subheading = (string)blaines_get_field('hero_subheading', '');
    $hero_address = (string) blaines_get_field( 'address', '' );

    // Build hero array for page-header component (it expects an array with ID/url/alt, ID is enough)
    $hero = null;
    if ($hero_image_id) {
        $hero = [
            'ID'  => $hero_image_id,
            'alt' => (string)get_post_meta($hero_image_id, '_wp_attachment_image_alt', true),
        ];
    }

    /**
     * Decide what to show as header intro.
     * - If you want the address prominent, use hero_address.
     * - If you prefer a softer intro, use hero_subheading.
     * - If you want both, combine them (shown below).
     */
    $header_intro_parts = array_values(array_filter([
        $hero_address ?: null,
        $hero_subheading ?: null,
    ]));

    $header_intro = '';
    if ( ! empty($header_intro_parts)) {
        // simple line breaks; page-header component can render as plain text
        $header_intro = implode("\n", $header_intro_parts);
    }

    // Other fields
    $storefront_image_id = (int)(blaines_get_field('storefront_image') ?: 0);

    $showcase_title = (string)blaines_get_field('showcase_title', '');
    $showcase_intro = (string)blaines_get_field('showcase_intro', '');

    $why_choose_title = (string)blaines_get_field('why_choose_title', '');
    $why_choose_items = blaines_get_field('why_choose_items', []);

    $main_content = blaines_get_field('showroom_main_content', '');

    $address         = (string)blaines_get_field('address', '');
    $phone           = (string)blaines_get_field('phone', '');
    $email           = (string)blaines_get_field('email', '');
    $opening_hours   = blaines_get_field('opening_hours', []);
    $parking_notes   = (string)blaines_get_field('parking_notes', '');
    $google_maps_url = (string)blaines_get_field('google_maps_url', '');

    $form_shortcode   = (string)blaines_get_field('contact_form_shortcode', '');
    $other_showrooms  = blaines_get_field('other_showrooms', []);
    $showcase_gallery = blaines_get_field('showcase_gallery', []);

    $consult_url = home_url('/design-consultation/');
    $tel_href    = $esc_tel($phone);

    /**
     * Header (shared component)
     * - Uses standard height: h-[300px] md:h-[400px]
     * - Handles breadcrumbs internally
     */
    get_template_part(
        'template-parts/components/page-header',
        null,
        [
            'hero'        => $hero,
            'title'       => get_the_title( $post_id ) . ' Showroom',
            'intro'       => wpautop( esc_html( $hero_address ) . ( $hero_subheading ? "\n\n" . esc_html( $hero_subheading ) : '' ) ),
            'intro_size' => 'text-lg md:text-xl',
            'breadcrumbs' => true,
            'align'       => 'center',
            'logo'        => null,
        ]
    );
    ?>

    <div class="container-wide py-6">

        <!-- Main layout grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- LEFT COLUMN (Main content) -->
            <div class="lg:col-span-6">
                <div class="prose-site prose-news max-w-none">

                    <?php
                    if (!empty($main_content)) {
                        echo wp_kses_post($main_content);
                    } else {
                        the_content();
                    }
                    ?>
                </div>

                <!-- Showroom showcase gallery -->
                <?php if (!empty($showcase_gallery) && is_array($showcase_gallery)) : ?>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($showcase_gallery as $img) :
                            $img_id = isset($img['ID']) ? (int) $img['ID'] : 0;
                            $full   = isset($img['url']) ? (string) $img['url'] : '';
                            $alt    = isset($img['alt']) ? (string) $img['alt'] : '';

                            if (!$img_id || !$full) continue;
                            ?>
                            <a
                                    class="group block overflow-hidden bg-white"
                                    href="<?php echo esc_url($full); ?>"
                                    data-fancybox="showroom-showcase"
                                    aria-label="<?php echo esc_attr($alt ?: 'Showroom image'); ?>"
                            >
                                <div class="aspect-[4/3] overflow-hidden">
                                    <?php
                                    echo wp_get_attachment_image(
                                        $img_id,
                                        'large',
                                        false,
                                        [
                                            'class'    => 'h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.02]',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                            'alt'      => $alt,
                                        ]
                                    );
                                    ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN (Showroom details) -->
            <div class="lg:col-span-6 space-y-8">

                <!-- Showroom details -->
                <div class="bg-white border border-gray-200 shadow-sm p-8 md:p-10">

                    <h2 class="text-2xl font-heading font-semibold tracking-tight text-gray-900">
                        Showroom Details </h2>

                    <div class="mt-6 space-y-3 text-base leading-relaxed font-body text-gray-700">

                        <?php if ($address) : ?>
                            <p>
                                <span class="font-semibold text-gray-900">Address</span><br>
                                <span class="font-extralight"><?php echo esc_html(str_replace("\n", ', ', $address)); ?></span>
                            </p>
                        <?php endif; ?>

                        <?php if ($phone) : ?>
                            <p>
                                <span class="font-semibold text-gray-900">Telephone</span><br>
                                <?php if ($tel_href) : ?>
                                    <a class="hover:underline" href="tel:<?php echo esc_attr($tel_href); ?>">
                                        <span class="font-extralight"><?php echo esc_html($phone); ?></span>
                                    </a>
                                <?php else : ?><?php echo esc_html($phone); ?><?php endif; ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <?php if ($google_maps_url) : ?>
                        <div class="mt-6">
                            <a class="inline-flex items-center justify-center px-6 py-3 text-sm font-body font-semibold border border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white transition"
                                    href="<?php echo esc_url($google_maps_url); ?>"
                                    target="_blank"
                                    rel="noopener"> View on Google Maps → </a>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty($opening_hours) && is_array($opening_hours)) : ?>

                        <hr class="my-8 border-gray-200">

                        <div class="space-y-2 text-sm font-body text-gray-700">

                            <p class="font-semibold text-gray-900">
                                Opening times </p>

                            <?php
                            foreach ($opening_hours as $row) :
                                $day = isset($row['day']) ? trim((string)$row['day']) : '';
                                $hrs = isset($row['hours']) ? trim((string)$row['hours']) : '';
                                if ( ! $day && ! $hrs) {
                                    continue;
                                }
                                ?>
                                <p>
                                    <?php echo esc_html($day); ?>
                                    <span class="font-extralight"><?php echo esc_html($hrs); ?></span>
                                </p>
                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

                <?php
                $forms = get_posts([
                    'post_type'      => 'wpforms',
                    'post_status'    => 'publish',
                    'title'          => 'Showroom Contact',
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                ]);

                $form_id = !empty($forms) ? (int) $forms[0] : 0;
                ?>

                <section id="contact" class="border border-gray-200 bg-white p-8 md:p-10">
                    <h2 class="text-2xl font-heading font-semibold tracking-tight text-gray-900">
                        Contact the <?php echo esc_html(get_the_title()); ?> showroom
                    </h2>

                    <p class="mt-3 font-body font-extralight text-sm leading-relaxed text-gray-600">
                        Our showroom team will be happy to help with product enquiries, appointments, and design consultations.
                    </p>

                    <?php if ($phone || $email) : ?>
                        <div class="mt-5 border-t border-gray-200 pt-5">
                            <div class="flex flex-col gap-2 text-sm font-body text-gray-700">
                                <?php if ($phone) : ?>
                                    <div>
                                        <span class="font-semibold text-gray-900">Call:</span>
                                        <?php if ($tel_href) : ?>
                                            <a class="hover:underline" href="tel:<?php echo esc_attr($tel_href); ?>">
                                                <?php echo esc_html($phone); ?>
                                            </a>
                                        <?php else : ?>
                                            <?php echo esc_html($phone); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($email) : ?>
                                    <div>
                                        <span class="font-semibold text-gray-900">Email:</span>
                                        <a class="hover:underline" href="mailto:<?php echo esc_attr($email); ?>">
                                            <?php echo esc_html($email); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6 blaines-wpforms">
                        <?php echo do_shortcode('[wpforms id="' . $form_id . '" title="false" description="false"]'); ?>
                    </div>
                </section>

            </div>
        </div>



    </div>

<?php endwhile; ?>

<?php
get_template_part(
    'template-parts/components/showroom-why-choose',
    null,
    [
        'title' => $why_choose_title ?: 'Our Showroom Experience',
        'items' => is_array($why_choose_items) ? $why_choose_items : [],
    ]
);
?>

<?php
get_template_part(
    'template-parts/components/showroom-visit-block',
    null,
    [
        'image_id' => $storefront_image_id, // or rename to $storefront_image_id if you prefer
        'title'    => 'Visiting Our '.get_the_title($post_id).' Showroom',
        'content'  => $parking_notes,
    ]
);
?>

<?php
get_template_part(
    'template-parts/components/showroom-other-showrooms-strip',
    null,
    [
        'title'      => 'Our other showrooms',
        'items'      => is_array($other_showrooms) ? $other_showrooms : [],
        'exclude_id' => (int)get_the_ID(),
        'limit'      => 3, // optional
    ]
);
?>

<?php get_template_part('template-parts/components/brand-logo-slider', null, [
    'variant' => 'interiors',
]); ?>

<?php get_footer(); ?>
