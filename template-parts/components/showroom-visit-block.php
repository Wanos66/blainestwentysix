<?php
/**
 * Showroom Visit Block
 * Args:
 *  - image_id (int)
 *  - title (string)
 *  - content (string)  // parking notes / visit copy
 */

$image_id = isset($args['image_id']) ? (int) $args['image_id'] : 0;
$title    = isset($args['title']) ? trim((string) $args['title']) : '';
$content  = isset($args['content']) ? (string) $args['content'] : '';

if (!$image_id && trim($content) === '') return;

// If $content is plain text (textarea), this will give you paragraphs.
// If it’s WYSIWYG, wpautop is usually fine too; wp_kses_post protects output.
$rendered = trim($content) !== '' ? wp_kses_post(wpautop($content)) : '';
?>

<section class="py-10 md:py-14">
    <div class="container-wide">
        <div class="grid items-start gap-10 lg:grid-cols-12">

            <!-- Image -->
            <div class="lg:col-span-6">
                <?php if ($image_id): ?>
                    <?php
                    echo wp_get_attachment_image(
                        $image_id,
                        'large',
                        false,
                        [
                            'class'    => 'w-full h-auto object-cover',
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                        ]
                    );
                    ?>
                <?php endif; ?>
            </div>

            <!-- Copy -->
            <div class="lg:col-span-6 prose-site">
                <?php if ($title !== ''): ?>
                    <h2 class="font-heading text-2xl md:text-3xl font-medium tracking-tight text-gray-900">
                        <?php echo esc_html($title); ?>
                    </h2>
                <?php endif; ?>

                <?php if ($rendered !== ''): ?>
                    <div class="prose-site prose-news mt-3 max-w-none">
                        <?php echo $rendered; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
