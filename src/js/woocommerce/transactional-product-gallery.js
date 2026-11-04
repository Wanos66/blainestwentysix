/**
 * Transactional WooCommerce product gallery.
 *
 * Registered as a global Alpine data factory because the PHP template uses
 * x-data="transactionalProductGallery(...)".
 */
window.transactionalProductGallery = (galleryImages = []) => ({
    images: Array.isArray(galleryImages) ? galleryImages : [],
    activeIndex: 0,

    get active() {
        return this.images[this.activeIndex] || {
            display: '',
            full: '',
            thumb: '',
            alt: '',
            caption: '',
        };
    },

    init() {
        if (this.images.length === 0) {
            this.activeIndex = 0;
        }
    },

    setActive(index) {
        if (this.images.length === 0) {
            return;
        }

        const nextIndex = Number(index);

        if (!Number.isInteger(nextIndex)) {
            return;
        }

        this.activeIndex = Math.min(
            Math.max(nextIndex, 0),
            this.images.length - 1
        );

        this.$nextTick(() => this.scrollActiveThumbnailIntoView());
    },

    previous() {
        if (this.images.length < 2) {
            return;
        }

        const previousIndex =
            (this.activeIndex - 1 + this.images.length) % this.images.length;

        this.setActive(previousIndex);
    },

    next() {
        if (this.images.length < 2) {
            return;
        }

        const nextIndex = (this.activeIndex + 1) % this.images.length;

        this.setActive(nextIndex);
    },

    scrollActiveThumbnailIntoView() {
        if (!(this.$refs.thumbTrack instanceof HTMLElement)) {
            return;
        }

        const activeThumbnail = this.$refs.thumbTrack.querySelector(
            `[data-gallery-thumb="${this.activeIndex}"]`
        );

        if (!(activeThumbnail instanceof HTMLElement)) {
            return;
        }

        activeThumbnail.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'nearest',
        });
    },

    openFancybox() {
        if (this.images.length === 0) {
            return;
        }

        const slides = this.images.map((image) => ({
            src: image.full || image.display,
            type: 'image',
            caption: image.caption || '',
        }));

        if (
            window.Fancybox
            && typeof window.Fancybox.show === 'function'
        ) {
            window.Fancybox.show(slides, {
                startIndex: this.activeIndex,
            });

            return;
        }

        const fallbackUrl = this.active.full || this.active.display;

        if (fallbackUrl) {
            window.open(fallbackUrl, '_blank', 'noopener,noreferrer');
        }
    },
});
