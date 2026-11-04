// src/filters/bedrooms.js

/**
 * bedroom archive filter logic
 *
 * Expects markup with:
 * - [data-bedroom-filter-toggle]
 * - [data-bedroom-filter-bar]
 * - [data-bedroom-results]
 * - [data-bedroom-pagination]
 * - [data-bedroom-active-filters]
 * - [data-filter-taxonomy][data-filter-term]
 *
 * REST endpoint:
 * /wp-json/blainestwentysix/v1/bedrooms
 */

// If you localise a global like window.blainestwentysix?.restUrl, use that:
const REST_URL =
    (window.blainestwentysix && window.blainestwentysix.restUrl) ||
    '/wp-json/blainestwentysix/v1/bedrooms';

export function initbedroomFilters() {
  const resultsEl = document.querySelector('[data-bedroom-results]');
  const paginationEl = document.querySelector('[data-bedroom-pagination]');
  const activeFiltersEl = document.querySelector('[data-bedroom-active-filters]');
  const filterBarEl = document.querySelector('[data-bedroom-filter-bar]');
  const toggleBtn = document.querySelector('[data-bedroom-filter-toggle]');

  if (!resultsEl || !paginationEl) {
    // Not on the bedroom archive
    return;
  }

  const state = {
    style: new Set(),
    finish: new Set(),
    brand: new Set(),
    search: '',
    paged: 1,
    isLoading: false,
  };

  /**
   * UTIL — build query string from state
   */
  function buildQuery() {
    const params = new URLSearchParams();

    // style, finish, brand: repeated keys
    state.style.forEach((slug) => params.append('style', slug));
    state.finish.forEach((slug) => params.append('finish', slug));
    state.brand.forEach((slug) => params.append('brand', slug));

    if (state.search) {
      params.set('search', state.search);
    }

    params.set('paged', String(state.paged || 1));

    return params.toString();
  }

  /**
   * UTIL — update dropdown button visual state
   */
  function updateDropdownButtonStates() {
    const optionButtons = document.querySelectorAll('[data-filter-taxonomy][data-filter-term]');
    optionButtons.forEach((btn) => {
      const tax = btn.getAttribute('data-filter-taxonomy');
      const term = btn.getAttribute('data-filter-term');
      let isActive = false;

      if (tax === 'bedroom_style') {
        isActive = state.style.has(term);
      } else if (tax === 'bedroom_finish') {
        isActive = state.finish.has(term);
      } else if (tax === 'bedroom_brand') {
        isActive = state.brand.has(term);
      }

      btn.classList.toggle('bg-slate-100', isActive);
      btn.classList.toggle('font-semibold', isActive);
      btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  /**
   * Render active filter chips from REST meta
   */
  function renderActiveChips(activeFilters) {
    if (!activeFiltersEl) return;

    const { style = [], finish = [], brand = [] } = activeFilters || {};
    const all = [
      ...style.map((item) => ({ tax: 'bedroom_style', ...item })),
      ...finish.map((item) => ({ tax: 'bedroom_finish', ...item })),
      ...brand.map((item) => ({ tax: 'bedroom_brand', ...item })),
    ];

    if (!all.length) {
      activeFiltersEl.innerHTML = '';
      return;
    }

    const frag = document.createDocumentFragment();

    all.forEach((item) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className =
          'inline-flex items-center gap-1 rounded-full bg-slate-900 px-3 py-1 text-xs text-white';
      button.setAttribute('data-chip-tax', item.tax);
      button.setAttribute('data-chip-term', item.slug);
      button.innerHTML = `
        <span>${item.name}</span>
        <span aria-hidden="true">✕</span>
      `;
      frag.appendChild(button);
    });

    // Clear all chip
    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className =
        'inline-flex items-center gap-1 rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 bg-white';
    clearBtn.setAttribute('data-chip-clear-all', '1');
    clearBtn.innerHTML = `<span>Clear all</span>`;
    frag.appendChild(clearBtn);

    activeFiltersEl.innerHTML = '';
    activeFiltersEl.appendChild(frag);
  }

  /**
   * Core fetcher
   */
  async function fetchbedrooms() {
    if (state.isLoading) return;

    state.isLoading = true;
    resultsEl.classList.add('opacity-60');

    try {
      const query = buildQuery();
      const url = `${REST_URL}?${query}`;

      const response = await fetch(url, {
        headers: {
          'X-WP-Nonce': window.wpApiSettings?.nonce || '',
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
      }

      const data = await response.json();

      // Update results & pagination
      resultsEl.innerHTML = data.html || '';
      paginationEl.innerHTML = data.pagination || '';

      // Active chips
      renderActiveChips(data.active_filters);

      // Update dropdown visual states
      updateDropdownButtonStates();
    } catch (err) {
      // Soft fail
      // eslint-disable-next-line no-console
      console.error('bedroom filter error:', err);
    } finally {
      state.isLoading = false;
      resultsEl.classList.remove('opacity-60');
    }
  }

  /**
   * Toggle a term in state
   */
  function toggleTerm(tax, slug) {
    let setRef;

    if (tax === 'bedroom_style') {
      setRef = state.style;
    } else if (tax === 'bedroom_finish') {
      setRef = state.finish;
    } else if (tax === 'bedroom_brand') {
      setRef = state.brand;
    }

    if (!setRef) return;

    if (setRef.has(slug)) {
      setRef.delete(slug);
    } else {
      setRef.add(slug);
    }

    state.paged = 1; // reset to first page
    fetchbedrooms();
  }

  /**
   * Clear everything
   */
  function clearAllFilters() {
    state.style.clear();
    state.finish.clear();
    state.brand.clear();
    state.paged = 1;
    fetchbedrooms();
  }

  /**
   * Pagination click handler
   */
  function handlePaginationClick(event) {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;

    const link = target.closest('a');
    if (!link || !paginationEl.contains(link)) return;

    const href = link.getAttribute('href');
    if (!href) return;

    event.preventDefault();

    // Try to extract ?paged=N or /page/N/
    let newPage = 1;
    const urlObj = new URL(href, window.location.origin);
    const pagedParam = urlObj.searchParams.get('paged');

    if (pagedParam) {
      newPage = parseInt(pagedParam, 10) || 1;
    } else {
      const match = href.match(/\/page\/(\d+)/);
      if (match && match[1]) {
        newPage = parseInt(match[1], 10) || 1;
      }
    }

    if (!newPage || newPage === state.paged) return;

    state.paged = newPage;
    fetchbedrooms();
  }

  /**
   * Dropdown open/close (desktop)
   */
  function setupDropdowns() {
    if (!filterBarEl) return;
    const triggers = filterBarEl.querySelectorAll('[data-bedroom-filter-dropdown-trigger]');

    triggers.forEach((btn) => {
      btn.addEventListener('click', () => {
        const tax = btn.getAttribute('data-tax');
        const dropdown = btn.parentElement?.querySelector('[data-bedroom-filter-dropdown]');
        if (!dropdown) return;

        const isOpen = dropdown.classList.contains('block');

        // Close all first
        filterBarEl.querySelectorAll('[data-bedroom-filter-dropdown]').forEach((dd) => {
          dd.classList.remove('block');
          dd.classList.add('hidden');
        });

        if (!isOpen) {
          dropdown.classList.remove('hidden');
          dropdown.classList.add('block');
          dropdown.setAttribute('data-open-for', tax || '');
        }
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
      if (!filterBarEl) return;
      const target = event.target;
      if (!(target instanceof HTMLElement)) return;

      if (filterBarEl.contains(target)) return;

      filterBarEl.querySelectorAll('[data-bedroom-filter-dropdown]').forEach((dd) => {
        dd.classList.remove('block');
        dd.classList.add('hidden');
      });
    });
  }

  /**
   * Mobile toggle for whole filter bar
   */
  function setupMobileToggle() {
    if (!toggleBtn || !filterBarEl) return;

    toggleBtn.addEventListener('click', () => {
      const isHidden = filterBarEl.classList.contains('hidden');
      if (isHidden) {
        filterBarEl.classList.remove('hidden');
        filterBarEl.classList.add('flex');
      } else {
        filterBarEl.classList.add('hidden');
        filterBarEl.classList.remove('flex');
      }
    });
  }

  /**
   * Listeners
   */
  function bindListeners() {
    // Dropdown option clicks
    document.addEventListener('click', (event) => {
      const target = event.target;
      if (!(target instanceof HTMLElement)) return;

      const btn = target.closest('[data-filter-taxonomy][data-filter-term]');
      if (!btn) return;

      const tax = btn.getAttribute('data-filter-taxonomy');
      const term = btn.getAttribute('data-filter-term');
      if (!tax || !term) return;

      event.preventDefault();
      toggleTerm(tax, term);
    });

    // Pagination
    paginationEl.addEventListener('click', handlePaginationClick);

    // Chips
    if (activeFiltersEl) {
      activeFiltersEl.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const chip = target.closest('[data-chip-tax][data-chip-term]');
        const clearChip = target.closest('[data-chip-clear-all]');

        if (clearChip) {
          event.preventDefault();
          clearAllFilters();
          return;
        }

        if (chip) {
          const tax = chip.getAttribute('data-chip-tax');
          const term = chip.getAttribute('data-chip-term');
          if (!tax || !term) return;
          event.preventDefault();
          toggleTerm(tax, term);
        }
      });
    }

    // "Clear all" inside dropdown panel
    document.addEventListener('click', (event) => {
      const target = event.target;
      if (!(target instanceof HTMLElement)) return;

      const clearBtn = target.closest('[data-bedroom-clear-all]');
      if (!clearBtn) return;

      event.preventDefault();
      clearAllFilters();
    });

  }

  // Init
  setupDropdowns();
  setupMobileToggle();
  bindListeners();
  updateDropdownButtonStates();
}
