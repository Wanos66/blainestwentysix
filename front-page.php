<?php get_header(); ?>


<?php get_template_part('template-parts/main-slider'); ?>

<?php get_template_part('template-parts/components/home-intro'); ?>

<!-- Features Section -->
<section class="bg-gray-50 py-16">
    <div class="container-wide">

        <div class="grid gap-8 md:grid-cols-3">
            <!-- Feature Card - 1 -->
            <div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-300">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/electrical-teaser-800x600-1.jpg" alt="" class="p-0 m-0">

                <div class="p-8 text-center flex flex-col h-[300px]">
                    <h3 class="mb-3 font-body text-xl font-semibold tracking-wide text-gray-800">Powering Your Home</h3>
                    <div class="border-t border-slate-700 mb-2"></div>
                    <p class="font-body text-gray-600 leading-relaxed font-light">
                        At Blaines, we stock only top brands, from sleek fridges to efficient washing machines. We offer a wide range of premium electrical appliances to suit every budget and home. </p>
                    <a
                            href="<?php echo home_url(); ?>/electrical"
                            class="mt-auto inline-flex items-center justify-center border-2 bg-slate-800 px-4 py-2 font-body text-sm font-semibold text-white shadow-md hover:bg-white hover:text-brand md:text-base"
                    >
                        Visit our online store
                    </a>
                </div>
            </div>

            <!-- Feature Card - 2 -->
            <div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-300">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/design-teaser-800x600-1.jpg" alt="" class="p-0 m-0">

                <div class="p-8 text-center flex flex-col h-[300px]">
                    <h3 class="mb-3 font-body text-xl font-semibold tracking-wide text-gray-800">Designing Your Dreams</h3>
                    <div class="border-t border-slate-700 mb-2"></div>
                    <p class="font-body text-gray-600 leading-relaxed font-light">
                        Transform your kitchen, bathroom, or bedroom with our bespoke design and installation service. From concept to completion, we offer a seamless service. </p>
                    <a
                            href="<?php echo home_url(); ?>/interiors"
                            class="mt-auto inline-flex items-center justify-center border-2 bg-slate-800 px-4 py-2 font-body text-sm font-semibold text-white shadow-md hover:bg-white hover:text-brand md:text-base"
                    >Explore our range</a>
                </div>
            </div>

            <!-- Feature Card - 3 -->
            <div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-300">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/H01-Showroom-Front-800x600-1.jpg" alt="" class="p-0 m-0">

                <div class="p-8 text-center flex flex-col h-[300px]">
                    <h3 class="mb-3 font-body text-xl font-semibold tracking-wide text-gray-800">Local & Trusted</h3>
                    <div class="border-t border-slate-700 mb-2"></div>
                    <p class="font-body text-gray-600 leading-relaxed font-light">
                        A locally owned business, we take pride in serving the Norfolk community. We’re committed to providing exceptional service, building lasting relationships with our customers. </p>
                        <a
                                href="<?php echo home_url(); ?>/showrooms"
                                class="mt-auto inline-flex items-center justify-center border-2 bg-slate-800 px-4 py-2 font-body text-sm font-semibold text-white shadow-md hover:bg-white hover:text-brand md:text-base"
                        >Find your showroom</a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Klarna Finance Promotion -->
<section class="py-10">
    <div class="container-wide">
        <img src="<?php echo get_template_directory_uri() ?>/assets/images/KLARNA-HOMEPAGE-BANNER-2220X300.webp" alt="">
    </div>

</section>

<?php get_template_part('template-parts/components/promotions-grid'); ?>


<?php get_template_part('template-parts/reviews'); ?>

<!--    --><?php //get_template_part('template-parts/newsletter'); ?>


<?php get_footer(); ?>
