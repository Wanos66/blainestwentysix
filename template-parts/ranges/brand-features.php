<?php
/**
 * Template part: Range Brand Features
 *
 * Expected ACF fields:
 * - show_brand_features_section (true/false)
 * - brand_features_mode (auto|custom)
 * - brand_features_heading
 * - brand_features_intro
 * - brand_feature_items (repeater)
 *   - title
 *   - text
 *   - illustration (image, optional, return format: array)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = $args['post_id'] ?? get_the_ID();

$is_freedom_mode = ! empty( $args['is_freedom_mode'] );

$show_section = get_field( 'show_brand_features_section', $post_id );
if ( ! $show_section ) {
    return;
}

$mode = get_field( 'brand_features_mode', $post_id );
$mode = $mode ? $mode : 'auto';

/**
 * Brand lookup from kitchen_brand taxonomy.
 */
$brand = '';
$terms = get_the_terms( $post_id, 'kitchen_brand' );

if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    $primary_term = reset( $terms );

    if ( $primary_term && ! empty( $primary_term->slug ) ) {
        $brand = $primary_term->slug;
    }
}

/**
 * Freedom view override.
 */
if ( $is_freedom_mode ) {
    $brand = 'freedom';
}

/**
 * IMPORTANT:
 * Update these keys if your real kitchen_brand term slugs differ.
 */
$defaults = [
    'gallery' => [
        'kicker'  => 'Symphony Gallery',
        'heading' => 'Why choose Symphony Gallery?',
        'intro'   => 'Beautifully designed for everyday living, Symphony Gallery kitchens combine style, flexibility and practical appeal.',
        'items'   => [
            [
                'title'        => 'Wide choice of styles',
                'text'         => 'From sleek modern kitchens to timeless shaker designs, Gallery offers a look to suit every home.',
                'illustration' => 'gallery-door-styles.svg',
            ],
            [
                'title'        => 'Flexible finish options',
                'text'         => 'Choose from a broad selection of colours, textures and combinations to create a kitchen that feels personal.',
                'illustration' => 'gallery-finish-options.svg',
            ],
            [
                'title'        => 'Everyday practical design',
                'text'         => 'Thoughtful storage and layout options help create a kitchen that works beautifully for daily family life.',
                'illustration' => 'gallery-practical-design.svg',
            ],
            [
                'title'        => 'Freedom accessible options',
                'text'         => 'Selected Gallery ranges are available with Freedom features for more accessible, inclusive kitchen design.',
                'illustration' => 'gallery-accessibility.svg',
            ],
        ],
    ],
    'hacker' => [
        'kicker'  => 'Häcker concept130',
        'heading' => 'Why choose Häcker concept130?',
        'intro'   => 'Häcker concept130 kitchens combine modern German design with flexible planning, refined finishes and everyday practicality.',
        'items'   => [
            [
                'title'        => 'Flexible kitchen planning',
                'text'         => 'Built around a versatile grid system, concept130 offers smart layouts for a wide range of spaces and lifestyles.',
                'illustration' => 'hacker-planning.svg',
            ],
            [
                'title'        => 'Broad finish choice',
                'text'         => 'From clean contemporary doors to textured and framed designs, concept130 gives you real freedom in the final look.',
                'illustration' => 'hacker-finishes.svg',
            ],
            [
                'title'        => 'German-made quality',
                'text'         => 'Precision engineering and durable materials help create a kitchen that feels beautifully made and built to last.',
                'illustration' => 'hacker-quality.svg',
            ],
            [
                'title'        => 'Responsible production',
                'text'         => 'Concept130 combines design and practicality with a strong focus on responsible manufacturing and long-term use.',
                'illustration' => 'hacker-sustainability.svg',
            ],
        ],
    ],
    'freedom' => [
        'kicker'  => 'Freedom Kitchens',
        'heading' => 'Why choose Freedom Kitchens?',
        'intro'   => 'Freedom kitchens are designed to make everyday cooking, access and movement easier without compromising on style.',
        'items'   => [
            [
                'title'        => 'Designed for accessibility',
                'text'         => 'Created to support easier, safer and more independent kitchen use in a beautifully integrated way.',
                'illustration' => 'freedom-accessibility.svg',
            ],
            [
                'title'        => 'Stylish by design',
                'text'         => 'Accessibility features are built into kitchens that still feel contemporary, elegant and visually considered.',
                'illustration' => 'freedom-design.svg',
            ],
            [
                'title'        => 'Flexible around your needs',
                'text'         => 'Layouts, storage and working areas can be tailored to suit the way you move, reach and use the space.',
                'illustration' => 'freedom-flexibility.svg',
            ],
            [
                'title'        => 'Comfort for everyday living',
                'text'         => 'Thoughtful design choices help improve usability, confidence and comfort throughout the kitchen.',
                'illustration' => 'freedom-comfort.svg',
            ],
        ],
    ],
];

$section = null;

if ( 'custom' === $mode ) {
    $custom_items = get_field( 'brand_feature_items', $post_id );

    $section = [
        'kicker'  => trim( (string) get_field( 'brand_features_kicker', $post_id ) ),
        'heading' => trim( (string) get_field( 'brand_features_heading', $post_id ) ),
        'intro'   => trim( (string) get_field( 'brand_features_intro', $post_id ) ),
        'items'   => is_array( $custom_items ) ? $custom_items : [],
    ];
} else {
    if ( $brand && isset( $defaults[ $brand ] ) ) {
        $section = $defaults[ $brand ];
    }
}

if (
    empty( $section ) ||
    empty( $section['heading'] ) ||
    empty( $section['items'] ) ||
    ! is_array( $section['items'] )
) {
    return;
}

/**
 * Brand styling map.
 * These are hard-coded so Tailwind can see the classes.
 */
$brand_styles = [
    'gallery' => [
        'section' => 'bg-[#f3f6f3]',
        'kicker'  => 'text-stone-500',
        'card'    => 'bg-white border border-stone-200',
    ],
    'hacker' => [
        'section' => 'bg-slate-50/80',
        'kicker'  => 'text-slate-500',
        'card'    => 'bg-white border border-slate-200',
    ],
    'freedom' => [
        'section' => 'bg-[#f5f1f4]',
        'kicker'  => 'text-[#7b5d78]',
        'card'    => 'bg-white border border-[#e4d9e2]',
    ],
];

$style_set = $brand_styles[ $brand ] ?? [
    'section' => 'bg-stone-50/80',
    'kicker'  => 'text-stone-500',
    'card'    => 'bg-white border border-stone-200',
];

$items = [];

foreach ( $section['items'] as $item ) {
    $title = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
    $text  = isset( $item['text'] ) ? trim( (string) $item['text'] ) : '';

    if ( ! $title || ! $text ) {
        continue;
    }

    $illustration_url = '';
    $illustration_alt = $title;

    if ( isset( $item['illustration'] ) && is_array( $item['illustration'] ) && ! empty( $item['illustration']['url'] ) ) {
        $illustration_url = $item['illustration']['url'];
        $illustration_alt = ! empty( $item['illustration']['alt'] ) ? $item['illustration']['alt'] : $title;
    }

    if ( ! $illustration_url && ! empty( $item['illustration'] ) && is_string( $item['illustration'] ) ) {
        $relative_file = ltrim( $item['illustration'], '/' );
        $file_path     = trailingslashit( get_template_directory() ) . 'assets/brand-features/' . $relative_file;

        if ( file_exists( $file_path ) ) {
            $illustration_url = trailingslashit( get_template_directory_uri() ) . 'assets/brand-features/' . $relative_file;
        }
    }

    $items[] = [
        'title'            => $title,
        'text'             => $text,
        'illustration_url' => $illustration_url,
        'illustration_alt' => $illustration_alt,
    ];
}

if ( empty( $items ) ) {
    return;
}

$has_any_illustrations = false;

foreach ( $items as $item ) {
    if ( ! empty( $item['illustration_url'] ) ) {
        $has_any_illustrations = true;
        break;
    }
}
?>

<section class="brand-features brand-features--<?php echo esc_attr( $brand ); ?> py-14 lg:py-18 <?php echo esc_attr( $style_set['section'] ); ?>">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="max-w-3xl">
            <?php if ( ! empty( $section['kicker'] ) ) : ?>
                <p class="mb-4 font-body text-xs font-medium uppercase tracking-[0.22em] <?php echo esc_attr( $style_set['kicker'] ); ?>">
                    <?php echo esc_html( $section['kicker'] ); ?>
                </p>
            <?php endif; ?>

            <h2 class="font-heading text-3xl text-slate-900 sm:text-4xl">
                <?php echo esc_html( $section['heading'] ); ?>
            </h2>

            <?php if ( ! empty( $section['intro'] ) ) : ?>
                <p class="mt-4 max-w-2xl font-body text-base leading-7 text-slate-600">
                    <?php echo esc_html( $section['intro'] ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <?php foreach ( $items as $item ) : ?>
                <article class="flex h-full flex-col p-7 <?php echo esc_attr( $style_set['card'] ); ?>">
                    <?php if ( $has_any_illustrations && ! empty( $item['illustration_url'] ) ) : ?>
                        <div class="mb-6 flex min-h-[96px] items-center justify-center">
                            <img
                                    class="max-h-24 w-auto object-contain"
                                    src="<?php echo esc_url( $item['illustration_url'] ); ?>"
                                    alt="<?php echo esc_attr( $item['illustration_alt'] ); ?>"
                                    loading="lazy"
                            />
                        </div>
                    <?php endif; ?>

                    <div class="min-h-[3.5rem]">
                        <h3 class="font-heading text-[1.3rem] leading-tight text-slate-900">
                            <?php echo esc_html( $item['title'] ); ?>
                        </h3>
                    </div>

                    <p class="mt-4 font-body text-[15px] leading-7 text-slate-600">
                        <?php echo esc_html( $item['text'] ); ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>