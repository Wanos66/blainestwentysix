<?php get_header(); ?>

<div class="container-wide mx-auto w-full py-32 px-4 text-center">
    <h1 class="text-7xl font-bold mb-4">404</h1>
    <p class="text-lg mb-8">Sorry, the page you’re looking for cannot be found.</p>

    <a href="<?php echo esc_url( home_url('/') ); ?>"
            class="text-blue-600 hover:underline">
        Return home
    </a>
</div>

<?php get_footer(); ?>



