<?php
/**
 * Consent-controlled Tawk.to integration.
 *
 * Tawk.to is loaded after Complianz Marketing consent.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', function () {

    // Only output when Complianz is available.
    if ( ! shortcode_exists( 'cmplz-document' ) ) {
        return;
    }

    ?>

    <script
            id="blaines-tawk-consent-script"
            type="text/plain"
            class="cmplz-script"
            data-category="marketing"
    >
        window.Tawk_API = window.Tawk_API || {};
        window.Tawk_LoadStart = new Date();

        (function () {
            if (document.getElementById('blaines-tawk-loader')) {
                return;
            }

            var script = document.createElement('script');

            script.id = 'blaines-tawk-loader';
            script.async = true;
            script.src = 'https://embed.tawk.to/594a6559e9c6d324a4736966/default';
            script.charset = 'UTF-8';
            script.setAttribute('crossorigin', '*');

            document.head.appendChild(script);
        })();
    </script>


    <script id="blaines-tawk-consent-cleanup">
      (function () {
        'use strict';

        function clearTawkCookies() {
          var names = document.cookie.split(';');

          names.forEach(function (entry) {
            var name = entry.split('=')[0].trim();

            if (
                name !== 'TawkConnectionTime' &&
                name !== 'twk_idm_key' &&
                !name.startsWith('twk_uuid_')
            ) {
              return;
            }

            var domains = [
              '',
              'staging9.blaines.co.uk',
              '.blaines.co.uk',
              'blaines.co.uk'
            ];

            domains.forEach(function (domain) {
              var cookie = encodeURIComponent(name) +
                  '=; Max-Age=0; path=/; SameSite=Lax';

              if (domain) {
                cookie += '; domain=' + domain;
              }

              document.cookie = cookie;
            });
          });
        }

        document.addEventListener('cmplz_status_change', function () {
          if (
              typeof cmplz_has_consent === 'function' &&
              cmplz_has_consent('marketing')
          ) {
            return;
          }

          if (
              window.Tawk_API &&
              typeof window.Tawk_API.shutdown === 'function'
          ) {
            window.Tawk_API.shutdown();
          }

          clearTawkCookies();
        });
      })();
    </script>

    <?php
}, 20 );
