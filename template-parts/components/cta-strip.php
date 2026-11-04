<?php
/**
 * CTA Strip (transition band)
 *
 * Args:
 * - title (string)
 * - text (string)
 * - button (array) { url, label, target? }
 * - tone (string) 'light' | 'dark' (default light)
 */

$title  = $args['title'] ?? 'Need help choosing?';
$text   = $args['text'] ?? 'Book a free design appointment or visit a showroom to see options in person.';
$button = $args['button'] ?? [];
$tone   = $args['tone'] ?? 'light';

$btn_url    = $button['url'] ?? '';
$btn_label  = $button['label'] ?? 'Book a free design appointment';
$btn_target = $button['target'] ?? '';

$wrap_classes = $tone === 'dark'
    ? 'bg-black text-white border border-white/10 shadow-[0_8px_24px_rgba(0,0,0,0.25)]'
    : 'bg-white text-neutral-900 border border-neutral-200 shadow-[0_8px_24px_rgba(0,0,0,0.06)]';

$text_classes = $tone === 'dark' ? 'text-white/80' : 'text-neutral-600';

$btn_classes = $tone === 'dark'
    ? 'bg-white text-black hover:bg-white/90'
    : 'bg-neutral-900 text-white hover:bg-neutral-800';
?>

<section class="my-12 lg:my-16 <?php echo esc_attr( $wrap_classes ); ?>">
    <div class="container-wide py-10 md:py-12">
        <div class="grid gap-6 md:grid-cols-12 md:items-center">

            <div class="md:col-span-8">
                <?php if ( $title ) : ?>
                    <h2 class="font-heading text-2xl font-medium text-slate-900 sm:text-3xl">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $text ) : ?>
                    <p class="mt-2 text-sm md:text-base leading-relaxed <?php echo esc_attr( $text_classes ); ?>">
                        <?php echo esc_html( $text ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="md:col-span-4 md:flex md:justify-end">
                <?php if ( $btn_url ) : ?>
                    <a
                        href="<?php echo esc_url( $btn_url ); ?>"
                        <?php echo $btn_target ? 'target="'.esc_attr($btn_target).'" rel="noopener"' : ''; ?>
                        class="inline-flex w-full md:w-auto items-center justify-center px-6 py-3 text-sm font-body font-semibold border border-transparent transition <?php echo
                        esc_attr( $btn_classes ); ?>"
                    >
                        <?php echo esc_html( $btn_label ); ?>
                        <span class="ml-2" aria-hidden="true">→</span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
