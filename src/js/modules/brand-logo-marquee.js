export function initBrandLogoMarquees() {
  const roots = document.querySelectorAll('[data-brand-marquee="1"]');
  if (!roots.length) return;

  roots.forEach((root) => {
    const track = root.querySelector("[data-track]");
    if (!track) return;

    const speed = parseInt(root.dataset.speed || "40", 10); // px/sec
    const autoplay = root.dataset.autoplay === "1";

    let last = null;
    let x = 0;
    let paused = !autoplay;
    let half = 0;

    const recompute = () => {
      // scrollWidth can be 0 briefly before layout/images settle
      half = Math.max(1, Math.floor(track.scrollWidth / 2));
    };

    // Pause on hover/focus
    root.addEventListener("mouseenter", () => (paused = true));
    root.addEventListener("mouseleave", () => (paused = !autoplay ? true : false));
    root.addEventListener("focusin", () => (paused = true));
    root.addEventListener("focusout", () => (paused = !autoplay ? true : false));

    // Recompute widths after images load + on resize
    window.addEventListener("load", recompute, { once: true });
    window.addEventListener("resize", recompute);
    recompute();

    function step(ts) {
      if (last == null) last = ts;
      const dt = (ts - last) / 1000;
      last = ts;

      // If half is still 1 (meaning we couldn't measure), keep trying to measure
      if (half <= 1) recompute();

      if (!paused) {
        x -= speed * dt;

        if (Math.abs(x) >= half) x = 0;
        track.style.transform = `translate3d(${x}px,0,0)`;
      }

      window.requestAnimationFrame(step);
    }

    window.requestAnimationFrame(step);
  });
}