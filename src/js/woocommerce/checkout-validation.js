/**
 * CP1B.1 — Classic checkout terms validation (Stripe, Klarna and PayPal).
 * WooCommerce's server-side terms validation remains authoritative.
 */
(function () {
  'use strict';

  const ERROR_ID = 'blaines-checkout-terms-error';
  const OVERLAY_CLASS = 'blaines-paypal-terms-guard';
  const MESSAGE = 'Please accept the Terms and Conditions before placing your order.';

  const form = () => document.querySelector('form.checkout.woocommerce-checkout');
  const terms = () => form()?.querySelector('input#terms[name="terms"]');

  function clearError() {
    const input = terms();
    document.getElementById(ERROR_ID)?.remove();
    input?.removeAttribute('aria-invalid');
    input?.removeAttribute('aria-describedby');
  }

  function validateTerms() {
    const input = terms();
    if (!input || input.checked || input.disabled) {
      clearError();
      return true;
    }

    const wrapper = input.closest('.woocommerce-terms-and-conditions-wrapper');
    const label = input.closest('label');
    if (!wrapper || !label) return true;

    let message = document.getElementById(ERROR_ID);
    if (!message) {
      message = document.createElement('p');
      message.id = ERROR_ID;
      message.className = 'blaines-checkout-terms-error';
      message.setAttribute('role', 'alert');
      message.textContent = MESSAGE;
      label.insertAdjacentElement('afterend', message);
    }

    input.setAttribute('aria-invalid', 'true');
    input.setAttribute('aria-describedby', ERROR_ID);
    input.focus({ preventScroll: true });
    return false;
  }

  // Standard checkout buttons, including Stripe and Klarna.
  document.addEventListener('click', (event) => {
    if (!event.target.closest('form.checkout #place_order')) return;
    if (!validateTerms()) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
  }, true);

  document.addEventListener('submit', (event) => {
    if (!event.target.matches('form.checkout.woocommerce-checkout')) return;
    if (!validateTerms()) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
  }, true);

  // PayPal Smart Buttons are inside an iframe: clicks in that iframe do not
  // bubble to the checkout. A transparent, accessible guard over the hosted
  // button is used ONLY while the terms checkbox is unticked. It is removed
  // immediately when the buyer accepts the terms.
  function updatePayPalGuard() {
    const input = terms();
    const gateway = form()?.querySelector('li.payment_method_ppcp-gateway');
    if (!gateway) return;

    const paypalSelected = !!gateway.querySelector('input[name="payment_method"]:checked');
    const blocked = !!input && !input.checked && !input.disabled && paypalSelected;
    const hosts = gateway.querySelectorAll('[id^="ppc-button-ppcp-gateway"]');

    hosts.forEach((host) => {
      const guard = Array.from(host.children).find((child) => child.classList.contains(OVERLAY_CLASS));
      if (!blocked) {
        guard?.remove();
        host.classList.remove('blaines-paypal-guarded');
        return;
      }
      host.classList.add('blaines-paypal-guarded');
      if (guard) return;

      const button = document.createElement('button');
      button.type = 'button';
      button.className = OVERLAY_CLASS;
      button.setAttribute('aria-label', 'Accept the terms and conditions before paying with PayPal');
      button.addEventListener('click', (event) => {
        event.preventDefault();
        validateTerms();
      });
      host.appendChild(button);
    });
  }

  document.addEventListener('change', (event) => {
    if (event.target.matches('form.checkout input#terms[name="terms"]')) {
      if (event.target.checked) clearError();
      updatePayPalGuard();
    }
    if (event.target.matches('form.checkout input[name="payment_method"]')) {
      updatePayPalGuard();
    }
  });

  function init() {
    updatePayPalGuard();
    if (window.jQuery) {
      window.jQuery(document.body).on('checkout_place_order.blainesTermsValidation', () => validateTerms());
      window.jQuery(document.body).on('updated_checkout.blainesTermsValidation payment_method_selected.blainesTermsValidation', updatePayPalGuard);
    }

    // PayPal may render/re-render its iframe after WooCommerce updates checkout.
    const payment = form()?.querySelector('#payment');
    if (payment && window.MutationObserver) {
      const observer = new MutationObserver(updatePayPalGuard);
      observer.observe(payment, { childList: true, subtree: true });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
