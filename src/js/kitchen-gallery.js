export default function kitchenGallery(images) {
  return {
    images,
    activeIndex: 0,
    active: {},

    init() {
      if (!this.images || !this.images.length) return;

      const first = this.images[0];

      this.active = {
        url: first.url,
        alt: first.alt || '',
        caption: first.caption || first.alt || ''
      };
      this.activeIndex = 0;
    },

    setActive(index) {
      const img = this.images[index];

      this.active = {
        url: img.url,
        alt: img.alt,
        caption: img.caption || img.alt || ''
      };

      this.activeIndex = index;
    },

    openFancybox() {
      if (typeof Fancybox === "undefined") {
        console.error("Fancybox not loaded!");
        return;
      }

      Fancybox.show(
          this.images.map(img => ({
            src: img.url,
            type: "image",
            caption: img.caption || img.alt || ""
          })),
          {
            startIndex: this.activeIndex,

            Toolbar: {
              display: ["counter", "prev", "next", "zoom", "fullscreen", "close"],
            },

            Thumbs: {
              autoStart: false
            },

            animated: true,
          }
      );
    }
  };
}
