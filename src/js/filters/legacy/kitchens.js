/**
 * Kitchen Archive Filter — Single Dropdown Version (News-style)
 *
 * Matches:
 *  - [data-kitchen-filter-container]
 *  - [data-filter-taxonomy]
 *  - [data-filter-term]
 *  - [data-kitchen-clear-all]
 *  - [data-kitchen-active-filters]
 *  - [data-kitchen-results]
 *  - [data-kitchen-pagination]
 *  - [data-kitchen-style-list]
 *  - [data-kitchen-finish-list]
 *  - [data-kitchen-brand-list]
 */

const REST_URL = '/wp-json/blt/v1/kitchens';

export function initKitchenFilters() {
  console.log('initKitchenFilters loaded ✅ v2');

  const resultsEl = document.querySelector('[data-kitchen-results]');
  const paginationEl = document.querySelector('[data-kitchen-pagination]');
  const chipsMount = document.getElementById('js-kitchen-active-filters-mount');
  const activeFiltersEl = document.querySelector('[data-kitchen-active-filters]');
  const archiveTitleEl = document.getElementById('js-archive-title');
  const defaultTitle =
      archiveTitleEl?.getAttribute('data-default-title') ||
      archiveTitleEl?.textContent?.trim() ||
      'Kitchens';

  function updateArchiveTitle() {
    if (!archiveTitleEl) return;

    const hasFilters = Boolean(state.style || state.finish || state.brand);
    const title = hasFilters ? 'Filtered kitchens' : defaultTitle;

    archiveTitleEl.textContent = title;
  }


  if (!resultsEl || !paginationEl) return;

  // -------------------------
  // STATE (single-select per group)
  // -------------------------
  const state = {
    style: '',
    finish: '',
    brand: '',
    search: '',
    paged: 1,
    isLoading: false,
  };

  // -------------------------
  // URL → STATE
  // -------------------------
  function initStateFromURL() {
    const params = new URLSearchParams(window.location.search);

    state.style  = params.get('style')  || '';
    state.finish = params.get('finish') || '';
    state.brand  = params.get('brand')  || '';

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
  // UPDATE OPTION BUTTON UI
  // -------------------------
  function updateDropdownButtonStates() {
    const optionButtons = document.querySelectorAll(
        '[data-filter-taxonomy][data-filter-term], [data-taxonomy][data-term]'
    );

    optionButtons.forEach((btn) => {
      const tax =
          btn.getAttribute('data-filter-taxonomy') || btn.getAttribute('data-taxonomy');
      const term =
          btn.getAttribute('data-filter-term') || btn.getAttribute('data-term');

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

      btn.classList.toggle('bg-slate-900', isActive);
      btn.classList.toggle('text-white', isActive);
      btn.classList.toggle('font-semibold', isActive);

      btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }



  // -------------------------
  // ACTIVE FILTER CHIPS
  // -------------------------
  function renderActiveChips(filters) {
    if (!activeFiltersEl) return;

    const items = [
      ...(filters.style  || []).map((f) => ({ ...f, tax: 'kitchen_style' })),
      ...(filters.finish || []).map((f) => ({ ...f, tax: 'kitchen_finish' })),
      ...(filters.brand  || []).map((f) => ({ ...f, tax: 'kitchen_brand' })),
    ];

    if (!items.length) {
      activeFiltersEl.innerHTML = '';
      return;
    }

    const frag = document.createDocumentFragment();

    items.forEach((item) => {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className =
          'inline-flex items-center gap-1 rounded-full bg-black px-3 py-1 text-xs text-white';
      chip.setAttribute('data-chip-tax', item.tax);
      chip.setAttribute('data-chip-term', item.slug);
      chip.innerHTML = `<span>${item.name}</span><span>✕</span>`;
      frag.appendChild(chip);
    });

    const clear = document.createElement('button');
    clear.type = 'button';
    clear.className =
        'inline-flex items-center gap-1 rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 bg-white';
    clear.setAttribute('data-chip-clear-all', '1');
    clear.textContent = 'Clear all';
    frag.appendChild(clear);

    activeFiltersEl.innerHTML = '';
    activeFiltersEl.appendChild(frag);
  }

  // -------------------------
  // MAIN FETCH FUNCTION
  // -------------------------
  async function fetchKitchens({ sync = true } = {}) {

    if (state.isLoading) return;

    state.isLoading = true;
    resultsEl?.classList.add('opacity-60');

    try {
      const query = buildQuery();
      const res = await fetch(`${REST_URL}?${query}`);

      if (!res.ok) throw new Error('REST returned error');

      const data = await res.json();

      // Grid + pagination
      resultsEl.innerHTML = data.grid_html || '';
      paginationEl.innerHTML = data.pagination || '';

      // Chips (server-rendered)
      const mount = document.getElementById('js-kitchen-active-filters-mount');
      if (mount) mount.innerHTML = data.chips_html || '';

      // Dropdown groups (Option-C)
      const styleWrap  = document.querySelector('[data-kitchen-style-list]');
      const finishWrap = document.querySelector('[data-kitchen-finish-list]');
      const brandWrap  = document.querySelector('[data-kitchen-brand-list]');

      if (styleWrap && data.style_html)   styleWrap.innerHTML = data.style_html;
      if (finishWrap && data.finish_html) finishWrap.innerHTML = data.finish_html;
      if (brandWrap && data.brand_html)   brandWrap.innerHTML = data.brand_html;

      // Button active states
      updateDropdownButtonStates();

      // Archive title
      updateArchiveTitle();

      if (sync) syncURL();

    } catch (err) {
      console.error('Kitchen filter error:', err);

    } finally {
      state.isLoading = false;
      resultsEl?.classList.remove('opacity-60');
    }
  }

  // -------------------------
  // FILTER TERM TOGGLE
  // -------------------------
  function selectTerm(tax, slug) {

    if (tax === 'kitchen_style')  state.style  = slug;
    if (tax === 'kitchen_finish') state.finish = slug;
    if (tax === 'kitchen_brand')  state.brand  = slug;

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

// Option buttons (style/finish/brand)
  document.addEventListener(
      'click',
      (event) => {
        const el = event.target.closest('[data-taxonomy][data-term]');
        if (!el) return;

        event.preventDefault();

        const tax  = el.getAttribute('data-taxonomy');
        const term = el.getAttribute('data-term');

        selectTerm(tax, term);
      },
      true // ✅ capture phase
  );


  // Clear all chip
  document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-chip-clear-all]');
    if (!el) return;
    event.preventDefault();
    clearAll();
  });

  // Remove single chip
  document.addEventListener('click', (event) => {
    const chip = event.target.closest('[data-chip-tax][data-chip-term]');
    if (!chip) return;

    event.preventDefault();

    const tax  = chip.getAttribute('data-chip-tax');
    const term = chip.getAttribute('data-chip-term');

    // Removing a chip = clearing that group
    if (tax === 'kitchen_style')  state.style  = '';
    if (tax === 'kitchen_finish') state.finish = '';
    if (tax === 'kitchen_brand')  state.brand  = '';

    state.paged = 1;
    fetchKitchens();
  });

  // Pagination
  paginationEl.addEventListener('click', (event) => {
    const link = event.target.closest('a');
    if (!link) return;

    event.preventDefault();

    const urlObj = new URL(link.href);
    let paged = parseInt(urlObj.searchParams.get('page'), 10) || 1;

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

  const hasInitial =
      state.style || state.finish || state.brand || state.paged > 1;

  if (hasInitial) {
    fetchKitchens({ sync: false });
  } else {
    updateDropdownButtonStates(); // highlight from server-rendered HTML
    updateArchiveTitle();
  }
}
