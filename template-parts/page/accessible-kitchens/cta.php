<?php
/**
 * Accessible Kitchens: CTA
 */

$eyebrow        = get_field( 'accessible_cta_eyebrow' );
$title          = get_field( 'accessible_cta_title' );
$text           = get_field( 'accessible_cta_text' );
$primary_text   = get_field( 'accessible_cta_primary_text' );
$primary_link   = get_field( 'accessible_cta_primary_link' );
$secondary_text = get_field( 'accessible_cta_secondary_text' );
$secondary_link = get_field( 'accessible_cta_secondary_link' );

if ( ! $eyebrow && ! $title && ! $text && ! $primary_link && ! $secondary_link ) {
    return;
}

$primary_url    = is_array( $primary_link ) ? ( $primary_link['url'] ?? '' ) : '';
$primary_target = is_array( $primary_link ) ? ( $primary_link['target'] ?? '_self' ) : '_self';

$secondary_url    = is_array( $secondary_link ) ? ( $secondary_link['url'] ?? '' ) : '';
$secondary_target = is_array( $secondary_link ) ? ( $secondary_link['target'] ?? '_self' ) : '_self';
?>

<section class="border-t border-neutral-200 bg-neutral-50 py-16 lg:py-20" aria-labelledby="accessible-cta-heading">
    <div class="prose-site mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-8">
                <?php if ( $eyebrow ) : ?>
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--freedom-accent)]">
                        <?php echo esc_html( $eyebrow ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $title ) : ?>
                    <h2 id="accessible-cta-heading" class="mt-3 max-w-4xl text-3xl tracking-wide text-neutral-900 sm:text-4xl">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $text ) : ?>
                    <div class="mt-5 max-w-3xl leading-7 text-neutral-700">
                        <?php echo wp_kses_post( wpautop( $text ) ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-4">
                <div class="flex h-full flex-col justify-center gap-4 lg:items-end">
                    <?php if ( $primary_url ) : ?>
                        <a
                                href="<?php echo esc_url( $primary_url ); ?>"
                                target="<?php echo esc_attr( $primary_target ); ?>"
                                class="inline-flex min-h-[52px] w-full items-center justify-center bg-[var(--freedom-accent)] px-6 py-3 text-center text-sm font-body font-medium
                                text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--freedom-accent)] focus:ring-offset-2 lg:max-w-[280px]"
                        >
                            <?php echo esc_html( $primary_text ?: 'Book a Design Consultation' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $secondary_url ) : ?>
                        <a
                                href="<?php echo esc_url( $secondary_url ); ?>"
                                target="<?php echo esc_attr( $secondary_target ); ?>"
                                class="inline-flex min-h-[52px] w-full items-center justify-center border border-neutral-300 bg-white px-6 py-3 text-center text-sm font-body
                                font-medium text-neutral-900 transition hover:border-neutral-900 focus:outline-none focus:ring-2 focus:ring-[var(--freedom-accent)] focus:ring-offset-2 lg:max-w-[280px]"
                        >
                            <?php echo esc_html( $secondary_text ?: 'Visit Our Showrooms' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>