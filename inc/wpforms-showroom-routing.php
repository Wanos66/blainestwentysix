<?php
/**
 * Route enquiries from the shared WPForms "Showroom Contact" form.
 *
 * Form IDs / showroom_page_id hidden-field IDs:
 *   Local:   form 1076,   field 4
 *   Staging: form 284870, field 7
 *   Live:    form 284870, field 7
 *
 * The hidden field's default value must be {page_id}.
 * The trusted destination comes from the showroom PAGE's ACF `email` field.
 * Never use the submitted "Showroom email" field as a mail recipient: it can
 * be modified by a visitor (including through Clearance enquiry URLs).
 *
 * Keep form fields (e.g. Product Name, enquiry message) untouched so that
 * Clearance enquiries continue to populate their existing notification text.
 */

declare(strict_types=1);

add_filter('wpforms_entry_email_atts', static function (
    $email,
    $fields,
    $entry,
    $form_data,
    $notification_id
) {
    $form_id = (int) ($form_data['id'] ?? 0);

    // These are the same form logically, but have different IDs per database.
    $page_field_ids = [
        1076   => 4, // Local
        284870 => 7, // Staging and Live
    ];

    if (!isset($page_field_ids[$form_id])) {
        return $email;
    }

    // Always retain the site administrator as a recipient.
    $recipients  = [];
    $admin_email = sanitize_email((string) get_option('admin_email'));

    if (is_email($admin_email)) {
        $recipients[] = $admin_email;
    }

    // Read the processed submitted field value (not a raw URL email address).
    $field_id = $page_field_ids[$form_id];
    $page_id  = absint($fields[$field_id]['value'] ?? 0);

    // A visitor can alter hidden fields; only accept actual published showroom pages.
    if (
        $page_id > 0
        && get_post_type($page_id) === 'page'
        && get_post_status($page_id) === 'publish'
        && get_page_template_slug($page_id) === 'templates/page-showroom.php'
        && function_exists('get_field')
    ) {
        $showroom_email = sanitize_email((string) get_field('email', $page_id));

        if (is_email($showroom_email)) {
            $recipients[] = $showroom_email;
        }
    }

    // WPForms expects an array of email addresses here.  Override *only*
    // recipients, leaving the subject, message and other settings unchanged.
    // If the page cannot be verified, only the administrator receives a copy.
    $email['address'] = array_values(array_unique($recipients));

    return $email;
}, 10, 5);
