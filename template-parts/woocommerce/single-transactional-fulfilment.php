<?php
/**
 * Transactional product fulfilment and payment reassurance panel.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
    return;
}
?>

<div class="bg-white p-6 lg:p-7">
    <h2 class="text-xl font-light text-neutral-950">
        Delivery & services
    </h2>

    <div class="mt-5 space-y-4 text-sm text-neutral-700">

        <div class="border-l-2 border-neutral-300 pl-4">
            <p class="font-medium text-neutral-950">
                Local delivery available
            </p>
            <p class="mt-1 text-neutral-600">
                Available for local delivery within the Blaines service area.
            </p>
        </div>

        <div class="border-l-2 border-neutral-300 pl-4">
            <p class="font-medium text-neutral-950">
                Collection available
            </p>
            <p class="mt-1 text-neutral-600">
                Choose collection at checkout where available.
            </p>
        </div>

        <div class="border-l-2 border-neutral-300 pl-4">
            <p class="font-medium text-neutral-950">
                Installation options
            </p>
            <p class="mt-1 text-neutral-600">
                Installation may be available on selected appliances. Please contact Web Sales for advice.
            </p>
        </div>

    </div>
</div>
