export function reviewsfun() {
  const initialiseReviewsSlider = () => {
    const section = document.getElementById('reviewsSection');
    const track = document.getElementById('reviewTrack');

    if (!section || !track || track.children.length <= 1) {
      return;
    }

    const nextButton = document.getElementById('nextReview');
    const previousButton = document.getElementById('prevReview');

    const transitionDuration = 700;
    const autoplayInterval = 6000;

    const reducedMotionQuery = window.matchMedia(
      '(prefers-reduced-motion: reduce)'
    );

    let step = 0;
    let autoplayTimer = null;
    let resizeTimer = null;
    let isAnimating = false;

    const calculateStep = () => {
      const firstReview = track.firstElementChild;

      if (!firstReview) {
        step = 0;
        return;
      }

      const styles = window.getComputedStyle(track);
      const gap =
        Number.parseFloat(styles.columnGap || styles.gap) || 0;

      step = firstReview.getBoundingClientRect().width + gap;
    };

    const canMove = () =>
      track.children.length > 1 && step > 0 && !isAnimating;

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
        const firstReview = track.firstElementChild;

        if (firstReview) {
          track.appendChild(firstReview);
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

      const lastReview = track.lastElementChild;

      if (!lastReview) {
        return;
      }

      isAnimating = true;
      track.insertBefore(lastReview, track.firstElementChild);
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
        reducedMotionQuery.matches ||
        document.hidden ||
        track.children.length <= 1
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

    reducedMotionQuery.addEventListener?.(
      'change',
      startAutoplay
    );

    calculateStep();
    startAutoplay();
  };

  if (document.readyState === 'loading') {
    document.addEventListener(
      'DOMContentLoaded',
      initialiseReviewsSlider,
      { once: true }
    );
  } else {
    initialiseReviewsSlider();
  }
}
