<?php
/**
 * Blog index main column (NO sidebar)
 * Used by home.php and AJAX replacements.
 */

if ( have_posts() ) : ?>
    <div class="grid gap-10">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/content/card', 'equal' ); ?>
        <?php endwhile; ?>
    </div>
<?php else : ?>
    <p class="text-center text-slate-600">No posts were found.</p>
<?php endif; ?>
