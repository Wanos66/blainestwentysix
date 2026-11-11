import kitchenGallery from './js/kitchen-gallery.js';
window.kitchenGallery = kitchenGallery;

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine
Alpine.start()

import { Fancybox } from "@fancyapps/ui";

window.Fancybox = Fancybox;

// Initialize
Fancybox.bind('[data-fancybox]', {
  // your options
});

import { initAccessibleFeatures } from "./js/modules/accessible-features";

import "./js/nav/menu.js";
import "./js/nav/desktop-mega.js";
import "./js/nav/desktop-nav-keys.js";
// import "./js/woocommerce/single-product";
import './js/woocommerce/product-postcode-checker';
import './js/woocommerce/cart-postcode-checker';
import './js/woocommerce/preferred-delivery-date';
import './js/woocommerce/checkout-validation';
import './js/woocommerce/transactional-promotions';
import './js/woocommerce/transactional-product-gallery.js';
import './js/woocommerce/transactional-similar-products.js';
import { slidefun } from './js/slider.js';
import { reviewsfun } from './js/reviews.js';
import { dealsfun } from './js/deals.js';
import { initBlogFilters } from "./js/filters/blog-filters.js";
import { initKitchenFilters } from './js/filters/kitchens.js';
import {initbathroomFilters} from "./js/filters/bathrooms.js";
import {initbedroomFilters} from "./js/filters/bedrooms.js";
import { initBrandLogoMarquees } from "./js/modules/brand-logo-marquee";
// import {initClearanceGallery} from "./js/woocommerce/single-product";

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
  const toggle = document.getElementById("mobile-menu-toggle");
  const menu = document.getElementById("mobile-menu");

  if (!toggle || !menu) return;

  toggle.addEventListener("click", () => {
    const isExpanded = toggle.getAttribute("aria-expanded") === "true";

    // Toggle ARIA attributes for accessibility
    toggle.setAttribute("aria-expanded", !isExpanded);
    menu.classList.toggle("hidden");

    // Toggle aria-hidden on menu
    menu.setAttribute("aria-hidden", isExpanded);

    // Trap focus within menu when open
    if (!isExpanded) {
      const firstFocusable = menu.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
      firstFocusable?.focus();
    }
  });

  // Close menu on Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !menu.classList.contains("hidden")) {
      toggle.click();
    }
  });

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
      if (!menu.classList.contains("hidden")) {
        toggle.click();
      }
    }
  });
}

/**
 * Smooth Scroll for Anchor Links
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");

      // Don't smooth scroll for # only links
      if (href === "#") return;

      const target = document.querySelector(href);
      if (!target) return;

      e.preventDefault();
      target.scrollIntoView({
        behavior: "smooth",
        block: "start"
      });

      // Update URL without jumping
      history.pushState(null, null, href);
    });
  });
}

/**
 * Lazy Load Images
 */
function initLazyLoad() {
  if ("loading" in HTMLImageElement.prototype) {
    // Native lazy loading is supported
    const images = document.querySelectorAll('img[data-src]');
    images.forEach(img => {
      img.src = img.dataset.src;
      img.removeAttribute('data-src');
    });
  } else {
    // Fallback for older browsers
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          observer.unobserve(img);
        }
      });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
      observer.observe(img);
    });
  }
}

/**
 * Add active class to current menu item
 */
function initActiveMenuItems() {
  const currentUrl = window.location.pathname;
  const menuLinks = document.querySelectorAll('.menu a');

  menuLinks.forEach(link => {
    if (link.getAttribute('href') === currentUrl) {
      link.classList.add('active');
      link.setAttribute('aria-current', 'page');
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const galleryRoot = document.querySelector('[data-project-gallery]');
  if (!galleryRoot) return;

  const mainWrapper = galleryRoot.querySelector('[data-project-main-wrapper]');
  const mainImage = galleryRoot.querySelector('[data-project-main-image]');
  const thumbs = Array.from(galleryRoot.querySelectorAll('[data-project-thumb]'));

  if (!mainWrapper || !mainImage || !thumbs.length) return;

  thumbs.forEach((thumb) => {
    thumb.addEventListener('click', () => {
      const fullSrc = thumb.getAttribute('data-full');
      const fullAlt = thumb.getAttribute('data-alt') || '';

      if (!fullSrc || mainImage.src === fullSrc) return;

      // Remove active styles from all thumbs
      thumbs.forEach((t) => {
        t.classList.remove('opacity-100', 'border-transparent');
        t.classList.add('opacity-70', 'border-gray-300');
      });

      // Add active styles to clicked thumb
      thumb.classList.add('opacity-100', 'border-transparent');
      thumb.classList.remove('opacity-70', 'border-gray-300');

// Fade out the image
      mainImage.classList.add('is-fading-out');

      setTimeout(() => {
        mainImage.src = fullSrc;
        mainImage.alt = fullAlt;

        mainImage.onload = () => {
          // Fade in new image
          mainImage.classList.remove('is-fading-out');
        };
      }, 200);

    });
  });
});

// document.addEventListener("DOMContentLoaded", () => {
//   const header = document.getElementById("site-header");
//
//   const SHOW_AT = 10;     // must be very near the top to re-expand
//   const HIDE_AT = 120;    // scroll-down threshold before condensing
//
//   window.addEventListener("scroll", () => {
//     const scroll = window.scrollY;
//
//     if (scroll > HIDE_AT) {
//       header.classList.add("header-condensed");
//     } else if (scroll <= SHOW_AT) {
//       header.classList.remove("header-condensed");
//     }
//   });
// });


// Run when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init);
} else {
  init();
}

// Export functions for potential reuse
export { initMobileMenu, initSmoothScroll, initLazyLoad };

/**
 * Initialize all features
 */
function init() {
  initMobileMenu();
  initSmoothScroll();
  initLazyLoad();
  initActiveMenuItems();
  initBrandLogoMarquees();
  initAccessibleFeatures();
  // initClearanceGallery();
  slidefun();
  reviewsfun();
  dealsfun();
  if (window.blainestwentysix?.filtersEnabled) {
    if (document.querySelector('[data-filter-context="blog"]')) {
      if (typeof initBlogFilters === 'function') initBlogFilters();
    }
    if (document.querySelector('[data-filter-context="kitchens"]')) {
      if (typeof initKitchenFilters === 'function') initKitchenFilters();
    }
    if (document.querySelector('[data-filter-context="bathrooms"]')) {
      if (typeof initbathroomFilters === 'function') initbathroomFilters();
    }
    if (document.querySelector('[data-filter-context="bedrooms"]')) {
      if (typeof initbedroomFilters === 'function') initbedroomFilters();
    }
  }

  console.log("✅ BlainesTwentySix theme loaded successfully!");
}