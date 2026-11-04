/**
 * Transactional product promotion enhancements.
 *
 * Opens the Promotions <details> row when:
 * - the product page loads with #product-promotions;
 * - the hash changes to #product-promotions; or
 * - a same-page "Promotion details" link is selected.
 */

const promotionTargetId = 'product-promotions';

const getPromotionTarget = () => {
  const element = document.getElementById(promotionTargetId);

  return element instanceof HTMLDetailsElement ? element : null;
};

const openPromotionTarget = ({ scroll = true } = {}) => {
  if (window.location.hash !== `#${promotionTargetId}`) {
    return;
  }

  const target = getPromotionTarget();

  if (!target) {
    return;
  }

  target.open = true;

  if (scroll) {
    window.requestAnimationFrame(() => {
      target.scrollIntoView({ block: 'start' });
    });
  }
};

const handlePromotionLinkClick = (event) => {
  if (!(event.target instanceof Element)) {
    return;
  }

  const link = event.target.closest(
      `a[href*="#${promotionTargetId}"]`
  );

  if (!(link instanceof HTMLAnchorElement)) {
    return;
  }

  let destination;

  try {
    destination = new URL(link.href, window.location.href);
  } catch (error) {
    return;
  }

  const isCurrentPage =
      destination.origin === window.location.origin
      && destination.pathname === window.location.pathname
      && destination.search === window.location.search;

  if (!isCurrentPage) {
    return;
  }

  const target = getPromotionTarget();

  if (target) {
    target.open = true;
  }
};

const initTransactionalPromotions = () => {
  document.addEventListener(
      'click',
      handlePromotionLinkClick
  );

  window.addEventListener(
      'hashchange',
      () => openPromotionTarget()
  );

  openPromotionTarget();
};

if (document.readyState === 'loading') {
  document.addEventListener(
      'DOMContentLoaded',
      initTransactionalPromotions,
      { once: true }
  );
} else {
  initTransactionalPromotions();
}