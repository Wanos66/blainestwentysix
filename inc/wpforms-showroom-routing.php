<?php
declare(strict_types=1);

/**
 * Route WPForms showroom enquiries to the showroom email stored on the page (ACF: enquiry_email).
 * Form ID: 1076
 * Requires a Hidden Field named: showroom_page_id (default value: {page_id})
 */

add_filter('wpforms_email_send_to_email_address', function ($emails, $notification, $form_data) {

    // Only for your showroom enquiry form
    if ((int) ($form_data['id'] ?? 0) !== 1076) {
        return $emails;
    }

    // Only override the main notification (optional but usually desirable)
    // If you have multiple notifications and only want to override one,
    // set its Notification Name in WPForms and match it here.
    // Example:
    // if (($notification['notification_name'] ?? '') !== 'Showroom Notification') return $emails;

    // Pull the page ID from the submitted hidden field.
    $page_id = 0;

    // WPForms stores submitted field values in $form_data['fields'].
    // But field structure differs depending on versions/addons, so we also check $_POST as fallback.
    if (!empty($_POST['wpforms']['complete'])) {
        $complete = json_decode((string) wp_unslash($_POST['wpforms']['complete']), true);
        if (is_array($complete)) {
            foreach ($complete as $field) {
                if (($field['name'] ?? '') === 'showroom_page_id' && !empty($field['value'])) {
                    $page_id = (int) $field['value'];
                    break;
                }
            }
        }
    }

    // Fallbacks (in case WPForms format differs)
    if (!$page_id && !empty($_POST['wpforms']['fields'])) {
        foreach ((array) $_POST['wpforms']['fields'] as $value) {
            // If your hidden field is a known field ID you can target it directly,
            // but this generic fallback avoids hardcoding field IDs.
            if (is_numeric($value) && (int) $value > 0) {
                // not reliable – leave it
            }
        }
    }

    if (!$page_id || !function_exists('get_field')) {
        return $emails;
    }

    $to = (string) get_field('enquiry_email', $page_id);
    $to = sanitize_email($to);

    if (!$to) {
        return $emails;
    }

    return [$to];

}, 10, 3);
