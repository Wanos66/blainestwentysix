const cartPostcodeCheckers = document.querySelectorAll('[data-blaines-cart-postcode-checker]');

cartPostcodeCheckers.forEach((checker) => {
  const input = checker.querySelector('[data-blaines-cart-postcode-input]');
  const button = checker.querySelector('[data-blaines-cart-postcode-submit]');
  const result = checker.querySelector('[data-blaines-cart-postcode-result]');

  if (!input || !button || !result) {
    return;
  }

  function setLoading(isLoading) {
    button.disabled = isLoading;
    button.textContent = isLoading ? 'Checking...' : 'Check postcode';
  }

  function showMessage(message, status = 'invalid') {
    result.className = 'mt-3 border-l-4 p-3 text-sm font-light leading-relaxed';
    result.textContent = message || '';

    if (status === 'delivery') {
      result.classList.add('border-green-700', 'bg-green-50', 'text-neutral-800');
    } else if (status === 'collection') {
      result.classList.add('border-blue-700', 'bg-blue-50', 'text-neutral-800');
    } else {
      result.classList.add('border-red-700', 'bg-red-50', 'text-neutral-800');
    }
  }

  function storePostcodeResult(data) {
    try {
      window.sessionStorage.setItem('blaines_product_postcode_checked', 'yes');
      window.sessionStorage.setItem('blaines_product_postcode_status', data.status || '');
      window.sessionStorage.setItem('blaines_product_postcode', data.postcode || '');
    } catch (error) {
      // Browser session storage is helpful but not required.
    }
  }

  async function checkPostcode() {
    const postcode = input.value.trim();

    if (!postcode) {
      showMessage('Please enter your full postcode so we can check delivery availability.');
      return;
    }

    const formData = new FormData();

    formData.append('action', 'blaines_check_product_postcode');
    formData.append('nonce', checker.dataset.nonce || '');
    formData.append('postcode', postcode);

    setLoading(true);

    try {
      const response = await fetch(checker.dataset.ajaxUrl || '', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      });

      const data = await response.json();

      if (!data || !data.status) {
        showMessage('We could not check delivery availability at this time. Please contact Web Sales if you need help.');
        return;
      }

      showMessage(data.message || '', data.status);

      if (data.status === 'delivery' || data.status === 'collection') {
        storePostcodeResult(data);

        window.setTimeout(() => {
          window.location.reload();
        }, 900);
      }
    } catch (error) {
      showMessage('We could not check delivery availability at this time. Please contact Web Sales if you need help.');
    } finally {
      setLoading(false);
    }
  }

  button.addEventListener('click', checkPostcode);

  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      checkPostcode();
    }
  });
});