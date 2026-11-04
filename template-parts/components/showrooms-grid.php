<?php
/**
 * Showrooms Grid (ACF-driven)
 */

if (!function_exists('get_field')) return;

$cards = get_field('showrooms_cards');
if (!is_array($cards) || empty($cards)) return;
?>

<section class="bg-gray-50 py-16">
    <div class="container-wide">
        <div class="grid gap-8 md:grid-cols-3">
            <?php foreach ($cards as $card): ?>
                <?php
                $image = $card['image'] ?? null;
                $title = trim((string)($card['title'] ?? ''));
                $text  = trim((string)($card['text'] ?? ''));
                $link  = $card['link'] ?? null;
                $btn   = trim((string)($card['button_text'] ?? 'Store details'));

                if ($title === '' || !is_array($link) || empty($link['url'])) {
                    continue;
                }
                ?>
                <article class="flex h-full flex-col bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                    <?php if (is_array($image) && !empty($image['ID'])): ?>
                        <?php
                        echo wp_get_attachment_image(
                            (int)$image['ID'],
                            'large',
                            false,
                            [
                                'class' => 'w-full h-auto block',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            ]
                        );
                        ?>
                    <?php endif; ?>

                    <div class="flex flex-1 flex-col p-8 text-center">
                        <h3 class="mb-3 font-heading text-xl font-semibold tracking-[-0.02em] text-gray-800">
                            <?php echo esc_html($title); ?>
                        </h3>

                        <div class="border-t border-slate-700 mb-4"></div>

                        <?php if ($text !== ''): ?>
                            <p class="flex-1 font-body font-light text-gray-600 leading-relaxed">
                                <?php echo esc_html($text); ?>
                            </p>
                        <?php endif; ?>

                        <div class="mt-auto pt-6">
                            <a
                                    class="inline-flex items-center justify-center px-4 py-2 font-body text-sm font-semibold text-white bg-slate-800 border-2 border-slate-800 transition hover:bg-white hover:text-brand md:text-base"
                                href="<?php echo esc_url($link['url']); ?>"
                                target="<?php echo esc_attr($link['target'] ?? '_self'); ?>"
                                rel="<?php echo ($link['target'] ?? '') === '_blank' ? 'noopener noreferrer' : ''; ?>"
                            >
                                <?php echo esc_html($btn); ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>