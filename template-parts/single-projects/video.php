<?php
/**
 * Single Project — Walk-through Video (optional)
 */

$value = get_field('project_video_url');
if ( ! $value ) return;
?>

<section class="mt-10 border border-black/10 bg-white p-6">
    <h3 class="mb-4 font-body text-lg font-medium tracking-wide text-black">
        Project walkthrough
    </h3>

    <div class="aspect-video bg-black/5 overflow-hidden relative">
        <div class="absolute inset-0 [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:block">
            <?php
            if ( is_string( $value ) && stripos( $value, '<iframe' ) !== false ) {
                echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                $embed = wp_oembed_get( (string) $value );
                if ( $embed ) {
                    echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                } else {
                    ?>
                    <a class="font-body text-sm text-black/70 underline hover:text-black"
                        href="<?php echo esc_url( (string) $value ); ?>" target="_blank" rel="noopener">
                        View walkthrough video
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>