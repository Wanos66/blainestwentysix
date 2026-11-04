<?php
/**
 * Template Name: Interiors Page
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $hero_image_id = (int) get_post_thumbnail_id();
        $hero = null;
        if ($hero_image_id) {
            $hero = [
                'ID'  => $hero_image_id,
                'alt' => (string)get_post_meta($hero_image_id, '_wp_attachment_image_alt', true),
            ];
        }
        ?>

        <?php
        get_template_part(
            'template-parts/components/page-header',
            null,
            [
                'hero'        => $hero,
                'title'       => 'Interiors by Blaines',
                'intro'       => 'Kitchens, bathrooms, bedrooms and fitted interiors — now brought together as part of the main Blaines website.',
                'breadcrumbs' => true,
                'hero_style'  => 'editorial',
                'panel_side'  => 'right',
            ]
        );
        ?>

        <!-- A NEW HOME SECTION -->
        <section class="border-t border-neutral-200 bg-white">
            <div class="prose-site mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        A new home for Blaines Interiors
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        If you’ve arrived from <span class="text-[#6F857C]">blainesinteriors.co.uk</span>, you’re in the right place.
                    </h2>

                    <p class="mt-6 max-w-3xl font-body text-base font-light leading-7 text-neutral-700">
                        Our Interiors service now sits within the main Blaines website, making it easier to explore
                        our wider offering while still benefiting from the same thoughtful design approach, practical
                        expertise and personal guidance.
                    </p>
                </div>
            </div>
        </section>

        <!-- INTERIORS SECTION -->
        <section class="bg-neutral-50">
            <div class="prose-site mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-12 lg:items-start lg:px-8">
                <div class="lg:col-span-7">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Interiors
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        Designed around the way you live.
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        At Blaines, interiors means more than individual products. It means carefully planned spaces
                        that feel right to use, day after day. From fitted kitchens, bathrooms and bedrooms to
                        storage, finishes and integrated appliances, our approach combines practical design,
                        lasting quality and a clear understanding of how each home needs to work.
                    </p>

                    <div class="mt-8 border-l border-[#B8C8BF] pl-5">
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-[#6F857C]">
                            What we offer
                        </p>

                        <ul class="mt-4 space-y-3 font-body text-sm font-light leading-6 text-neutral-700">
                            <li>Fitted kitchens</li>
                            <li>Bathrooms and bathroom design</li>
                            <li>Bedrooms and storage</li>
                            <li>Integrated appliances</li>
                            <li>Tailored design guidance</li>
                            <li>Showroom-led service</li>
                        </ul>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="overflow-hidden border border-neutral-200 bg-white">
                        <div class="aspect-[4/5] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Harwood-Farnham-House.webp' ) ); ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- EXPLORE SECTION -->
        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8 prose-site">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Explore
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        Explore our interiors services
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        Explore kitchens, bathrooms, bedrooms and consultation services designed to help you
                        shape spaces that feel considered, practical and beautifully resolved.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2">
                    <article class="group overflow-hidden border border-neutral-200 bg-white transition-colors duration-200 hover:border-neutral-300">
                        <div class="aspect-[24/7] bg-neutral-200 overflow-hidden">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Langdale-Fern-Green-2560.webp' ) ); ?>"
                                    alt="Kitchen interiors"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                    loading="lazy"
                            >
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="font-heading text-[1.35rem] tracking-wide leading-tight text-neutral-900">
                                    Kitchens
                                </h3>
<!--                                <span class="text-[10px] font-medium uppercase tracking-[0.16em] text-[#9AA9A1]">-->
<!--                            01-->
<!--                        </span>-->
                            </div>

                            <p class="mt-4 font-body text-sm font-light leading-6 text-neutral-700">
                                Discover fitted kitchens shaped around the way you cook, gather and live every day.
                            </p>

                            <div class="mt-5">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900 underline-offset-4 hover:underline" href="<?php echo esc_url( home_url( '/kitchens/' ) ); ?>">
                                    Explore kitchens
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="group overflow-hidden border border-neutral-200 bg-white transition-colors duration-200 hover:border-neutral-300">
                        <div class="aspect-[24/7] bg-neutral-200 overflow-hidden">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Burford-Matt-Coniston-Blue-2400x818-1.webp' ) ); ?>"
                                    alt="Bathroom interiors"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                    loading="lazy"
                            >
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="font-heading text-[1.35rem] tracking-wide leading-tight text-neutral-900">
                                    Bathrooms
                                </h3>
<!--                                <span class="text-[10px] font-medium uppercase tracking-[0.16em] text-[#9AA9A1]">-->
<!--                            02-->
<!--                        </span>-->
                            </div>

                            <p class="mt-4 font-body text-sm font-light leading-6 text-neutral-700">
                                Explore bathroom spaces designed for comfort, calm and lasting everyday practicality.
                            </p>

                            <div class="mt-5">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900 underline-offset-4 hover:underline" href="<?php echo esc_url( home_url( '/bathrooms/' ) ); ?>">
                                    View bathrooms
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="group overflow-hidden border border-neutral-200 bg-white transition-colors duration-200 hover:border-neutral-300">
                        <div class="aspect-[24/7] bg-neutral-200 overflow-hidden">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Marston-Cobble-Grey-2400x8182-1.webp' ) ); ?>"
                                    alt="Bedroom interiors"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                    loading="lazy"
                            >
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="font-heading text-[1.35rem] tracking-wide leading-tight text-neutral-900">
                                    Bedrooms
                                </h3>
<!--                                <span class="text-[10px] font-medium uppercase tracking-[0.16em] text-[#9AA9A1]">-->
<!--                            03-->
<!--                        </span>-->
                            </div>

                            <p class="mt-4 font-body text-sm font-light leading-6 text-neutral-700">
                                Explore fitted bedroom furniture and storage designed for calm, balanced living.
                            </p>

                            <div class="mt-5">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900 underline-offset-4 hover:underline" href="<?php echo esc_url( home_url( '/bedrooms/' ) ); ?>">
                                    View bedrooms
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="group overflow-hidden border border-neutral-200 bg-white transition-colors duration-200 hover:border-neutral-300">
                        <div class="aspect-[24/7] bg-neutral-200 overflow-hidden">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/ant-and-sheila-2400x818-1.webp' ) ); ?>"
                                    alt="Design consultation"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                    loading="lazy"
                            >
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="font-heading text-[1.35rem] tracking-wide leading-tight text-neutral-900">
                                    Design Consultation
                                </h3>
<!--                                <span class="text-[10px] font-medium uppercase tracking-[0.16em] text-[#9AA9A1]">-->
<!--                            04-->
<!--                        </span>-->
                            </div>

                            <p class="mt-4 font-body text-sm font-light leading-6 text-neutral-700">
                                Begin your project with tailored advice, clearer planning and support from the outset.
                            </p>

                            <div class="mt-5">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900 underline-offset-4 hover:underline" href="<?php echo esc_url( home_url( '/design-consultation/' ) ); ?>">
                                    Book a consultation
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- WHY BLAINES INTERIORS SECTION -->
        <section class="bg-[#F3F6F3]">
            <div class="prose-site mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Why Blaines Interiors
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        A more considered approach to interiors
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        From first ideas to final finishes, we help bring kitchens, bathrooms and bedrooms together
                        with thoughtful design, practical planning and personal guidance.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <article class="border border-neutral-200 bg-white p-8">
                        <h3 class="font-heading text-xl tracking-wide text-neutral-900">Thoughtful design</h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            Spaces planned around everyday living, not just appearance.
                        </p>
                    </article>

                    <article class="border border-neutral-200 bg-white p-8">
                        <h3 class="font-heading text-xl tracking-wide text-neutral-900">Joined-up expertise</h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            Kitchens, bathrooms and bedrooms considered as part of the wider home.
                        </p>
                    </article>

                    <article class="border border-neutral-200 bg-white p-8">
                        <h3 class="font-heading text-xl tracking-wide text-neutral-900">Showroom guidance</h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            Explore finishes, compare ideas and speak to our team in person.
                        </p>
                    </article>

                    <article class="border border-neutral-200 bg-white p-8">
                        <h3 class="font-heading text-xl tracking-wide text-neutral-900">Practical confidence</h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            Clear advice, careful planning and a service-led process throughout.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- PROCESS SECTION -->
        <section class="bg-neutral-50">
            <div class="prose-site mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Process
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        How we work
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        From first ideas to final decisions, our approach is centred on thoughtful design,
                        practical planning and personal service.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div class="border border-[#B8C8BF] bg-white p-8">
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">
                            01
                        </p>
                        <h3 class="mt-3 font-heading text-xl tracking-wide text-neutral-900">
                            Discover
                        </h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            We start by understanding your space, priorities and day-to-day needs.
                        </p>
                    </div>

                    <div class="border border-[#B8C8BF] bg-white p-8">
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">
                            02
                        </p>
                        <h3 class="mt-3 font-heading text-xl tracking-wide text-neutral-900">
                            Design
                        </h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            We shape a scheme around layout, function, finishes and overall feel.
                        </p>
                    </div>

                    <div class="border border-[#B8C8BF] bg-white p-8">
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">
                            03
                        </p>
                        <h3 class="mt-3 font-heading text-xl tracking-wide text-neutral-900">
                            Refine
                        </h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            Details are reviewed carefully so the final result feels both practical and considered.
                        </p>
                    </div>

                    <div class="border border-[#B8C8BF] bg-white p-8">
                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">
                            04
                        </p>
                        <h3 class="mt-3 font-heading text-xl tracking-wide text-neutral-900">
                            Deliver
                        </h3>
                        <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                            From first ideas to final decisions, we guide the project with clarity and care.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- INSPIRATION SECTION -->
        <section class="bg-white">
            <div class="prose-site mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Inspiration
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        Interior inspiration
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        Explore selected spaces and ideas that reflect our approach to thoughtful design,
                        practical planning and quality of finish.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-12">
                    <article class="overflow-hidden border border-neutral-200 bg-white lg:col-span-7">
                        <div class="aspect-[16/10] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Bright-accessible-5.jpg' ) ); ?>"
                                    alt="Kitchen inspiration"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>

                        <div class="p-8">
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-[#6F857C]">
                                Featured inspiration
                            </p>

                            <h3 class="mt-3 font-heading text-2xl tracking-wide text-neutral-900">
                                Kitchens
                            </h3>

                            <p class="mt-4 max-w-2xl font-body text-sm font-light leading-6 text-neutral-700">
                                Explore kitchen inspiration shaped around layout, flow, finishes and everyday living.
                            </p>

                            <div class="mt-6">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/kitchens/' ) ); ?>">
                                    Explore kitchens
                                </a>
                            </div>
                        </div>
                    </article>

                    <div class="flex flex-col gap-6 lg:col-span-5">
                        <article class="overflow-hidden border border-neutral-200 bg-white">
                            <div class="grid md:grid-cols-[40%_1fr]">
                                <div class="aspect-[4/3] bg-neutral-200 md:aspect-auto">
                                    <img
                                            src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Image-5-1.jpg' ) ); ?>"
                                            alt="Bathroom inspiration"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                    >
                                </div>

                                <div class="p-6">
                                    <h3 class="font-heading text-xl tracking-wide text-neutral-900">
                                        Bathrooms
                                    </h3>

                                    <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                        Bathroom ideas that combine comfort, practicality and a calm, considered finish.
                                    </p>

                                    <div class="mt-5">
                                        <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/bathrooms/' ) ); ?>">
                                            View bathrooms
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article class="overflow-hidden border border-neutral-200 bg-white">
                            <div class="grid md:grid-cols-[40%_1fr]">
                                <div class="aspect-[4/3] bg-neutral-200 md:aspect-auto">
                                    <img
                                            src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Nexus-Hunter-Green.webp' ) ); ?>"
                                            alt="Bedroom inspiration"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                    >
                                </div>

                                <div class="p-6">
                                    <h3 class="font-heading text-xl tracking-wide text-neutral-900">
                                        Bedrooms
                                    </h3>

                                    <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                        Bedroom inspiration focused on storage, atmosphere and beautifully balanced design.
                                    </p>

                                    <div class="mt-5">
                                        <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/bedrooms/' ) ); ?>">
                                            View bedrooms
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <article class="border border-[#B8C8BF] bg-[#F3F6F3] p-6">
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-[#6F857C]">
                                Explore more
                            </p>

                            <h3 class="mt-3 font-heading text-xl tracking-wide text-neutral-900">
                                Looking for more inspiration?
                            </h3>

                            <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                Explore kitchens, bathrooms and bedrooms across the wider Blaines site, or take the next step with a design consultation.
                            </p>

                            <div class="mt-5 flex flex-col gap-3">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/design-consultation/' ) ); ?>">
                                    Book a design consultation
                                </a>

                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/showrooms/' ) ); ?>">
                                    Visit our showrooms
                                </a>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- SHOWROOMS SECTION -->
        <section class="bg-neutral-50">
            <div class="prose-site mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Showrooms
                    </p>

                    <h2 class="mt-3 font-heading text-3xl tracking-wide leading-tight text-neutral-900">
                        Visit a Blaines showroom
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        See finishes in person, explore ideas and speak to our team at one of our Blaines showrooms.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <article class="overflow-hidden border border-neutral-200 bg-white">
                        <div class="aspect-[16/10] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/H01-Showroom-Front-800x600-1.jpg' ) ); ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>

                        <div class="border-t border-neutral-200 p-6">

                            <h3 class="mt-4 font-heading text-2xl tracking-wide text-neutral-900">
                                Swaffham
                            </h3>

                            <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                Visit our Swaffham showroom to explore interiors, finishes and tailored design guidance.
                            </p>

                            <div class="mt-6">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/showrooms/swaffham/' ) ); ?>">
                                    View Swaffham showroom
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="overflow-hidden border border-neutral-200 bg-white">
                        <div class="aspect-[16/10] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/h02_front_800x600.jpg' ) ); ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>
                        <div class="border-t border-neutral-200 p-6">

                            <h3 class="mt-4 font-heading text-2xl tracking-wide text-neutral-900">
                                Dereham
                            </h3>

                            <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                Explore our Dereham showroom and speak to the team about kitchens, bathrooms and bedrooms.
                            </p>

                            <div class="mt-6">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/showrooms/dereham/' ) ); ?>">
                                    View Dereham showroom
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="overflow-hidden border border-neutral-200 bg-white">
                        <div class="aspect-[16/10] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/H03-Showroom-Front-800x600-1.jpg' ) ); ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>
                        <div class="border-t border-neutral-200 p-6">

                            <h3 class="mt-4 font-heading text-2xl tracking-wide text-neutral-900">
                                Watton
                            </h3>

                            <p class="mt-3 font-body text-sm font-light leading-6 text-neutral-700">
                                Discover our Watton showroom and see ideas, materials and finishes in person.
                            </p>

                            <div class="mt-6">
                                <a class="inline-flex font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/showrooms/watton/' ) ); ?>">
                                    View Watton showroom
                                </a>
                            </div>
                        </div>
                    </article>
                </div>

            </div>
        </section>

        <!-- CONNECTED INTERIORS SECTION -->
        <section class="bg-[#F3F6F3]">
            <div class="prose-site mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-12 lg:items-center lg:px-8">
                <div class="lg:col-span-7">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Connected Interiors
                    </p>

                    <h2 class="mt-3 font-heading text-3xl md:text-[2.15rem] tracking-wide leading-tight text-neutral-900">
                        A more connected approach to interiors
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        When kitchens, bathrooms and bedrooms are planned with the wider home in mind, the result
                        feels calmer, more cohesive and easier to live with. At Blaines, we help bring together
                        layout, finishes, storage and practical detail so that each space works well on its own and
                        as part of the bigger picture.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="border border-[#B8C8BF] bg-white p-5">
                            <h3 class="font-heading text-lg tracking-wide text-neutral-900">Cohesive finishes</h3>
                            <p class="mt-2 font-body text-sm font-light leading-6 text-neutral-700">
                                Materials, colours and details that feel balanced across the home.
                            </p>
                        </div>

                        <div class="border border-[#B8C8BF] bg-white p-5">
                            <h3 class="font-heading text-lg tracking-wide text-neutral-900">Practical planning</h3>
                            <p class="mt-2 font-body text-sm font-light leading-6 text-neutral-700">
                                Storage, appliances and layout considered from the start.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="overflow-hidden border border-neutral-200 bg-white">
                        <div class="aspect-[5/4] bg-neutral-200">
                            <img
                                    src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/Pendle-Midnight-Blue.webp' ) ); ?>"
                                    alt=""
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- NEXT STEP SECTION -->
        <section class="border-t border-neutral-200 bg-white">
            <div class="prose-site mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-[#6F857C]">
                        Next step
                    </p>

                    <h2 class="mt-3 font-heading text-3xl md:text-[2.15rem] tracking-wide leading-tight text-neutral-900">
                        Start your interiors project with Blaines
                    </h2>

                    <p class="mt-6 font-body text-base font-light leading-7 text-neutral-700">
                        Whether you are planning a new kitchen, bathroom, fitted bedroom or a broader interior update,
                        our team can help you take the next step with clarity and confidence.
                    </p>
                </div>

                <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:flex-wrap">
                    <a class="inline-flex items-center justify-center border border-neutral-900 px-6 py-3 font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/design-consultation/' ) ); ?>">
                        Book a design consultation
                    </a>

                    <a class="inline-flex items-center justify-center border border-neutral-300 px-6 py-3 font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/kitchens/' ) ); ?>">
                        Explore kitchens
                    </a>

                    <a class="inline-flex items-center justify-center border border-neutral-300 px-6 py-3 font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/bathrooms/' ) ); ?>">
                        View bathrooms
                    </a>

                    <a class="inline-flex items-center justify-center border border-neutral-300 px-6 py-3 font-body text-sm font-medium text-neutral-900" href="<?php echo esc_url( home_url( '/bedrooms/' ) ); ?>">
                        View bedrooms
                    </a>
                </div>
            </div>
        </section>

    <?php
    endwhile;
endif;

get_footer();