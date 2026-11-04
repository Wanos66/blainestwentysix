<?php
/**
 * Blaines WooCommerce checkout customisations.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_checkout_make_phone_required' ) ) {
    /**
     * Make the checkout billing phone field required.
     *
     * @param array<string, mixed> $fields Checkout fields.
     * @return array<string, mixed>
     */
    function blaines_checkout_make_phone_required( array $fields ): array {
        if ( isset( $fields['billing']['billing_phone'] ) ) {
            $fields['billing']['billing_phone']['required'] = true;
            $fields['billing']['billing_phone']['label']    = 'Phone';
            $fields['billing']['billing_phone']['class']    = [ 'form-row-wide' ];
            $fields['billing']['billing_phone']['priority'] = 100;
        }

        return $fields;
    }

    add_filter( 'woocommerce_checkout_fields', 'blaines_checkout_make_phone_required', 9999 );
}

if ( ! function_exists( 'blaines_checkout_make_phone_required_billing_field' ) ) {
    /**
     * Make the billing phone field required at billing-field level too.
     *
     * @param array<string, mixed> $fields Billing fields.
     * @return array<string, mixed>
     */
    function blaines_checkout_make_phone_required_billing_field( array $fields ): array {
        if ( isset( $fields['billing_phone'] ) ) {
            $fields['billing_phone']['required'] = true;
            $fields['billing_phone']['label']    = 'Phone';
            $fields['billing_phone']['class']    = [ 'form-row-wide' ];
            $fields['billing_phone']['priority'] = 100;
        }

        return $fields;
    }

    add_filter( 'woocommerce_billing_fields', 'blaines_checkout_make_phone_required_billing_field', 9999 );
}


if ( ! function_exists( 'blaines_checkout_phone_required_label_display' ) ) {
    /**
     * Force the billing phone label to display as required.
     *
     * This is a display cleanup only. Server-side validation is handled separately.
     *
     * @param string              $field Rendered field HTML.
     * @param string              $key   Field key.
     * @param array<string,mixed> $args  Field args.
     * @param string|null         $value Field value.
     * @return string
     */
    function blaines_checkout_phone_required_label_display( string $field, string $key, array $args, $value ): string {
        if ( 'billing_phone' !== $key ) {
            return $field;
        }

        // Remove WooCommerce's optional marker if another filter has left it in place.
        $field = preg_replace(
            '/<span class="optional">\s*\(optional\)\s*<\/span>/i',
            '',
            $field
        );

        // Add a required marker if the rendered label does not already contain one.
        if (
            false === strpos( $field, 'class="required"' )
            && false !== strpos( $field, '</label>' )
        ) {
            $field = str_replace(
                '</label>',
                ' <abbr class="required" title="required">*</abbr></label>',
                $field
            );
        }

        return $field;
    }

    add_filter( 'woocommerce_form_field', 'blaines_checkout_phone_required_label_display', 9999, 4 );
}

if ( ! function_exists( 'blaines_checkout_phone_required_display_script' ) ) {
    /**
     * Keep the billing phone label visually required, remove duplicate global phone notice,
     * and return the customer to the Phone field after WooCommerce checkout validation.
     *
     * @return void
     */
    function blaines_checkout_phone_required_display_script(): void {
        if (
            ! function_exists( 'is_checkout' )
            || ! is_checkout()
            || is_order_received_page()
        ) {
            return;
        }
        ?>

        <script>
          (function () {
            function blainesNormaliseText(text) {
              return (text || '').replace(/\s+/g, ' ').trim();
            }

            function blainesSetPhoneRequiredLabel() {
              var phoneField = document.getElementById('billing_phone_field');

              if (!phoneField) {
                return;
              }

              var label = phoneField.querySelector('label[for="billing_phone"]');

              if (!label) {
                return;
              }

              var optional = label.querySelector('.optional');

              if (optional) {
                optional.remove();
              }

              if (!label.querySelector('.required')) {
                var required = document.createElement('abbr');
                required.className = 'required';
                required.title = 'required';
                required.textContent = '*';

                label.appendChild(document.createTextNode(' '));
                label.appendChild(required);
              }
            }

            function blainesRemoveDuplicatePhoneNotice() {
              var phoneField = document.getElementById('billing_phone_field');

              if (!phoneField) {
                return;
              }

              var inlineText = blainesNormaliseText(phoneField.textContent);

              if (inlineText.indexOf('Billing Phone is a required field.') === -1) {
                return;
              }

              var noticeGroups = document.querySelectorAll(
                  '.woocommerce-NoticeGroup-checkout, .woocommerce-notices-wrapper'
              );

              noticeGroups.forEach(function (group) {
                var listItems = group.querySelectorAll('li');

                listItems.forEach(function (item) {
                  var itemText = blainesNormaliseText(item.textContent);

                  if (itemText === 'Billing Phone is a required field.') {
                    var parent = item.parentElement;

                    item.remove();

                    if (parent && parent.children.length === 0) {
                      parent.remove();
                    }
                  }
                });

                var groupText = blainesNormaliseText(group.textContent);

                if (
                    groupText === ''
                    || groupText === 'Billing Phone is a required field.'
                ) {
                  group.remove();
                }
              });
            }

            function blainesPhoneHasInlineError() {
              var phoneField = document.getElementById('billing_phone_field');

              if (!phoneField) {
                return false;
              }

              return blainesNormaliseText(phoneField.textContent).indexOf(
                  'Billing Phone is a required field.'
              ) !== -1;
            }

            function blainesScrollToPhoneField() {
              var phoneField = document.getElementById('billing_phone_field');

              if (!phoneField || !blainesPhoneHasInlineError()) {
                return;
              }

              var top = phoneField.getBoundingClientRect().top + window.pageYOffset - 140;

              window.scrollTo({
                top: top,
                behavior: 'smooth'
              });
            }

            function blainesPhoneNoticeIsPresent() {
              var noticeText = '';

              document.querySelectorAll('.woocommerce-NoticeGroup-checkout, .woocommerce-notices-wrapper').forEach(function (group) {
                noticeText += ' ' + blainesNormaliseText(group.textContent);
              });

              return (
                  noticeText.indexOf('Billing Phone is a required field.') !== -1
                  || blainesPhoneHasInlineError()
              );
            }

            function blainesPatchWooCommerceNoticeScroll() {
              if (typeof window.jQuery === 'undefined') {
                return;
              }

              var $ = window.jQuery;

              if (
                  typeof $.scroll_to_notices !== 'function'
                  || $.blainesPhoneNoticeScrollPatched
              ) {
                return;
              }

              var originalScrollToNotices = $.scroll_to_notices;

              $.scroll_to_notices = function () {
                if (blainesPhoneNoticeIsPresent()) {
                  setTimeout(blainesCheckoutDisplayCleanup, 50);
                  setTimeout(blainesScrollToPhoneField, 100);
                  setTimeout(blainesScrollToPhoneField, 350);

                  return;
                }

                return originalScrollToNotices.apply(this, arguments);
              };

              $.blainesPhoneNoticeScrollPatched = true;
            }

            function blainesCheckoutDisplayCleanup() {
              blainesPatchWooCommerceNoticeScroll();
              blainesSetPhoneRequiredLabel();
              blainesRemoveDuplicatePhoneNotice();
            }

            document.addEventListener('DOMContentLoaded', blainesCheckoutDisplayCleanup);

            document.body.addEventListener('updated_checkout', function () {
              setTimeout(blainesCheckoutDisplayCleanup, 50);
            });

            document.body.addEventListener('checkout_error', function () {
              setTimeout(blainesCheckoutDisplayCleanup, 50);
              setTimeout(blainesCheckoutDisplayCleanup, 250);

              /*
               * WooCommerce scrolls to its global notice area after validation.
               * Let it finish, then move the customer back to the inline Phone error.
               */
              setTimeout(blainesScrollToPhoneField, 700);
            });

            var observer = new MutationObserver(function () {
              blainesCheckoutDisplayCleanup();
            });

            observer.observe(document.body, {
              childList: true,
              subtree: true
            });

            blainesCheckoutDisplayCleanup();
          })();
        </script>

        <?php
    }

    add_action( 'wp_footer', 'blaines_checkout_phone_required_display_script', 99 );
}
