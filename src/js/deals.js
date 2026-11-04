export function dealsfun() {
  const initialiseDealsSlider = () => {
    const section = document.getElementById('dealsSection');
    const track = document.getElementById('dealTrack');

    if (!section || !track) {
      return;
    }

    const nextButton = document.getElementById('nextDeal');
    const previousButton = document.getElementById('prevDeal');

    const transitionDuration = 700;
    const autoplayInterval = 5000;
    const reducedMotionQuery = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    let step = 0;
    let autoplayTimer = null;
    let isAnimating = false;
    let resizeTimer = null;

    const productCount = () => track.children.length;

    const calculateStep = () => {
      const firstProduct = track.firstElementChild;

      if (!firstProduct) {
        step = 0;
        return;
      }

      const trackStyles = window.getComputedStyle(track);
      const gap =
          Number.parseFloat(trackStyles.columnGap || trackStyles.gap) || 0;

      step = firstProduct.getBoundingClientRect().width + gap;
    };

    const canMove = () =>
        productCount() > 1 && step > 0 && !isAnimating;

    const stopAutoplay = () => {
      if (autoplayTimer !== null) {
        window.clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    };

    const moveNext = () => {
      calculateStep();

      if (!canMove()) {
        return;
      }

      isAnimating = true;

      track.style.transition =
          `transform ${transitionDuration}ms ease-in-out`;

      track.style.transform = `translate3d(-${step}px, 0, 0)`;

      window.setTimeout(() => {
        const firstProduct = track.firstElementChild;

        if (firstProduct) {
          track.appendChild(firstProduct);
        }

        track.style.transition = 'none';
        track.style.transform = 'translate3d(0, 0, 0)';

        isAnimating = false;
      }, transitionDuration);
    };

    const movePrevious = () => {
      calculateStep();

      if (!canMove()) {
        return;
      }

      const lastProduct = track.lastElementChild;

      if (!lastProduct) {
        return;
      }

      isAnimating = true;

      track.insertBefore(lastProduct, track.firstElementChild);
      track.style.transition = 'none';
      track.style.transform = `translate3d(-${step}px, 0, 0)`;

      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          track.style.transition =
              `transform ${transitionDuration}ms ease-in-out`;

          track.style.transform = 'translate3d(0, 0, 0)';
        });
      });

      window.setTimeout(() => {
        isAnimating = false;
      }, transitionDuration);
    };

    const startAutoplay = () => {
      stopAutoplay();

      if (
          productCount() <= 1 ||
          reducedMotionQuery.matches ||
          document.hidden
      ) {
        return;
      }

      autoplayTimer = window.setInterval(
          moveNext,
          autoplayInterval
      );
    };

    const restartAutoplay = () => {
      stopAutoplay();
      startAutoplay();
    };

    nextButton?.addEventListener('click', () => {
      moveNext();
      restartAutoplay();
    });

    previousButton?.addEventListener('click', () => {
      movePrevious();
      restartAutoplay();
    });

    section.addEventListener('mouseenter', stopAutoplay);
    section.addEventListener('mouseleave', startAutoplay);
    section.addEventListener('focusin', stopAutoplay);

    section.addEventListener('focusout', (event) => {
      if (!section.contains(event.relatedTarget)) {
        startAutoplay();
      }
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        stopAutoplay();
      } else {
        startAutoplay();
      }
    });

    window.addEventListener('resize', () => {
      window.clearTimeout(resizeTimer);

      resizeTimer = window.setTimeout(() => {
        calculateStep();
      }, 150);
    });

    reducedMotionQuery.addEventListener?.('change', () => {
      startAutoplay();
    });

    calculateStep();
    startAutoplay();
  };

  if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initialiseDealsSlider,
        { once: true }
    );
  } else {
    initialiseDealsSlider();
  }
}