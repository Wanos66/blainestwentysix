<?php
/* -------------------------------------------------------------------------- */
/* File: template-parts/page/accessible-kitchens/faqs.php                     */
/* -------------------------------------------------------------------------- */
?>

<section class="border-t border-neutral-200 py-12 lg:py-16">
    <div class="prose-site mx-auto w-full max-w-5xl px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-neutral-500">Questions</p>
            <h2 class="mt-3 text-3xl tracking-wide text-neutral-900 lg:text-[2.2rem]">
                Accessible Kitchens FAQ
            </h2>
        </div>

        <?php
        $faqs = array(
            array(
                'question' => 'What makes a kitchen more accessible?',
                'answer'   => 'An accessible kitchen is designed to make movement, storage, preparation, and appliance use easier and more comfortable. This can include layout planning, adjusted worktop heights, improved storage access, better lighting, and more practical appliance positioning.',
            ),
            array(
                'question' => 'Can an accessible kitchen still look stylish?',
                'answer'   => 'Yes. Accessible design does not mean compromising on quality or appearance. A well-designed kitchen can feel elegant, contemporary, and highly practical at the same time.',
            ),
            array(
                'question' => 'Do you design kitchens for wheelchair users?',
                'answer'   => 'We can help plan kitchens with wheelchair access and ease of movement in mind, including layout considerations, circulation space, and more practical positioning of cabinets and appliances.',
            ),
            array(
                'question' => 'Can appliances be fitted at easier heights?',
                'answer'   => 'Yes. In many cases, appliances can be positioned at more convenient heights to make loading, unloading, and everyday use easier.',
            ),
            array(
                'question' => 'Can you help future-proof a kitchen?',
                'answer'   => 'Yes. Many clients choose accessible design features as part of a longer-term plan, helping their kitchen remain comfortable and practical for years to come.',
            ),
            array(
                'question' => 'Do you offer home visits?',
                'answer'   => 'Where needed, home visits can help us better understand the room layout, access considerations, and the most suitable design options for your space.',
            ),
        );
        ?>

        <div class="mt-10 divide-y divide-neutral-200 border-y border-neutral-200">
            <?php foreach ( $faqs as $faq ) : ?>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-start justify-between gap-6 text-left">
                        <span class="text-lg text-neutral-900"><?php echo esc_html( $faq['question'] ); ?></span>
                        <span class="mt-1 text-neutral-500 transition group-open:rotate-45">+</span>
                    </summary>
                    <div class="mt-4 max-w-3xl text-sm leading-6 text-neutral-700">
                        <p><?php echo esc_html( $faq['answer'] ); ?></p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

