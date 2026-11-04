export function slidefun() {
  const slider = document.getElementById("slider");
  const container = document.getElementById("acfSlider");

  // If there is no slider on this page, do nothing
  if (!slider || !container) return;

  const slides = Array.from(slider.querySelectorAll("[data-index]"));
  const dots = Array.from(container.querySelectorAll("[data-dot]"));
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  const total = slides.length;
  if (total <= 1) {
    // Ensure first slide is visible and interactive if markup/classes got out of sync
    if (slides[0]) {
      slides[0].classList.add("opacity-100", "pointer-events-auto");
      slides[0].classList.remove("opacity-0", "pointer-events-none");
      slides[0].setAttribute("aria-hidden", "false");
    }
    return;
  }

  let current = 0;
  let autoplayInterval = null;
  const delayAttr = Number(container.getAttribute("data-autoplay-delay") || "5000");
  const autoplayDelay = Number.isFinite(delayAttr) ? delayAttr : 5000;

// 0 disables autoplay
  const autoplayEnabled = autoplayDelay > 0;

  const durationAttr = Number(container.getAttribute("data-transition-duration") || "700");
  const transitionDuration = Number.isFinite(durationAttr) ? durationAttr : 700;

  const prefersReducedMotion =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function setSlideState(slide, isActive) {
    slide.classList.toggle("opacity-100", isActive);
    slide.classList.toggle("opacity-0", !isActive);

    // Prevent invisible slides from stealing clicks / focus
    slide.classList.toggle("pointer-events-auto", isActive);
    slide.classList.toggle("pointer-events-none", !isActive);

    slide.setAttribute("aria-hidden", isActive ? "false" : "true");
  }

  function setDotState(dot, isActive) {
    // match your dot classes (adjust if you changed them in PHP)
    dot.classList.toggle("bg-white", isActive);
    dot.classList.toggle("bg-white/70", !isActive);
    dot.setAttribute("aria-selected", isActive ? "true" : "false");
  }

  function goTo(index) {
    const next = ((index % total) + total) % total;

    slides.forEach((slide, i) => setSlideState(slide, i === next));
    dots.forEach((dot, i) => setDotState(dot, i === next));

    current = next;
  }

  function nextSlide() {
    goTo(current + 1);
  }

  function prevSlide() {
    goTo(current - 1);
  }

  function stopAutoplay() {
    if (autoplayInterval) {
      window.clearInterval(autoplayInterval);
      autoplayInterval = null;
    }
  }

  function startAutoplay() {
    stopAutoplay();
    if (prefersReducedMotion) return;
    if (!autoplayEnabled) return;
    autoplayInterval = window.setInterval(nextSlide, autoplayDelay);
  }

  slides.forEach((slide) => {
    slide.style.transitionDuration = `${transitionDuration}ms`;
  });

  // Buttons
  nextBtn?.addEventListener("click", () => {
    nextSlide();
    startAutoplay(); // optional: restart timer on interaction
  });

  prevBtn?.addEventListener("click", () => {
    prevSlide();
    startAutoplay();
  });

  // Dots
  dots.forEach((dot) => {
    dot.addEventListener("click", () => {
      const n = Number(dot.dataset.dot);
      if (Number.isFinite(n)) {
        goTo(n);
        startAutoplay();
      }
    });
  });

  // Keyboard (container has tabindex="0" in the PHP refactor)
  container.addEventListener("keydown", (e) => {
    if (e.key === "ArrowLeft") {
      e.preventDefault();
      prevSlide();
      startAutoplay();
    }
    if (e.key === "ArrowRight") {
      e.preventDefault();
      nextSlide();
      startAutoplay();
    }
  });

  // Pause autoplay on hover/focus
  container.addEventListener("mouseenter", stopAutoplay);
  container.addEventListener("mouseleave", startAutoplay);
  container.addEventListener("focusin", stopAutoplay);
  container.addEventListener("focusout", startAutoplay);

  // Swipe / touch support
  let startX = 0;
  let endX = 0;
  const threshold = 50;

  container.addEventListener(
      "touchstart",
      (e) => {
        startX = e.touches[0].clientX;
        endX = startX;
      },
      { passive: true }
  );

  container.addEventListener(
      "touchmove",
      (e) => {
        endX = e.touches[0].clientX;
      },
      { passive: true }
  );

  container.addEventListener("touchend", () => {
    const diff = startX - endX;
    if (Math.abs(diff) > threshold) {
      if (diff > 0) nextSlide();
      else prevSlide();
      startAutoplay();
    }
  });

  // Init
  goTo(0);
  startAutoplay();
}