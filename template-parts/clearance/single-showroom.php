<?php
$showroom = $args['showroom'] ?? null;

if ( ! ( $showroom instanceof WP_Post ) ) {
    return;
}

$showroom_phone = function_exists( 'get_field' ) ? get_field( 'phone', $showroom->ID ) : '';
$showroom_phone = is_string( $showroom_phone ) ? trim( $showroom_phone ) : '';
?>

<div class="border border-neutral-200 bg-white p-6">
    <p class="font-body text-sm font-semibold uppercase tracking-[0.08em] text-neutral-500">
        Available at
    </p>

    <h2 class="mt-2 font-body text-xl font-medium tracking-[-0.02em] text-neutral-900">
        <?php echo esc_html( get_the_title( $showroom->ID ) ); ?>
    </h2>

    <?php if ( $showroom_phone ) : ?>
        <a
                href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $showroom_phone ) ); ?>"
                class="font-body text-sm font-medium text-neutral-600 hover:text-neutral-900 hover:underline"
        >
            <?php echo esc_html( $showroom_phone ); ?>
        </a>
    <?php endif; ?>

    <p class="mt-3 font-body text-sm leading-relaxed text-neutral-600">
        Contact the showroom directly to confirm availability or arrange a viewing.
    </p>

    <div class="mt-4 space-y-3">
        <a
                href="<?php echo esc_url( get_permalink( $showroom->ID ) ); ?>"
                class="inline-flex w-full items-center justify-center border border-neutral-900 px-4 py-3 font-body text-sm font-semibold text-neutral-900 transition hover:bg-neutral-100"
        >
            View showroom
        </a>

        <?php
        $product_id   = get_the_ID();
        $product_obj  = wc_get_product( $product_id );
        $product_name = get_the_title( $product_id );
        $product_code = $product_obj ? $product_obj->get_sku() : '';

        $prefill_message = $product_code
            ? sprintf(
                'I’m interested in the following clearance product: %1$s (SKU: %2$s)',
                $product_name,
                $product_code
            )
            : sprintf(
                'I’m interested in the following clearance product: %s',
                $product_name
            );

        // Match the form lookup in templates/page-showroom.php, so the form
        // targeted by the Clearance URL is the one displayed on the showroom page.
        $showroom_forms = get_posts( [
            'post_type'      => 'wpforms',
            'post_status'    => 'publish',
            'title'          => 'Showroom Contact',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ] );
        $form_id = ! empty( $showroom_forms ) ? (int) $showroom_forms[0] : 0;

        // WPForms field IDs differ between Local and Staging/Live.
        $prefill_fields = [
            1076   => [ 'message' => 3, 'product' => 6 ], // Local
            284870 => [ 'message' => 3, 'product' => 5 ], // Staging / Live
        ];

        $message_url = get_permalink( $showroom->ID ) . '#contact';

        if ( isset( $prefill_fields[ $form_id ] ) ) {
            $field_ids = $prefill_fields[ $form_id ];
            $message_url = add_query_arg(
                [
                    'wpf' . $form_id . '_' . $field_ids['product'] => $product_name,
                    'wpf' . $form_id . '_' . $field_ids['message'] => $prefill_message,
                ],
                get_permalink( $showroom->ID )
            ) . '#contact';
        }
        ?>

        <a
                href="<?php echo esc_url( $message_url ); ?>"
                class="inline-flex w-full items-center justify-center bg-neutral-900 px-4 py-3 font-body text-sm font-semibold text-white transition hover:bg-neutral-800"
        >
            Message showroom
        </a>
    </div>
</div>

