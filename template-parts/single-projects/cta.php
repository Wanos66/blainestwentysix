<?php
/**
 * Single Project — CTA (under Project Details)
 */

// Optional: if you want this configurable later via Options/ACF, we can wire that in.
// For now, keep it simple and hard-coded (consistent + fast).

$title = 'Planning something similar?';
$text  = 'Visit one of our showrooms for expert advice on kitchens, appliances, TV and audio — and book a design consultation when you’re ready.';
$btn_text = 'Book a consultation';
$btn_url  = '/design-consultation/';
?>

<section class="mt-10 border border-black/10 bg-white p-6">
    <h3 class="mb-3 font-body text-lg font-medium tracking-[-0.02em] text-black">
        <?php echo esc_html( $title ); ?>
    </h3>

    <p class="mb-5 font-body text-sm leading-relaxed text-black/70">
        <?php echo esc_html( $text ); ?>
    </p>

    <a href="<?php echo esc_url( $btn_url ); ?>"
            class="inline-flex w-full items-center justify-center bg-black px-4 py-3 font-body text-sm font-medium text-white transition hover:bg-black/90">
        <?php echo esc_html( $btn_text ); ?>
    </a>

    <div class="mt-4 font-body text-xs text-black/50">
        Prefer to browse first? <a class="font-body underline underline-offset-4 hover:text-black" href="/showrooms/">Find a showroom</a>
    </div>
</section>
