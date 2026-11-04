<?php
/**
 * Showroom: Why Choose (4-up)
 * Args:
 *  - title (string)
 *  - items (array)  // repeater rows: image, label, body
 */

$title = isset($args['title']) ? trim((string) $args['title']) : '';
$items = isset($args['items']) && is_array($args['items']) ? $args['items'] : [];

if ($title === '' && empty($items)) return;

// Filter out empty rows
$items = array_values(array_filter($items, static function ($item) {
    if (!is_array($item)) return false;
    $label = trim((string)($item['label'] ?? ''));
    $body  = trim((string)($item['body'] ?? ''));
    $img   = $item['image'] ?? null;
    return ($label !== '' || $body !== '' || !empty($img));
}));

if ($title === '' && empty($items)) return;
?>

<section class="w-full bg-gray-50 py-14 md:py-16">
    <div class="container-wide">

        <?php if ($title !== ''): ?>
            <header class="mb-10 md:mb-12">
                <p class="text-xs tracking-[0.18em] uppercase text-gray-500 font-body">Service</p>
                <h2 class="mt-2 font-heading text-3xl font-medium tracking-[-0.02em] text-gray-900 md:text-4xl">
                    <?php echo esc_html($title); ?>
                </h2>
            </header>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <div class="grid gap-8 md:gap-10 sm:grid-cols-2 lg:grid-cols-4">

                <?php foreach ($items as $item): ?>
                    <?php
                    $label = trim((string)($item['label'] ?? ''));
                    $body  = trim((string)($item['body'] ?? ''));

                    // Support image as ID or array
                    $img = $item['image'] ?? null;
                    $img_id = 0;
                    $img_alt = '';

                    if (is_numeric($img)) {
                        $img_id = (int) $img;
                        $img_alt = (string) get_post_meta($img_id, '_wp_attachment_image_alt', true);
                    } elseif (is_array($img)) {
                        $img_id = (int) ($img['ID'] ?? 0);
                        $img_alt = (string) ($img['alt'] ?? '');
                    }
                    ?>

                    <article>
                        <?php if ($img_id): ?>
                            <div class="mb-4">
                                <?php
                                echo wp_get_attachment_image(
                                    $img_id,
                                    'large',
                                    false,
                                    [
                                        'class'    => 'w-full h-auto object-cover',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                        'alt'      => $img_alt,
                                    ]
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($label !== ''): ?>
                            <h3 class="font-heading text-xl font-medium tracking-[-0.02em] text-gray-900 md:text-2xl">
                                <?php echo esc_html($label); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ($body !== ''): ?>
                            <div class="mt-3 font-body font-light text-sm leading-[1.65] text-gray-600 md:text-base">
                                <?php echo esc_html($body); ?>
                            </div>
                        <?php endif; ?>
                    </article>

                <?php endforeach; ?>

            </div>
        <?php endif; ?>

    </div>
</section>
