/**
 * Kitchen Archive Filter — Single Dropdown Version (News-style)
 *
 * Expected markup:
 *  - [data-kitchen-results]
 *  - [data-kitchen-pagination]
 *  - [data-kitchen-style-list]
 *  - [data-kitchen-finish-list]
 *  - [data-kitchen-brand-list]
 *  - [data-kitchen-filter-container]  (wraps the dropdown + option buttons)
 *  - #js-kitchen-active-filters-mount (chips mount — single source of truth)
 *  - #js-archive-title (optional)
 */

const REST_URL = '/wp-json/blt/v1/kitchens';

export function initKitchenFilters() {
  console.log('initKitchenFilters loaded ✅ v3');

  const resultsEl = document.querySelector('[data-kitchen-results]');
  const paginationEl = document.querySelector('[data-kitchen-pagination]');
  if (!resultsEl || !paginationEl) return;

  const filterContainer = document.querySelector('[data-kitchen-filter-container]');
  const chipsMount = document.getElementById('js-kitchen-active-filters-mount');

  const archiveTitleEl = document.getElementById('js-archive-title');
  const defaultTitle =
      archiveTitleEl?.getAttribute('data-default-title') ||
      archiveTitleEl?.textContent?.trim() ||
      'Kitchens';

  // -------------------------
  // STATE (single-select per group)
  // -------------------------
  const state = {
    style: '',
    finish: '',
    brand: '',
    // search is intentionally not wired yet (UI exists but not implemented here)
    paged: 1,
    isLoading: false,
  };

  function updateArchiveTitle() {
    if (!archiveTitleEl) return;

    const hasFilters = Boolean(state.style || state.finish || state.brand);
    archiveTitleEl.textContent = hasFilters ? 'Filtered kitchens' : defaultTitle;
  }

  // -------------------------
  // URL → STATE
  // -------------------------
  function initStateFromURL() {
    const params = new URLSearchParams(window.location.search);

    state.style = params.get('style') || '';
    state.finish = params.get('finish') || '';
    state.brand = params.get('brand') || '';

    const page = parseInt(params.get('page'), 10);
    state.paged = !Number.isNaN(page) && page > 1 ? page : 1;
  }

  // -------------------------
  // BUILD QUERY STRING
  // -------------------------
  function buildQuery() {
    const params = new URLSearchParams();

    if (state.style) params.set('style', state.style);
    if (state.finish) params.set('finish', state.finish);
    if (state.brand) params.set('brand', state.brand);

    params.set('page', String(state.paged || 1));
    return params.toString();
  }

  // -------------------------
  // WRITE STATE → URL
  // -------------------------
  function syncURL() {
    const query = buildQuery();
    const url = query
        ? `${window.location.pathname}?${query}`
        : window.location.pathname;

    window.history.pushState({}, '', url);
  }

  // -------------------------
  // UPDATE OPTION BUTTON UI (scoped)
  // -------------------------
  function updateDropdownButtonStates() {
    if (!filterContainer) return;

    const optionButtons = filterContainer.querySelectorAll('[data-taxonomy][data-term]');

    optionButtons.forEach((btn) => {
      const tax = btn.getAttribute('data-taxonomy') || '';
      const term = btn.getAttribute('data-term') || '';

      const current =
          tax === 'kitchen_style'
              ? state.style
              : tax === 'kitchen_finish'
                  ? state.finish
                  : tax === 'kitchen_brand'
                      ? state.brand
                      : '';

      // "All" is active when the current value is empty for that taxonomy
      const isActive = term === '' ? current === '' : current === term;

// Remove BOTH possibilities first (prevents Tailwind precedence issues)
      btn.classList.remove('bg-white', 'bg-slate-900', 'text-gray-700', 'text-white', 'font-semibold');

// Re-apply the correct set
      if (isActive) {
        btn.classList.add('bg-slate-900', 'text-white', 'font-semibold');
      } else {
        btn.classList.add('bg-white', 'text-gray-700');
      }

      btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');

    });
  }

  // -------------------------
  // MAIN FETCH FUNCTION
  // -------------------------
  async function fetchKitchens({ sync = true } = {}) {
    if (state.isLoading) return;

    state.isLoading = true;
    resultsEl.classList.add('opacity-60');

    try {
      const query = buildQuery();
      const res = await fetch(`${REST_URL}?${query}`);
      if (!res.ok) throw new Error('REST returned error');

      const data = await res.json();

      // Grid + pagination
      resultsEl.innerHTML = data.grid_html || '';
      paginationEl.innerHTML = data.pagination || '';

      // Chips (single mount — server-rendered HTML)
      if (chipsMount) chipsMount.innerHTML = data.chips_html || '';

      // Dropdown groups (Option-C)
      const styleWrap = filterContainer?.querySelector('[data-kitchen-style-list]') || null;
      const finishWrap = filterContainer?.querySelector('[data-kitchen-finish-list]') || null;
      const brandWrap = filterContainer?.querySelector('[data-kitchen-brand-list]') || null;

      if (styleWrap && data.style_html) styleWrap.innerHTML = data.style_html;
      if (finishWrap && data.finish_html) finishWrap.innerHTML = data.finish_html;
      if (brandWrap && data.brand_html) brandWrap.innerHTML = data.brand_html;

      // Button active states
      updateDropdownButtonStates();

      // Archive title
      updateArchiveTitle();

      // URL
      if (sync) syncURL();
    } catch (err) {
      console.error('Kitchen filter error:', err);
    } finally {
      state.isLoading = false;
      resultsEl.classList.remove('opacity-60');
    }
  }

  // -------------------------
  // FILTER TERM SELECT
  // -------------------------
  function selectTerm(tax, slug) {
    if (tax === 'kitchen_style') state.style = slug;
    if (tax === 'kitchen_finish') state.finish = slug;
    if (tax === 'kitchen_brand') state.brand = slug;

    state.paged = 1;
    fetchKitchens();
  }

  // -------------------------
  // CLEAR ALL
  // -------------------------
  function clearAll() {
    state.style = '';
    state.finish = '';
    state.brand = '';
    state.paged = 1;
    fetchKitchens();
  }

  // -------------------------
  // EVENT LISTENERS
  // -------------------------

  // Option buttons (style/finish/brand) — capture phase helps when innerHTML swaps
  document.addEventListener(
      'click',
      (event) => {
        const el = event.target.closest('[data-taxonomy][data-term]');
        if (!el) return;

        // Scope to kitchens filter container only
        if (filterContainer && !filterContainer.contains(el)) return;

        event.preventDefault();

        const tax = el.getAttribute('data-taxonomy') || '';
        const term = el.getAttribute('data-term') || '';

        selectTerm(tax, term);
      },
      true
  );

  // Clear all chip
  document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-chip-clear-all]');
    if (!el) return;

    // Optional: scope to chips mount if it exists
    if (chipsMount && !chipsMount.contains(el)) return;

    event.preventDefault();
    clearAll();
  });

  // Remove single chip
  document.addEventListener('click', (event) => {
    const chip = event.target.closest('[data-chip-tax][data-chip-term]');
    if (!chip) return;

    // Optional: scope to chips mount if it exists
    if (chipsMount && !chipsMount.contains(chip)) return;

    event.preventDefault();

    const tax = chip.getAttribute('data-chip-tax') || '';

    // Removing a chip = clearing that group
    if (tax === 'kitchen_style') state.style = '';
    if (tax === 'kitchen_finish') state.finish = '';
    if (tax === 'kitchen_brand') state.brand = '';

    state.paged = 1;
    fetchKitchens();
  });

  // Pagination
  paginationEl.addEventListener('click', (event) => {
    const link = event.target.closest('a');
    if (!link) return;

    event.preventDefault();

    const urlObj = new URL(link.href);
    const paged = parseInt(urlObj.searchParams.get('page'), 10) || 1;

    if (paged === state.paged) return;

    state.paged = paged;
    fetchKitchens();
  });

  // Back / Forward
  window.addEventListener('popstate', () => {
    initStateFromURL();
    fetchKitchens({ sync: false });
  });

  // -------------------------
  // INITIAL LOAD
  // -------------------------
  initStateFromURL();

  const hasInitial = state.style || state.finish || state.brand || state.paged > 1;

  if (hasInitial) {
    fetchKitchens({ sync: false });
  } else {
    updateDropdownButtonStates(); // highlight from server-rendered HTML
    updateArchiveTitle();
  }
}
