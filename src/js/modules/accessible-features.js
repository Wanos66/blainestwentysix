export function initAccessibleFeatures() {
  document.documentElement.classList.add('js');

  const sections = document.querySelectorAll('[data-accessibility-features-section]');

  sections.forEach((section) => {
    const toggleButton = section.querySelector('[data-feature-toggle]');
    const gridWrap = section.querySelector('[data-feature-grid-wrap]');
    const items = Array.from(section.querySelectorAll('.feature-item'));

    if (!toggleButton || !gridWrap || items.length <= 4) {
      return;
    }

    const showMoreText = toggleButton.dataset.showMoreText || 'View All Features';
    const showLessText = toggleButton.dataset.showLessText || 'Show Less';
    const initialVisible = 4;

    let expanded = false;

    function getCollapsedHeight() {
      const visibleItems = items.slice(0, initialVisible);
      const wrapTop = gridWrap.getBoundingClientRect().top;

      let maxBottom = 0;

      visibleItems.forEach((item) => {
        const itemBottom = item.getBoundingClientRect().bottom - wrapTop;

        if (itemBottom > maxBottom) {
          maxBottom = itemBottom;
        }
      });

      return Math.ceil(maxBottom);
    }

    function applyState() {
      if (expanded) {
        gridWrap.style.maxHeight = `${gridWrap.scrollHeight}px`;
        toggleButton.textContent = showLessText;
        toggleButton.setAttribute('aria-expanded', 'true');
      } else {
        gridWrap.style.maxHeight = `${getCollapsedHeight()}px`;
        toggleButton.textContent = showMoreText;
        toggleButton.setAttribute('aria-expanded', 'false');
      }
    }

    applyState();

    toggleButton.addEventListener('click', () => {
      expanded = !expanded;
      applyState();
    });

    window.addEventListener('resize', applyState);
  });
}