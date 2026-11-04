/**
 * Mobile Menu Controller (Blaine’s 2025)
 * - Slide-down panel
 * - Accessible focus trap
 * - Accordion sub-menus
 */

document.addEventListener("DOMContentLoaded", () => {
  const menu     = document.getElementById("mobile-menu");
  const openBtn  = document.getElementById("menu-toggle");
  const closeBtn = document.getElementById("menu-close");

  if (!menu || !openBtn) return;

  /* -----------------------------------------------------
     Focus Trap Setup
  ----------------------------------------------------- */

  let focusableEls = [];
  let firstFocusable;
  let lastFocusable;

  const updateFocusable = () => {
    focusableEls = menu.querySelectorAll(
        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );
    firstFocusable = focusableEls[0];
    lastFocusable  = focusableEls[focusableEls.length - 1];
  };

  const trapFocus = (e) => {
    if (e.key !== "Tab") return;

    // backwards tab
    if (e.shiftKey) {
      if (document.activeElement === firstFocusable) {
        e.preventDefault();
        lastFocusable.focus();
      }
    }
    // forward tab
    else {
      if (document.activeElement === lastFocusable) {
        e.preventDefault();
        firstFocusable.focus();
      }
    }
  };

  /* -----------------------------------------------------
     Menu Open / Close
  ----------------------------------------------------- */

  const showMenu = () => {
    menu.classList.remove("hidden");
    menu.classList.add("open");

    menu.setAttribute("aria-hidden", "false");
    openBtn.setAttribute("aria-expanded", "true");

    document.body.classList.add("overflow-hidden");

    updateFocusable();
    (firstFocusable ?? closeBtn).focus();

    document.addEventListener("keydown", trapFocus);
  };

  const hideMenu = () => {
    menu.classList.remove("open");
    menu.classList.add("hidden");

    menu.setAttribute("aria-hidden", "true");
    openBtn.setAttribute("aria-expanded", "false");

    document.body.classList.remove("overflow-hidden");

    document.removeEventListener("keydown", trapFocus);

    openBtn.focus();
  };

  openBtn.addEventListener("click", () => {
    if (menu.classList.contains("hidden")) {
      showMenu();
    } else {
      hideMenu();
    }
  });

  closeBtn.addEventListener("click", hideMenu);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !menu.classList.contains("hidden")) {
      hideMenu();
    }
  });
});

/* ============================================
   MOBILE SUB-MENU ACCORDION (Simple, reliable)
   ============================================ */

document.addEventListener("DOMContentLoaded", () => {

  const mobileMenu = document.getElementById("mobile-menu");
  if (!mobileMenu) return;

  const submenuParents = mobileMenu.querySelectorAll(".menu-item-has-children");

  submenuParents.forEach(parent => {

    const link = parent.querySelector("a");
    const submenu = parent.querySelector("ul");

    if (!link || !submenu) return;

    // Close by default (mobile only)
    submenu.style.display = "none";

    // Add arrow indicator
    link.insertAdjacentHTML(
        "beforeend",
        '<span class="ml-2 text-sm">▾</span>'
    );

    link.addEventListener("click", (e) => {
      // Mobile-only accordion
      if (window.innerWidth < 1024) {
        e.preventDefault();
        const isOpen = submenu.style.display === "block";
        submenu.style.display = isOpen ? "none" : "block";
      }
    });

  });
});



