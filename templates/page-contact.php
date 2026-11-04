<?php
/**
 * Template Name: Contact
 */

get_header();

// ACF Fields
$hero_heading = get_field( 'contact_hero_heading' ) ?: get_the_title();
$hero_intro   = get_field( 'contact_hero_intro' );

$thumb_id = get_post_thumbnail_id( get_the_ID() );

$hero_image = null;
if ( $thumb_id ) {
    $hero_image = [
        'ID'  => $thumb_id,
        'url' => wp_get_attachment_url( $thumb_id ),
        'alt' => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
    ];
}

$showrooms   = get_field( 'contact_showrooms' );
$after_sales = get_field( 'contact_after_sales_text' );

$design_heading = get_field( 'contact_design_cta_heading' );
$design_text    = get_field( 'contact_design_cta_text' );
$design_link    = get_field( 'contact_design_cta_link' );

$sidebar_promo_image = get_field( 'contact_sidebar_promo_image' );

$wpforms_shortcode = get_field( 'contact_wpforms_shortcode' );
$wpforms_id        = get_field( 'contact_wpforms_id' );
?>

<?php
get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero_image,
        'title'       => $hero_heading,
        'intro'       => $hero_intro ?: '',
        'breadcrumbs' => true,
        'align'       => 'center',
    ]
);
?>

    <section class="pb-12 md:pb-16">
        <div class="container-wide prose-site">
            <div class="grid gap-10 lg:grid-cols-12">

                <!-- MAIN -->
                <div class="space-y-12 lg:col-span-8">

                    <?php if ( $showrooms ) : ?>
                        <section aria-labelledby="contact-showrooms">
                            <h2 id="contact-showrooms" class="font-body text-2xl font-medium text-neutral-900 md:text-3xl">
                                Visit, Call or Email Our Showrooms
                            </h2>

                            <p class="mt-3 max-w-3xl text-neutral-600">
                                Speak to the Blaines team in Dereham, Swaffham or Watton, get directions, or view each showroom for more details before you visit.
                            </p>

                            <div class="mt-6 space-y-6">
                                <?php foreach ( $showrooms as $s ) : ?>
                                    <?php
                                    $name        = $s['contact_showroom_name'] ?? '';
                                    $addr        = $s['contact_showroom_address'] ?? '';
                                    $phone       = $s['contact_showroom_phone_number'] ?? '';
                                    $phone_label = $s['contact_showroom_phone_label'] ?? 'Tel';
                                    $email       = $s['contact_showroom_email'] ?? '';
                                    $hours       = $s['contact_showroom_hours'] ?? '';
                                    $maps        = $s['contact_showroom_maps_url'] ?? '';
                                    $page_obj    = $s['contact_showroom_page'] ?? null;

                                    $page_id    = 0;
                                    $page_url   = '';
                                    $page_title = '';
                                    $image_id   = 0;

                                    if ( $page_obj ) {
                                        $page_id = is_object( $page_obj ) ? (int) $page_obj->ID : (int) $page_obj;
                                    }

                                    if ( ! $page_id && $name ) {
                                        $slug      = sanitize_title( str_replace( [ 'Blaines ', 'blaines ' ], '', $name ) );
                                        $page_post = get_page_by_path( 'showrooms/' . $slug );

                                        if ( $page_post ) {
                                            $page_id = (int) $page_post->ID;
                                        }
                                    }

                                    if ( $page_id ) {
                                        $page_url   = get_permalink( $page_id );
                                        $page_title = get_the_title( $page_id );
                                        $image_id   = function_exists( 'get_field' ) ? (int) get_field( 'storefront_image', $page_id ) : 0;
                                    }

                                    /**
                                     * Use the individual showroom page as the
                                     * authoritative source for contact information.
                                     */
                                    if ( $page_id ) {

                                        $showroom_slug = get_post_field(
                                            'post_name',
                                            $page_id
                                        );

                                        $details = blaines_get_showroom_details(
                                            $showroom_slug
                                        );

                                        if ( $details ) {

                                            $addr  = $details['address'];
                                            $phone = $details['phone'];
                                            $email = $details['email'];

                                            // Convert opening-hours repeater rows
                                            // into the text format used by this card.
                                            $hours_lines = array();

                                            foreach ( $details['opening_hours'] as $row ) {

                                                $day = trim( (string) ( $row['day'] ?? '' ) );
                                                $time = trim( (string) ( $row['hours'] ?? '' ) );

                                                if ( $day || $time ) {
                                                    $hours_lines[] = trim(
                                                        $day . ' ' . $time
                                                    );
                                                }
                                            }

                                            $hours = implode( "\n", $hours_lines );
                                        }
                                    }

                                    ?>

                                    <article class="overflow-hidden border border-neutral-200 bg-white">
                                        <div class="grid md:grid-cols-[46%_54%]">

                                            <div class="bg-neutral-100">
                                                <?php if ( $image_id ) : ?>
                                                    <?php
                                                    echo wp_get_attachment_image(
                                                        $image_id,
                                                        'large',
                                                        false,
                                                        [
                                                            'class'    => 'h-[220px] w-full object-cover md:h-full',
                                                            'loading'  => 'lazy',
                                                            'decoding' => 'async',
                                                            'alt'      => $name ?: '',
                                                        ]
                                                    );
                                                    ?>
                                                <?php else : ?>
                                                    <div class="flex h-[220px] items-center justify-center text-xs uppercase tracking-[0.12em] text-neutral-500">
                                                        Showroom
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="flex min-w-0 flex-col bg-neutral-50 px-6 py-5 md:px-7 md:py-6">
                                                <?php if ( $name ) : ?>
                                                    <h3 class="font-body text-[2rem] font-medium leading-none text-neutral-900">
                                                        <?php echo esc_html( $name ); ?>
                                                    </h3>
                                                <?php endif; ?>

                                                <?php if ( $addr ) : ?>
                                                    <p class="mt-3 whitespace-pre-line font-body text-[1rem] leading-[1.35] text-neutral-700">
                                                        <?php echo esc_html( $addr ); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="mt-5 space-y-3">
                                                    <?php if ( $phone ) : ?>
                                                        <p class="font-body text-[1rem] leading-snug text-neutral-900">
                                                            <strong><?php echo esc_html( $phone_label ); ?>:</strong>
                                                            <a
                                                                    href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"
                                                                    class="hover:underline"
                                                            >
                                                                <?php echo esc_html( $phone ); ?>
                                                            </a>
                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if ( $email ) : ?>
                                                        <p class="font-body text-[1rem] leading-snug text-neutral-900">
                                                            <a href="mailto:<?php echo esc_attr( $email ); ?>" class="font-medium hover:underline">
                                                                <?php echo esc_html( $email ); ?>
                                                            </a>
                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if ( $hours ) : ?>
                                                        <p class="flex items-start gap-2 whitespace-pre-line font-body text-[0.95rem] leading-snug text-neutral-700">
                                                            <?php echo blaines_icon( 'clock', 'mt-0.5 h-4 w-4 flex-shrink-0 text-neutral-500' ); ?>
                                                            <span><?php echo esc_html( $hours ); ?></span>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="mt-5">
                                                    <?php if ( $page_url ) : ?>
                                                        <a
                                                                href="<?php echo esc_url( $page_url ); ?>"
                                                                class="inline-flex items-center font-body text-[0.95rem] font-medium text-neutral-900 underline underline-offset-4 hover:no-underline"
                                                        >
                                                            View showroom<span class="ml-2">→</span>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                </div>

                <!-- SIDEBAR -->
                <aside class="lg:col-span-4">
                    <div class="space-y-6 lg:sticky lg:top-32">

                        <?php if ( ! empty( $sidebar_promo_image['ID'] ) ) : ?>
                            <div class="overflow-hidden border border-neutral-200 bg-white">
                                <?php
                                echo wp_get_attachment_image(
                                    (int) $sidebar_promo_image['ID'],
                                    'large',
                                    false,
                                    [
                                        'class'    => 'block h-auto w-full object-cover',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                        'alt'      => $sidebar_promo_image['alt'] ?? '',
                                    ]
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="border border-neutral-200 bg-neutral-50 p-7">
                            <h2 class="font-body text-lg font-medium text-neutral-900">
                                Need help with an order or service query?
                            </h2>
                            <p class="mt-2 text-neutral-600">
                                Call your nearest showroom for stock checks, order updates or after-sales support. Or fill out the form below.
                            </p>
                        </div>

                        <?php if ( $design_heading && $design_text && $design_link ) : ?>
                            <?php
                            $design_url   = is_array( $design_link ) ? ( $design_link['url'] ?? '' ) : $design_link;
                            $design_title = ( is_array( $design_link ) && ! empty( $design_link['title'] ) )
                                ? $design_link['title']
                                : 'Book a Free Design Appointment';
                            ?>
                            <div class="border border-neutral-200 bg-white p-7 shadow-[0_8px_24px_rgba(0,0,0,0.06)]">
                                <h2 class="font-body text-lg font-medium text-neutral-900">
                                    <?php echo esc_html( $design_heading ); ?>
                                </h2>

                                <p class="mt-2 text-neutral-600">
                                    <?php echo esc_html( $design_text ); ?>
                                </p>

                                <?php if ( $design_url ) : ?>
                                    <a
                                            href="<?php echo esc_url( $design_url ); ?>"
                                            class="mt-5 inline-flex w-full items-center justify-center border border-neutral-900 bg-neutral-900 px-6 py-3 text-sm font-body font-semibold text-white transition hover:bg-neutral-800"
                                    >
                                        <?php echo esc_html( $design_title ); ?>
                                        <span class="ml-2">→</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </aside>

            </div>
        </div>
    </section>

    <section class="pt-8 pb-16 md:pt-12 md:pb-24" aria-labelledby="contact-form">
        <div class="container-wide prose-site">
            <div class="border border-neutral-200 bg-white p-7 shadow-[0_8px_24px_rgba(0,0,0,0.06)] md:p-10 lg:p-12">
                <div class="max-w-4xl">
                    <h2 id="contact-form" class="font-body text-2xl font-medium text-neutral-900 md:text-3xl">
                        Send Us a Message
                    </h2>

                    <p class="mt-3 text-neutral-600">
                        For general questions, product enquiries, after-sales support or order updates, send us a message below and our team will get back to you.
                    </p>
                </div>

                <div class="mt-8 max-w-4xl">
                    <?php
                    if ( $wpforms_shortcode ) {
                        echo do_shortcode( wp_kses_post( $wpforms_shortcode ) );
                    } elseif ( $wpforms_id ) {
                        $id = (int) $wpforms_id;
                        echo do_shortcode(
                            sprintf(
                                '[wpforms id="%d" title="false" description="false" ajax="1"]',
                                $id
                            )
                        );
                    } elseif ( current_user_can( 'edit_posts' ) ) {
                        echo '<p class="text-sm text-neutral-600">WPForms is not configured. Add <code>contact_wpforms_shortcode</code> or <code>contact_wpforms_id</code> in ACF.</p>';
                    }
                    ?>
                </div>

                <?php if ( $after_sales ) : ?>
                    <p class="mt-6 max-w-4xl whitespace-pre-line text-sm text-neutral-600">
                        <?php echo esc_html( $after_sales ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>