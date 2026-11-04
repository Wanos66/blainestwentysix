<?php
/**
 * Project Meta
 */

$types = get_the_term_list( get_the_ID(), 'project_type', '', ', ', '' );
$completion = get_field('completion_date'); // ACF
$tags = get_the_tag_list('', ', ');
?>

<div class="border-t border-gray-200 pt-4 pb-6 mb-8 text-sm text-gray-700">

    <?php if ( $types ) : ?>
        <div class="mb-2">
            <span class="font-semibold">Project Type:</span>
            <?php echo wp_kses_post( $types ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $completion ) : ?>
        <div class="mb-2">
            <span class="font-semibold">Completion Date:</span>
            <?php echo esc_html( $completion ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $tags ) : ?>
        <div>
            <span class="font-semibold">Style & Colours:</span>
            <?php echo wp_kses_post( $tags ); ?>
        </div>
    <?php endif; ?>

</div>

