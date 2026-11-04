<?php
/**
 * Content block for Single Project
 */

$types = get_the_term_list( get_the_ID(), 'project_type', '', ', ', '' );
$completion = get_field('completion_date'); // ACF
$tags = get_the_tag_list('', ', ');
?>

<!-- TITLE -->
<h1 class="mb-4 font-heading text-3xl font-medium leading-tight tracking-wide text-slate-900 md:text-4xl">
    <?php the_title(); ?>
</h1>

<div class="mb-10 border-t border-black/10 pt-5 pb-6 font-body text-sm text-black/70">

    <?php if ( $types ) : ?>
        <div class="mb-3">
            <span class="font-medium text-black">Project Type:</span>
            <?php echo wp_kses_post( $types ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $completion ) : ?>
        <div class="mb-3">
            <span class="font-medium text-black">Completion Date:</span>
            <?php echo esc_html( $completion ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $tags ) : ?>
        <div>
            <span class="font-medium text-black">Style & Colours:</span>
            <?php echo wp_kses_post( $tags ); ?>
        </div>
    <?php endif; ?>

</div>

<!-- DESCRIPTION / CONTENT -->
<div class="prose-site prose-news max-w-none mt-8">
    <?php the_content(); ?>
</div>
