document.addEventListener('DOMContentLoaded', () => {
  const topLevelItems = document.querySelectorAll(
      'nav[aria-label="Primary menu"] > ul > li.menu-item-depth-0'
  );

  if (topLevelItems.length === 0) return;

  topLevelItems.forEach((item, index) => {
    const link = item.querySelector('a');

    if (!link) return;

    link.addEventListener('keydown', (e) => {
      const isMega = item.classList.contains('has-mega');
      const submenu = item.querySelector('.sub-menu');

      switch (e.key) {

          /* ---------------------------------
             ARROW RIGHT — move to next item
          ----------------------------------- */
        case 'ArrowRight': {
          e.preventDefault();
          const nextItem = topLevelItems[(index + 1) % topLevelItems.length];
          nextItem.querySelector('a').focus();
          break;
        }

          /* ---------------------------------
             ARROW LEFT — move to previous item
          ----------------------------------- */
        case 'ArrowLeft': {
          e.preventDefault();
          const prevItem =
              topLevelItems[(index - 1 + topLevelItems.length) % topLevelItems.length];
          prevItem.querySelector('a').focus();
          break;
        }

          /* ---------------------------------
             ARROW DOWN — open submenu
          ----------------------------------- */
        case 'ArrowDown': {
          if (submenu) {
            e.preventDefault();

            // Mark submenu as keyboard-open
            item.classList.add('mega-open');
            link.setAttribute('aria-expanded', 'true');

            // Focus first item inside submenu
            const firstChildLink = submenu.querySelector('a');
            if (firstChildLink) firstChildLink.focus();
          }
          break;
        }

          /* ---------------------------------
             ENTER / SPACE — toggle submenu
          ----------------------------------- */
        case 'Enter':
        case ' ': {

          if (!submenu) return;

          e.preventDefault();

          const isOpen = item.classList.contains('mega-open');

          item.classList.toggle('mega-open', !isOpen);
          link.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

          if (!isOpen) {
            // Focus first submenu item
            const firstChild = submenu.querySelector('a');
            if (firstChild) firstChild.focus();
          }

          break;
        }

          /* ---------------------------------
             ESC — close submenu
          ----------------------------------- */
        case 'Escape': {
          if (submenu) {
            item.classList.remove('mega-open');
            link.setAttribute('aria-expanded', 'false');
            link.focus();
          }
          break;
        }
      }
    });
  });

  /* ---------------------------------
     Submenu ↑/↓ navigation
  ----------------------------------- */
  document.addEventListener('keydown', (e) => {
    const current = document.activeElement;
    if (!current || current.tagName !== 'A') return;

    const parentLi = current.closest('li');
    const submenu = parentLi?.parentElement;

    if (!submenu || !submenu.classList.contains('sub-menu')) return;

    const items = [...submenu.querySelectorAll('a')];
    const index = items.indexOf(current);

    switch (e.key) {

      case 'ArrowDown': {
        e.preventDefault();
        const next = items[(index + 1) % items.length];
        next.focus();
        break;
      }

      case 'ArrowUp': {
        e.preventDefault();
        const prev = items[(index - 1 + items.length) % items.length];
        prev.focus();
        break;
      }

      case 'Escape': {
        e.preventDefault();

        // Close menu and return to parent
        const containingMega = parentLi.closest('.menu-item-depth-0.has-mega');
        if (containingMega) {
          containingMega.classList.remove('mega-open');
          const parentLink = containingMega.querySelector('a');
          if (parentLink) {
            parentLink.setAttribute('aria-expanded', 'false');
            parentLink.focus();
          }
        }
        break;
      }
    }
  });
});
