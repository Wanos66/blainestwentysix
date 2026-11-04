function selectedShippingMethod() {
  const checkedRadio = document.querySelector('input[type="radio"][name^="shipping_method"]:checked');

  if (checkedRadio) {
    return checkedRadio.value || '';
  }

  const hiddenInput = document.querySelector('input[type="hidden"][name^="shipping_method"]');

  if (hiddenInput) {
    return hiddenInput.value || '';
  }

  const selectInput = document.querySelector('select[name^="shipping_method"]');

  if (selectInput) {
    return selectInput.value || '';
  }

  const firstInput = document.querySelector('input[name^="shipping_method"]');

  if (firstInput) {
    return firstInput.value || '';
  }

  return '';
}

function isLocalDeliveryMethod(method) {
  return method.startsWith('flat_rate') || method.startsWith('free_shipping');
}

function updatePreferredDeliveryDateField() {
  const card = document.querySelector('.blaines-preferred-delivery-date-card');

  if (!card) {
    return;
  }

  const select = card.querySelector('.blaines-preferred-delivery-date-select');
  const method = selectedShippingMethod();
  const isLocalDelivery = isLocalDeliveryMethod(method);

  card.style.display = isLocalDelivery ? '' : 'none';

  if (select) {
    select.required = isLocalDelivery;

    if (!isLocalDelivery) {
      select.value = '';
    }
  }
}

document.addEventListener('change', (event) => {
  if (event.target && event.target.matches('input[name^="shipping_method"], select[name^="shipping_method"]')) {
    updatePreferredDeliveryDateField();
  }

  if (event.target && event.target.matches('.blaines-preferred-delivery-date-select')) {
    if (window.jQuery) {
      window.jQuery(document.body).trigger('update_checkout');
    }
  }
});

document.addEventListener('DOMContentLoaded', updatePreferredDeliveryDateField);

window.addEventListener('load', updatePreferredDeliveryDateField);

if (window.jQuery) {
  window.jQuery(document.body).on('updated_checkout', updatePreferredDeliveryDateField);
}