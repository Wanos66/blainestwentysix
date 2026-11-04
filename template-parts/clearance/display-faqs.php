<?php
/**
 * Ex-display product FAQs.
 *
 * Shared by Kitchen Clearance and Bathroom Clearance single product templates.
 */

defined( 'ABSPATH' ) || exit;

$display_type = $args['display_type'] ?? 'display';

$item_label = match ( $display_type ) {
    'kitchen'  => 'kitchen',
    'bathroom' => 'bathroom',
    default    => 'display',
};

$item_label_cap = ucfirst( $item_label );
?>

<section class="mt-12 border-t border-neutral-200 pt-10 lg:mt-16 lg:pt-12">
    <div class="max-w-3xl">
        <p class="font-body text-sm uppercase tracking-[0.12em] text-neutral-500">
            Buying an ex-showroom <?php echo esc_html( $item_label ); ?>
        </p>

        <h2 class="mt-3 font-heading text-2xl font-light text-neutral-950 md:text-3xl">
            Frequently asked questions
        </h2>

        <div class="mt-7 divide-y divide-neutral-200 border-y border-neutral-200">

            <details class="group py-5" open>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <h3 class="font-body text-base font-semibold text-neutral-950">
                        What does ex-showroom mean?
                    </h3>

                    <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                        ↓
                    </span>
                </summary>

                <div class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                    <p>
                        Ex-showroom means this <?php echo esc_html( $item_label ); ?> has been used as a display in one of our showrooms. It may include signs of showroom use, but it is offered at a reduced price compared with its original display value.
                    </p>
                </div>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <h3 class="font-body text-base font-semibold text-neutral-950">
                        Can I view it before buying?
                    </h3>

                    <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                        ↓
                    </span>
                </summary>

                <div class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                    <p>
                        Yes. We recommend visiting the showroom where the display is located so you can see the <?php echo esc_html( $item_label ); ?> in person and speak to our team before making a decision.
                    </p>
                </div>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <h3 class="font-body text-base font-semibold text-neutral-950">
                        What is included in the price?
                    </h3>

                    <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                        ↓
                    </span>
                </summary>

                <div class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                    <p>
                        Included items can vary from display to display. Please check the specification above and contact the showroom if you need anything clarified before arranging purchase or removal.
                    </p>
                </div>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <h3 class="font-body text-base font-semibold text-neutral-950">
                        Is delivery or installation included?
                    </h3>

                    <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                        ↓
                    </span>
                </summary>

                <div class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                    <p>
                        Delivery, removal and installation are not automatically included unless stated. Our showroom team can explain what options may be available for this particular <?php echo esc_html( $item_label ); ?>.
                    </p>
                </div>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                    <h3 class="font-body text-base font-semibold text-neutral-950">
                        Are ex-showroom displays sold as seen?
                    </h3>

                    <span class="text-sm text-neutral-500 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">
                        &darr;
                    </span>
                </summary>

                <div class="mt-3 font-body text-sm leading-relaxed text-neutral-700">
                    <p>
                        In most cases, ex-showroom displays are sold as seen. We recommend viewing the display and confirming all details with the showroom before proceeding.
                    </p>
                </div>
            </details>

        </div>
    </div>
</section>
