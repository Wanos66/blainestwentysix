<?php
/**
 * My Account page.
 *
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="w-full">
    <div class="grid items-start gap-6 lg:grid-cols-[270px_minmax(0,1fr)] lg:gap-8 xl:gap-10">

        <?php
        /**
         * My Account navigation.
         *
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_navigation' );
        ?>

        <div
            class="woocommerce-MyAccount-content !float-none !w-full min-w-0 border border-neutral-200 bg-white p-5 shadow-[0_10px_30px_rgba(0,0,0,0.06)] sm:p-7 lg:p-8
            [&_h2]:mb-5 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-neutral-950
            [&_h3]:mb-4 [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:text-neutral-950
            [&_p]:leading-relaxed [&_p]:text-neutral-700
            [&_form_.form-row]:mb-4
            [&_form_label]:mb-2 [&_form_label]:block [&_form_label]:text-sm [&_form_label]:font-medium [&_form_label]:text-neutral-800
            [&_input.input-text]:min-h-12 [&_input.input-text]:w-full [&_input.input-text]:border [&_input.input-text]:border-neutral-300 [&_input.input-text]:bg-white [&_input.input-text]:px-4 [&_input.input-text]:py-2.5 [&_input.input-text]:text-neutral-950 [&_input.input-text]:outline-none focus:[&_input.input-text]:border-blue-700
            [&_select]:min-h-12 [&_select]:w-full [&_select]:border [&_select]:border-neutral-300 [&_select]:bg-white [&_select]:px-4
            [&_textarea]:w-full [&_textarea]:border [&_textarea]:border-neutral-300 [&_textarea]:bg-white [&_textarea]:px-4 [&_textarea]:py-3
            [&_.button]:inline-flex! [&_.button]:h-11! [&_.button]:items-center! [&_.button]:justify-center! [&_.button]:rounded-none! [&_.button]:border-0! [&_.button]:bg-neutral-950! [&_.button]:px-4! [&_.button]:py-0! [&_.button]:text-sm! [&_.button]:font-semibold! [&_.button]:leading-none! [&_.button]:text-white! [&_.button]:no-underline! [&_.button]:transition [&_.button:hover]:bg-blue-700!
            [&_table]:w-full [&_table]:border-collapse
            [&_th]:border-b [&_th]:border-neutral-200 [&_th]:px-3 [&_th]:py-3 [&_th]:text-left [&_th]:text-sm [&_th]:font-semibold [&_th]:text-neutral-950
            [&_td]:border-b [&_td]:border-neutral-200 [&_td]:px-3 [&_td]:py-3 [&_td]:align-top [&_td]:text-sm [&_td]:text-neutral-700
            [&_address]:not-italic [&_address]:leading-relaxed [&_address]:text-neutral-700
            [&_a]:font-medium [&_a]:text-blue-700 [&_a:hover]:text-neutral-950"
        >
            <?php
            /**
             * My Account content.
             *
             * @since 2.6.0
             */
            do_action( 'woocommerce_account_content' );
            ?>
        </div>

    </div>
</section>
