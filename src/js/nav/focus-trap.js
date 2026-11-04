/**
 * Shared focus-trap utilities for desktop & mobile navigation.
 *
 * NOTE: Not yet imported anywhere – safe to add now.
 */

export function trapFocus(container) {
  if (!container) return;

  const focusableSelectors = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])'
  ];

  const elements = container.querySelectorAll(focusableSelectors.join(','));
  const first = elements[0];
  const last = elements[elements.length - 1];

  function handleKey(e) {
    if (e.key !== 'Tab') return;

    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }

  container.addEventListener('keydown', handleKey);

  return () => {
    container.removeEventListener('keydown', handleKey);
  };
}

export function focusFirst(container) {
  if (!container) return;
  const first = container.querySelector(
      'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
  );
  if (first) first.focus();
}
