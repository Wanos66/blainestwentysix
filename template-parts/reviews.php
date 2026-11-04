<?php
/**
 * Customer reviews carousel.
 *
 * Optional arguments:
 * - source_id: Post ID containing the ACF reviews repeater.
 * - title:     Main section heading.
 * - intro:     Supporting section copy.
 * - variant:   Presentation variant. Supports "retail".
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
    $args ?? [],
    [
        'source_id' => 518,
        'title'     => 'What our customers say',
        'intro'     => 'Recent feedback about our advice, service and delivery.',
        'variant'   => 'default',
    ]
);

$source_id = absint( $args['source_id'] );

$reviews = function_exists( 'get_field' )
    ? get_field( 'reviews', $source_id )
    : [];

if ( ! is_array( $reviews ) || empty( $reviews ) ) {
    return;
}

$reviews = array_values(
    array_filter(
        $reviews,
        static function ( $review ) {
            if ( ! is_array( $review ) ) {
                return false;
            }

            $name = trim(
                (string) ( $review['reviewer_name'] ?? '' )
            );

            $text = trim(
                (string) ( $review['review_text'] ?? '' )
            );

            return '' !== $name || '' !== $text;
        }
    )
);

if ( empty( $reviews ) ) {
    return;
}

$review_count = count( $reviews );
$is_retail    = 'retail' === sanitize_key(
    (string) $args['variant']
);
?>

<?php if ( $is_retail ) : ?>

    <section
            id="reviewsSection"
            class="retail-reviews"
            aria-labelledby="customer-reviews-heading"
    >
        <div class="container-wide">

            <div class="retail-reviews__shell">

                <header class="retail-reviews__header">

                    <div>

                        <h2 id="customer-reviews-heading">
                            <?php echo esc_html( $args['title'] ); ?>
                        </h2>

                        <?php if ( '' !== trim( (string) $args['intro'] ) ) : ?>

                            <p>
                                <?php echo esc_html( $args['intro'] ); ?>
                            </p>

                        <?php endif; ?>

                    </div>

                    <?php if ( $review_count > 1 ) : ?>

                        <div class="retail-reviews__controls">

                            <button
                                    id="prevReview"
                                    type="button"
                                    aria-label="Show previous customer review"
                                    class="retail-reviews__control"
                            >
                                <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        focusable="false"
                                >
                                    <path d="m15 18-6-6 6-6"></path>
                                </svg>
                            </button>

                            <button
                                    id="nextReview"
                                    type="button"
                                    aria-label="Show next customer review"
                                    class="retail-reviews__control"
                            >
                                <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        focusable="false"
                                >
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>

                        </div>

                    <?php endif; ?>

                </header>

                <div class="retail-reviews__viewport">

                    <div
                            id="reviewTrack"
                            class="retail-reviews__track"
                            aria-live="off"
                    >

                        <?php foreach ( $reviews as $review ) : ?>

                            <?php
                            $name = trim(
                                (string) (
                                    $review['reviewer_name']
                                    ?? ''
                                )
                            );

                            $text = trim(
                                (string) (
                                    $review['review_text']
                                    ?? ''
                                )
                            );

                            $date = trim(
                                (string) (
                                    $review['review_date']
                                    ?? ''
                                )
                            );

                            $rating = max(
                                0,
                                min(
                                    5,
                                    (int) (
                                        $review['rating']
                                        ?? 0
                                    )
                                )
                            );
                            ?>

                            <article class="retail-review-card">

                                <span
                                        class="retail-review-card__quote"
                                        aria-hidden="true"
                                >
                                    “
                                </span>

                                <?php if ( $rating > 0 ) : ?>

                                    <p
                                            class="retail-review-card__rating"
                                            aria-label="<?php echo esc_attr(
                                                sprintf(
                                                    _n(
                                                        '%d out of 5 star',
                                                        '%d out of 5 stars',
                                                        $rating,
                                                        'blaines'
                                                    ),
                                                    $rating
                                                )
                                            ); ?>"
                                    >
                                        <span aria-hidden="true">
                                            <?php
                                            echo esc_html(
                                                str_repeat(
                                                    '★',
                                                    $rating
                                                )
                                            );
                                            ?>
                                        </span>
                                    </p>

                                <?php endif; ?>

                                <?php if ( '' !== $text ) : ?>

                                    <p class="retail-review-card__text">
                                        <?php echo esc_html( $text ); ?>
                                    </p>

                                <?php endif; ?>

                                <footer class="retail-review-card__footer">

                                    <?php if ( '' !== $name ) : ?>

                                        <h3>
                                            <?php echo esc_html( $name ); ?>
                                        </h3>

                                    <?php endif; ?>

                                    <?php if ( '' !== $date ) : ?>

                                        <p>
                                            <?php echo esc_html( $date ); ?>
                                        </p>

                                    <?php endif; ?>

                                </footer>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>
    </section>

<?php else : ?>

    <section
            id="reviewsSection"
            class="bg-neutral-100 py-12 lg:py-16"
            aria-labelledby="customer-reviews-heading"
    >
        <div class="container-wide">

            <div class="mb-8 border-b border-neutral-300 pb-4">

                <div class="flex items-start justify-between gap-6">

                    <div class="min-w-0 flex-1">

                        <h2
                                id="customer-reviews-heading"
                                class="text-3xl font-semibold leading-tight text-blue-900 md:text-4xl"
                        >
                            <?php echo esc_html( $args['title'] ); ?>
                        </h2>

                        <?php if ( '' !== trim( (string) $args['intro'] ) ) : ?>

                            <p class="mt-3 max-w-2xl text-base leading-relaxed text-neutral-700">
                                <?php echo esc_html( $args['intro'] ); ?>
                            </p>

                        <?php endif; ?>

                        <div
                                class="mt-5 h-[3px] w-48 max-w-full bg-amber-400 md:w-64"
                                aria-hidden="true"
                        ></div>

                    </div>

                    <?php if ( $review_count > 1 ) : ?>

                        <div class="flex flex-none items-center gap-2 sm:gap-4">

                            <button
                                    id="prevReview"
                                    type="button"
                                    aria-label="Show previous customer review"
                                    class="inline-flex h-10 w-10 items-center justify-center text-blue-900 transition hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
                            >
                                <svg
                                        class="h-8 w-8"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.75"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        focusable="false"
                                >
                                    <path d="M15 18l-6-6 6-6"></path>
                                </svg>
                            </button>

                            <button
                                    id="nextReview"
                                    type="button"
                                    aria-label="Show next customer review"
                                    class="inline-flex h-10 w-10 items-center justify-center text-blue-900 transition hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
                            >
                                <svg
                                        class="h-8 w-8"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.75"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                        focusable="false"
                                >
                                    <path d="M9 18l6-6-6-6"></path>
                                </svg>
                            </button>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <div class="relative overflow-hidden">

                <div
                        id="reviewTrack"
                        class="flex gap-4 transition-transform duration-700 ease-in-out will-change-transform"
                        aria-live="off"
                >

                    <?php foreach ( $reviews as $review ) : ?>

                        <?php
                        $name = trim(
                            (string) (
                                $review['reviewer_name']
                                ?? ''
                            )
                        );

                        $text = trim(
                            (string) (
                                $review['review_text']
                                ?? ''
                            )
                        );

                        $date = trim(
                            (string) (
                                $review['review_date']
                                ?? ''
                            )
                        );

                        $rating = max(
                            0,
                            min(
                                5,
                                (int) (
                                    $review['rating']
                                    ?? 0
                                )
                            )
                        );
                        ?>

                        <article
                                class="w-[85vw] max-w-[360px] flex-none border border-neutral-200 bg-white p-6
                                sm:w-[320px]
                                md:w-[calc((100%_-_2rem)/3)] md:max-w-none
                                lg:w-[calc((100%_-_3rem)/4)]"
                        >

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <?php if ( '' !== $name ) : ?>

                                        <h3 class="text-lg font-semibold leading-snug text-neutral-950">
                                            <?php echo esc_html( $name ); ?>
                                        </h3>

                                    <?php endif; ?>

                                    <?php if ( '' !== $date ) : ?>

                                        <p class="mt-1 text-sm text-neutral-500">
                                            <?php echo esc_html( $date ); ?>
                                        </p>

                                    <?php endif; ?>

                                </div>

                                <?php if ( $rating > 0 ) : ?>

                                    <p
                                            class="flex-none whitespace-nowrap text-sm tracking-[0.08em] text-amber-500"
                                            aria-label="<?php echo esc_attr(
                                                sprintf(
                                                    _n(
                                                        '%d out of 5 star',
                                                        '%d out of 5 stars',
                                                        $rating,
                                                        'blaines'
                                                    ),
                                                    $rating
                                                )
                                            ); ?>"
                                    >
                                        <span aria-hidden="true">
                                            <?php
                                            echo esc_html(
                                                str_repeat(
                                                    '★',
                                                    $rating
                                                )
                                            );
                                            ?>
                                        </span>
                                    </p>

                                <?php endif; ?>

                            </div>

                            <?php if ( '' !== $text ) : ?>

                                <p class="mt-5 line-clamp-6 text-sm leading-relaxed text-neutral-700">
                                    <?php echo esc_html( $text ); ?>
                                </p>

                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </section>

<?php endif; ?>
