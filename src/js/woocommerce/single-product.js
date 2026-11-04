export function initClearanceGallery() {
  document.addEventListener('click', (event) => {
    const thumb = event.target.closest('[data-clearance-gallery-thumb]');
    if (!thumb) return;

    const gallery = thumb.closest('[data-clearance-gallery]');
    if (!gallery) return;

    const mainImage = gallery.querySelector('[data-clearance-gallery-main]');
    if (!mainImage) return;

    const newSrc = thumb.getAttribute('data-image-src');
    const newAlt = thumb.getAttribute('data-image-alt') || '';

    if (newSrc) {
      mainImage.setAttribute('src', newSrc);
      mainImage.setAttribute('alt', newAlt);
    }

    gallery.querySelectorAll('[data-clearance-gallery-thumb]').forEach((button) => {
      button.classList.remove('is-active', 'border-neutral-900');
      button.classList.add('border-neutral-200');
    });

    thumb.classList.add('is-active', 'border-neutral-900');
    thumb.classList.remove('border-neutral-200');
  });

}
