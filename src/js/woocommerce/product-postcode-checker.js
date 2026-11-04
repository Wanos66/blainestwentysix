const modal = document.getElementById('blaines-product-postcode-checker');

if (modal) {
  const input = modal.querySelector('#blaines-product-postcode-input');
  const result = modal.querySelector('#blaines-product-postcode-result');
  const actions = modal.querySelector('[data-blaines-postcode-actions]');
  const checkButton = modal.querySelector('[data-blaines-postcode-check]');
  const continueButton = modal.querySelector('[data-blaines-postcode-continue]');
  const callLink = modal.querySelector('[data-blaines-postcode-call]');
  const closeButtons = modal.querySelectorAll('[data-blaines-postcode-close]');

  let pendingButton = null;
  let lastCheckResult = null;

  const storageKey = 'blaines_product_postcode_checked';

  function openModal(button) {
    pendingButton = button;
    lastCheckResult = null;

    resetResult();

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('overflow-hidden');

    window.setTimeout(() => {
      if (input) {
        input.focus();
      }
    }, 50);
  }

  function closeModal() {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('overflow-hidden');
  }

  function resetResult() {
    if (!result || !actions || !continueButton || !callLink) {
      return;
    }

    result.className = 'mt-5 hidden border-l-4 p-4';
    result.textContent = '';

    actions.classList.add('hidden');
    actions.classList.remove('flex');

    continueButton.classList.add('hidden');
    callLink.classList.add('hidden');
    callLink.classList.remove('inline-flex');
  }

  function setLoading(isLoading) {
    if (!checkButton) {
      return;
    }

    checkButton.disabled = isLoading;
    checkButton.textContent = isLoading ? 'Checking...' : 'Check postcode';
  }

  function showResult(data) {
    if (!result || !actions || !continueButton || !callLink) {
      return;
    }

    lastCheckResult = data;

    result.className = 'mt-5 border-l-4 p-4';
    result.textContent = data.message || '';

    if (data.status === 'delivery') {
      result.classList.add('border-green-700', 'bg-green-50', 'text-neutral-800');

      continueButton.textContent = 'Continue and add to basket';
      continueButton.classList.remove('hidden');

      actions.classList.remove('hidden');
      actions.classList.add('flex');
    } else if (data.status === 'collection') {
      result.classList.add('border-blue-700', 'bg-blue-50', 'text-neutral-800');

      continueButton.textContent = 'Continue for collection';
      continueButton.classList.remove('hidden');

      callLink.classList.remove('hidden');
      callLink.classList.add('inline-flex');

      actions.classList.remove('hidden');
      actions.classList.add('flex');
    } else {
      result.classList.add('border-red-700', 'bg-red-50', 'text-neutral-800');

      callLink.classList.remove('hidden');
      callLink.classList.add('inline-flex');

      actions.classList.remove('hidden');
      actions.classList.add('flex');
    }
  }

  async function checkPostcode() {
    if (!input) {
      return;
    }

    const postcode = input.value.trim();

    if (!postcode) {
      showResult({
        status: 'invalid',
        message: 'Please enter your full postcode so we can check delivery availability.',
      });

      return;
    }

    const formData = new FormData();

    formData.append('action', 'blaines_check_product_postcode');
    formData.append('nonce', modal.dataset.nonce || '');
    formData.append('postcode', postcode);

    setLoading(true);

    try {
      const response = await fetch(modal.dataset.ajaxUrl || '', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      });

      const data = await response.json();

      showResult(data);
    } catch (error) {
      showResult({
        status: 'unavailable',
        message: 'We could not check delivery availability at this time. Please contact Web Sales if you need help.',
      });
    } finally {
      setLoading(false);
    }
  }

  function continueAddToBasket() {
    if (!pendingButton || !lastCheckResult) {
      closeModal();
      return;
    }

    if (lastCheckResult.status !== 'delivery' && lastCheckResult.status !== 'collection') {
      return;
    }

    try {
      window.sessionStorage.setItem(storageKey, 'yes');
      window.sessionStorage.setItem(
          'blaines_product_postcode_status',
          lastCheckResult.status || ''
      );
      window.sessionStorage.setItem(
          'blaines_product_postcode',
          lastCheckResult.postcode || ''
      );
    } catch (error) {
      // Session storage is optional; continue without it.
    }

    pendingButton.dataset.blainesPostcodeChecked = 'yes';

    closeModal();

    window.setTimeout(() => {
      pendingButton.click();
    }, 50);
  }

  function postcodeAlreadyChecked() {
    try {
      return window.sessionStorage.getItem(storageKey) === 'yes';
    } catch (error) {
      return false;
    }
  }

  document.addEventListener('click', (event) => {
    const button = event.target.closest('.single_add_to_cart_button');

    if (!button) {
      return;
    }

    const form = button.closest('form.cart');

    if (!form) {
      return;
    }

    if (
        button.dataset.blainesPostcodeChecked === 'yes'
        || postcodeAlreadyChecked()
    ) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    openModal(button);
  }, true);

  if (checkButton) {
    checkButton.addEventListener('click', checkPostcode);
  }

  if (continueButton) {
    continueButton.addEventListener('click', continueAddToBasket);
  }

  if (input) {
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        checkPostcode();
      }
    });
  }

  closeButtons.forEach((button) => {
    button.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
      closeModal();
    }
  });
}