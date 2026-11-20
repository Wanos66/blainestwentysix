const $ = window.jQuery;
const formSelector = 'form.retail-basket-form';
const quantitySelector = 'input.qty[name^="cart["]';
const serviceSelector = 'input[type="checkbox"][name^="blaines_fulfilment_services["]';
const postcodeButtonSelector = '[data-blaines-cart-postcode-submit]';
const basketActionSelector = [
  '.wc-proceed-to-checkout a',
  '.retail-basket-item__remove a',
  '.woocommerce-remove-coupon',
  '[name="apply_coupon"]',
  postcodeButtonSelector,
].join(', ');

if ($ && document.querySelector(formSelector)) {
  $(() => {
    if (!window.wc_cart_params) return;

    const status = document.querySelector('[data-blaines-cart-update-status]');
    if (!status) return;

    let timer;
    let changedInput;
    let savingForm;
    let request;
    let submitting = false;
    let automaticUpdateFailed = false;

    function showStatus(message) {
      status.textContent = message;
      status.hidden = !message;
    }

    function queueUpdate(delay) {
      window.clearTimeout(timer);
      timer = window.setTimeout(updateBasket, delay);
    }

    function updateBasket() {
      const form = document.querySelector(formSelector);
      const button = form?.querySelector('button[name="update_cart"]');

      if (!changedInput || !button || savingForm || automaticUpdateFailed) return;
      if (form.classList.contains('processing') || form.querySelector(postcodeButtonSelector + ':disabled')) {
        queueUpdate(200);
        return;
      }

      const emptyQuantity = [...form.querySelectorAll(quantitySelector)].find((input) => input.value === '');
      if (emptyQuantity || !form.checkValidity()) {
        showStatus('Check your quantities to update the basket.');
        return;
      }

      savingForm = form;
      showStatus('Updating basket…');
      submitting = true;
      button.disabled = false;
      button.click();
      submitting = false;

      if (request) {
        form.inert = true;
      } else {
        savingForm = undefined;
        automaticUpdateFailed = true;
        showStatus('Please use Update basket to save your changes.');
      }
    }

    $(document).on('ajaxSend.blainesCartAutoUpdate', (_event, xhr, settings) => {
      if (!submitting || !savingForm || !settings.data) return;

      const data = new URLSearchParams(settings.data);
      if (data.has('update_cart') && data.has('woocommerce-cart-nonce')) {
        request = xhr;
      }
    });

    $(document).on('ajaxComplete.blainesCartAutoUpdate', (_event, xhr) => {
      if (xhr !== request || !savingForm) return;

      const form = document.querySelector(formSelector);
      const previousInput = changedInput;
      savingForm.inert = false;
      savingForm = undefined;
      request = undefined;

      if (xhr.status < 200 || xhr.status >= 300 || (form && form.contains(previousInput))) {
        automaticUpdateFailed = true;
        const button = form?.querySelector('button[name="update_cart"]');
        if (button) button.disabled = false;
        showStatus('Basket update could not be confirmed. Please use Update basket.');
        return;
      }

      automaticUpdateFailed = false;
      changedInput = undefined;
      showStatus('');

      // Put keyboard focus back on the matching control after WooCommerce replaces the form.
      if (document.activeElement === document.body && form && previousInput) {
        const replacement = Array.from(form.elements).find((input) => (
          input.name === previousInput.name
          && (!input.matches(serviceSelector) || input.value === previousInput.value)
        ));
        replacement?.focus({ preventScroll: true });
      }
    });

    document.addEventListener('input', (event) => {
      const input = event.target;
      if (input.matches(quantitySelector) && input.closest(formSelector)) {
        changedInput = input;
        queueUpdate(550);
      }
    });

    document.addEventListener('change', (event) => {
      const input = event.target;
      if (input.matches(serviceSelector) && input.closest(formSelector)) {
        changedInput = input;
        queueUpdate(0);
      }
    });

    document.addEventListener('click', (event) => {
      const target = event.target.closest('a, button');
      if (!target?.closest('.blaines-retail--basket') || submitting) return;

      if (changedInput && target.matches(formSelector + ' [name="update_cart"], ' + basketActionSelector)) {
        event.preventDefault();
        event.stopImmediatePropagation();
        window.clearTimeout(timer);

        if (automaticUpdateFailed && target.matches('[name="update_cart"]')) {
          const form = target.form;
          if (!form.reportValidity()) return;
          const submitter = document.createElement('input');
          submitter.type = 'hidden';
          submitter.name = 'update_cart';
          submitter.value = target.value;
          form.append(submitter);
          showStatus('Updating basket…');
          HTMLFormElement.prototype.submit.call(form);
          return;
        }

        updateBasket();
      }
    }, true);

    document.addEventListener('keydown', (event) => {
      const input = event.target;
      if (!changedInput || event.key !== 'Enter' || !input.closest(formSelector)) return;
      if (!input.matches(quantitySelector + ', [data-blaines-cart-postcode-input], #coupon_code')) return;
      event.preventDefault();
      event.stopImmediatePropagation();
      window.clearTimeout(timer);
      updateBasket();
    }, true);

    document.addEventListener('blaines:cart-postcode-reload', (event) => {
      if (changedInput) {
        event.preventDefault();
        queueUpdate(0);
      }
    });
  });
}
