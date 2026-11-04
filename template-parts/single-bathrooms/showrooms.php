<?php
/**
 * Showrooms block for Single Kitchen
 * Styled to match the showroom strip used in page-showroom.php
 */

$showroom_displays = get_field('showroom_display');

if (! $showroom_displays || ! is_array($showroom_displays)) {
    return;
}

$showroom_cards = [];

foreach ($showroom_displays as $display) {
    $showroom_name = trim((string) $display);

    if ($showroom_name === '') {
        continue;
    }

    $slug = sanitize_title($showroom_name);
    $showroom_page = get_page_by_path('showrooms/' . $slug);

    if (! $showroom_page) {
        $showroom_cards[] = [
            'title' => ucwords($showroom_name),
            'url'   => site_url('/showrooms/' . $slug . '/'),
            'image' => 0,
            'text'  => '',
        ];
        continue;
    }

    $post_id  = $showroom_page->ID;
    $image_id = function_exists('get_field') ? (int) get_field('storefront_image', $post_id) : 0;
    $address  = function_exists('get_field') ? (string) get_field('address', $post_id) : '';
    $title    = get_the_title($post_id);

    $showroom_cards[] = [
        'title' => $title,
        'url'   => get_permalink($post_id),
        'image' => $image_id,
        'text'  => $address ? str_replace("\n", ', ', $address) : '',
    ];
}

if (empty($showroom_cards)) {
    return;
}
?>

<section class="bg-gray-50 py-10 lg:py-12">
    <div class="container-wide">

        <h2 class="mb-6 font-heading text-xl md:text-3xl font-medium tracking-wide text-gray-900">
            Visit this bathroom in our showrooms
        </h2>

        <p class="mb-8 max-w-3xl font-body text-base leading-7 text-gray-700">
            See this range in person, compare finishes and details up close, and speak to our team about planning your bathroom.
        </p>

        <div class="grid gap-6 xl:grid-cols-3">
            <?php foreach ($showroom_cards as $card) : ?>
                <a
                        href="<?php echo esc_url($card['url']); ?>"
                        class="group flex items-center gap-4 border border-gray-200 bg-white p-4 transition-shadow hover:shadow-sm"
                >
                    <?php if ($card['image']) : ?>
                        <div class="h-16 w-24 shrink-0 overflow-hidden bg-gray-200">
                            <?php
                            echo wp_get_attachment_image(
                                $card['image'],
                                'medium',
                                false,
                                [
                                    'class'    => 'h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]',
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                    'alt'      => $card['title'],
                                ]
                            );
                            ?>
                        </div>
                    <?php else : ?>
                        <div class="flex h-16 w-24 shrink-0 items-center justify-center bg-gray-200 text-[10px] uppercase tracking-[0.12em] text-gray-500">
                            Showroom
                        </div>
                    <?php endif; ?>

                    <div class="min-w-0">
                        <div class="font-body text-base font-semibold text-gray-900 group-hover:underline">
                            <?php echo esc_html($card['title']); ?>
                        </div>

                        <?php if ($card['text']) : ?>
                            <div class="mt-1 font-body text-sm leading-snug text-gray-600">
                                <?php echo esc_html($card['text']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-1 text-xs text-gray-500 transition-colors group-hover:text-brand">
                            View showroom →
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

