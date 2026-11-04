/**
 * Similar Products carousel controls.
 *
 * Native horizontal scrolling remains available when JavaScript is disabled.
 */

const initialiseSimilarProducts = (section) => {
    const track = section.querySelector('[data-similar-track]');
    const previousButton = section.querySelector('[data-similar-prev]');
    const nextButton = section.querySelector('[data-similar-next]');
    const controls = section.querySelector('[data-similar-controls]');

    if (!(track instanceof HTMLElement)) {
        return;
    }

    const getStep = () => {
        const firstCard = track.querySelector('[data-similar-card]');

        if (!(firstCard instanceof HTMLElement)) {
            return track.clientWidth;
        }

        const styles = window.getComputedStyle(track);
        const gap = Number.parseFloat(styles.columnGap || styles.gap || '0');

        return firstCard.getBoundingClientRect().width + gap;
    };

    const updateControls = () => {
        const maximumScroll = Math.max(0, track.scrollWidth - track.clientWidth);
        const hasOverflow = maximumScroll > 2;

        if (controls instanceof HTMLElement) {
            controls.hidden = !hasOverflow;
        }

        if (previousButton instanceof HTMLButtonElement) {
            previousButton.disabled = !hasOverflow || track.scrollLeft <= 2;
        }

        if (nextButton instanceof HTMLButtonElement) {
            nextButton.disabled = !hasOverflow || track.scrollLeft >= maximumScroll - 2;
        }
    };

    previousButton?.addEventListener('click', () => {
        track.scrollBy({ left: -getStep(), behavior: 'smooth' });
    });

    nextButton?.addEventListener('click', () => {
        track.scrollBy({ left: getStep(), behavior: 'smooth' });
    });

    track.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
            return;
        }

        event.preventDefault();
        track.scrollBy({
            left: event.key === 'ArrowRight' ? getStep() : -getStep(),
            behavior: 'smooth',
        });
    });

    let frameId = 0;

    track.addEventListener(
        'scroll',
        () => {
            window.cancelAnimationFrame(frameId);
            frameId = window.requestAnimationFrame(updateControls);
        },
        { passive: true }
    );

    if ('ResizeObserver' in window) {
        const resizeObserver = new ResizeObserver(updateControls);
        resizeObserver.observe(track);
    } else {
        window.addEventListener('resize', updateControls, { passive: true });
    }

    updateControls();
};

const initialiseAllSimilarProducts = () => {
    document.querySelectorAll('[data-similar-products]').forEach((section) => {
        if (section instanceof HTMLElement) {
            initialiseSimilarProducts(section);
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseAllSimilarProducts, { once: true });
} else {
    initialiseAllSimilarProducts();
}
