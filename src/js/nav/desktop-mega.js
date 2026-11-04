document.addEventListener('DOMContentLoaded', () => {
  const triggers = document.querySelectorAll('[data-has-mega]');

  triggers.forEach(trigger => {

    // Open mega menu on keyboard focus
    trigger.addEventListener('focusin', () => {
      trigger.classList.add('mega-open');
    });

    // Close it when focus leaves
    trigger.addEventListener('focusout', () => {
      setTimeout(() => {
        if (!trigger.contains(document.activeElement)) {
          trigger.classList.remove('mega-open');
        }
      }, 40);
    });

    // ESC closes the menu
    trigger.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        trigger.classList.remove('mega-open');
        const link = trigger.querySelector('a');
        if (link) link.focus();
      }
    });
  });
});

