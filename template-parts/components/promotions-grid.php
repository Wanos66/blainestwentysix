<?php
/**
 * Promotions Grid Component.
 *
 * Expects ACF repeater: promotions_grid
 *
 * Optional arguments:
 * - section_class: Legacy section classes.
 * - grid_class:    Legacy grid classes.
 * - source_id:     Post containing the ACF repeater.
 * - variant:       Presentation variant. Supports "retail".
 * - title:         Retail section heading.
 * - intro:         Retail section introduction.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
    $args ?? [],
    [
        'section_class' => 'bg-gray-50 pb-16',
        'grid_class'    => 'grid gap-8 md:grid-cols-4',
        'source_id'     => get_queried_object_id(),
        'variant'       => 'default',
        'title'         => '',
        'intro'         => '',
    ]
);

$items = function_exists( 'get_field' )
    ? get_field(
        'promotions_grid',
        (int) $args['source_id']
    )
    : [];

if ( ! is_array( $items ) || empty( $items ) ) {
    return;
}

$items = array_values(
    array_filter(
        $items,
        static function ( $item ) {
            return (
                is_array( $item )
                && is_array( $item['image'] ?? null )
                && ! empty( $item['image']['ID'] )
            );
        }
    )
);

if ( empty( $items ) ) {
    return;
}

$is_retail = 'retail' === sanitize_key(
    (string) $args['variant']
);
?>

<?php if ( $is_retail ) : ?>

    <section
            class="retail-promotions"
            aria-labelledby="retail-promotions-heading"
    >
        <div class="container-wide">

            <header class="retail-promotions__header">

                <h2 id="retail-promotions-heading">
                    <?php
                    echo wp_kses_post(
                        (string) $args['title']
                    );
                    ?>
                </h2>

                <?php if ( '' !== trim( (string) $args['intro'] ) ) : ?>

                    <p>
                        <?php echo esc_html( $args['intro'] ); ?>
                    </p>

                <?php endif; ?>

            </header>

            <div class="retail-promotions__grid">

                <?php foreach ( $items as $item ) : ?>

                    <?php
                    $image = $item['image'];
                    $link  = $item['link'] ?? null;
                    $alt   = trim(
                        (string) ( $item['alt'] ?? '' )
                    );

                    $image_alt = '' !== $alt
                        ? $alt
                        : trim(
                            (string) ( $image['alt'] ?? '' )
                        );

                    $has_link = (
                        is_array( $link )
                        && ! empty( $link['url'] )
                    );

                    $target = $has_link
                        ? (string) ( $link['target'] ?? '_self' )
                        : '_self';

                    $rel = '_blank' === $target
                        ? 'noopener noreferrer'
                        : '';
                    ?>

                    <article class="retail-promotion-card">

                        <?php if ( $has_link ) : ?>

                            <a
                                    href="<?php echo esc_url( $link['url'] ); ?>"
                                    target="<?php echo esc_attr( $target ); ?>"
                                    rel="<?php echo esc_attr( $rel ); ?>"
                                    class="retail-promotion-card__link"
                            >

                        <?php endif; ?>

                            <?php
                            echo wp_get_attachment_image(
                                (int) $image['ID'],
                                'large',
                                false,
                                [
                                    'class'    => 'retail-promotion-card__image',
                                    'alt'      => esc_attr( $image_alt ),
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]
                            );
                            ?>

                        <?php if ( $has_link ) : ?>

                            </a>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        </div>
    </section>

<?php else : ?>

    <section class="<?php echo esc_attr( $args['section_class'] ); ?>">

        <div class="container-wide">

            <div class="<?php echo esc_attr( $args['grid_class'] ); ?>">

                <?php foreach ( $items as $item ) : ?>

                    <?php
                    $image = $item['image'];
                    $link  = $item['link'] ?? null;
                    $alt   = trim(
                        (string) ( $item['alt'] ?? '' )
                    );

                    $image_alt = '' !== $alt
                        ? $alt
                        : trim(
                            (string) ( $image['alt'] ?? '' )
                        );

                    $has_link = (
                        is_array( $link )
                        && ! empty( $link['url'] )
                    );

                    $target = $has_link
                        ? (string) ( $link['target'] ?? '_self' )
                        : '_self';

                    $rel = '_blank' === $target
                        ? 'noopener noreferrer'
                        : '';
                    ?>

                    <div>

                        <?php if ( $has_link ) : ?>

                            <a
                                    href="<?php echo esc_url( $link['url'] ); ?>"
                                    target="<?php echo esc_attr( $target ); ?>"
                                    rel="<?php echo esc_attr( $rel ); ?>"
                                    class="block"
                            >

                        <?php endif; ?>

                            <?php
                            echo wp_get_attachment_image(
                                (int) $image['ID'],
                                'large',
                                false,
                                [
                                    'class'    => 'h-auto w-full',
                                    'alt'      => esc_attr( $image_alt ),
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]
                            );
                            ?>

                        <?php if ( $has_link ) : ?>

                            </a>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

<?php endif; ?>
