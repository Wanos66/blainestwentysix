// src/js/filters/blog-filters.js
// Blog (News) filtering only — init-style, no auto execution

export function initBlogFilters() {
  // Feature flag guard
  if (!window.blainestwentysix?.filtersEnabled) return;

  const contextEl = document.querySelector('[data-filter-context="blog"]');
  if (!contextEl) return;

  const CONFIG = {
    endpoint: '/wp-json/blt/v1/posts',
    paramKeys: ['category', 'tag', 's', 'page'],
    paginationSelector: '#js-pagination',
    searchInputId: 'js-filter-search',
    searchClearId: 'js-filter-search-clear',
  };

  let isLoading = false;

  // ----------------------
  // Helpers
  // ----------------------
  function extractParamsFromURL(urlLike, keys) {
    const u = new URL(urlLike, window.location.origin);
    const obj = {};
    keys.forEach((key) => {
      obj[key] = u.searchParams.get(key) || '';
    });
    if (!obj.page) obj.page = '1';
    return obj;
  }

  function buildApiUrl(base, params) {
    const apiUrl = new URL(base, window.location.origin);
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        apiUrl.searchParams.set(key, value);
      }
    });
    return apiUrl.toString();
  }

  function updateBrowserUrl(params) {
    const url = new URL(window.location.href);
    CONFIG.paramKeys.forEach((key) => {
      const value = params[key];
      if (!value || value === '') url.searchParams.delete(key);
      else url.searchParams.set(key, value);
    });
    window.history.pushState(params, '', url.toString());
  }

  // ----------------------
  // DOM Updates
  // ----------------------
  function applyBlogResponse(data) {
    const contentArea = document.getElementById('js-content-area');
    if (!contentArea || !data.html) return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = data.html.trim();
    const newContent = wrapper.firstElementChild;
    if (!newContent || newContent.id !== 'js-content-area') return;

    const sync = (id) => {
      const oldEl = document.getElementById(id);
      const newEl = newContent.querySelector(`#${id}`);
      if (oldEl && newEl) oldEl.innerHTML = newEl.innerHTML;
    };

    sync('js-title-block');
    sync('js-active-filters');
    sync('js-post-grid-wrapper');

    if (typeof data.tags_html === 'string') {
      const tagList = document.getElementById('js-tag-list');
      if (tagList) tagList.innerHTML = data.tags_html;
    }

    if (typeof data.cats_html === 'string') {
      const catList = document.getElementById('js-category-list');
      if (catList) catList.innerHTML = data.cats_html;
    }

    const pagContainer = document.getElementById('js-pagination');
    if (pagContainer) pagContainer.innerHTML = data.pagination || '';

    initSearch();
  }

  // ----------------------
  // Fetch
  // ----------------------
  async function loadPosts(params, pushState = true) {
    if (isLoading) return;
    isLoading = true;

    try {
      const apiUrl = buildApiUrl(CONFIG.endpoint, params);
      const response = await fetch(apiUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      if (!response.ok) {
        throw new Error(`REST error: ${response.status}`);
      }

      const data = await response.json();
      applyBlogResponse(data);

      if (pushState) {
        updateBrowserUrl(params);
      }
    } catch (err) {
      console.error('REST fetch error:', err);
    } finally {
      isLoading = false;
    }
  }

  // ----------------------
  // Search
  // ----------------------
  function initSearch() {
    const input = document.getElementById(CONFIG.searchInputId);
    const clearBtn = document.getElementById(CONFIG.searchClearId);
    if (!input) return;

    if (clearBtn) {
      clearBtn.classList.toggle('hidden', input.value.length === 0);
    }

    let timer;
    input.oninput = () => {
      if (clearBtn) {
        clearBtn.classList.toggle('hidden', input.value.length === 0);
      }

      clearTimeout(timer);
      timer = setTimeout(() => {
        const params = extractParamsFromURL(window.location.href, CONFIG.paramKeys);
        params.s = input.value;
        params.page = '1';
        loadPosts(params);
      }, 250);
    };

    if (clearBtn) {
      clearBtn.onclick = () => {
        input.value = '';
        clearBtn.classList.add('hidden');

        const params = extractParamsFromURL(window.location.href, CONFIG.paramKeys);
        params.s = '';
        params.page = '1';
        loadPosts(params);
      };
    }
  }

  // ----------------------
  // Click handler (filters + pagination)
  // ----------------------
  document.body.addEventListener(
      'click',
      (e) => {
        const filterLink = e.target.closest('[data-filter-link]');
        if (filterLink) {
          e.preventDefault();
          e.stopPropagation();

          const current = extractParamsFromURL(window.location.href, CONFIG.paramKeys);
          const params = { ...current };

          if ('category' in filterLink.dataset) {
            params.category = filterLink.dataset.category || '';
          }

          if ('tag' in filterLink.dataset) {
            params.tag = filterLink.dataset.tag || '';
          }

          const clearingAll =
              (!filterLink.dataset.category || filterLink.dataset.category === '') &&
              (!filterLink.dataset.tag || filterLink.dataset.tag === '');

          if (clearingAll) {
            const input = document.getElementById(CONFIG.searchInputId);
            const clearBtn = document.getElementById(CONFIG.searchClearId);
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            params.s = '';
          }

          params.page = '1';
          loadPosts(params);
          return;
        }

        const paginationLink = e.target.closest(CONFIG.paginationSelector + ' a');
        if (paginationLink) {
          e.preventDefault();

          const paramsFromLink = extractParamsFromURL(
              paginationLink.href,
              CONFIG.paramKeys
          );
          const current = extractParamsFromURL(window.location.href, CONFIG.paramKeys);
          const params = { ...current, ...paramsFromLink };

          if (!params.page) params.page = '1';
          loadPosts(params);
        }
      },
      true
  );

  // ----------------------
  // History
  // ----------------------
  window.addEventListener('popstate', (e) => {
    const params =
        e.state || extractParamsFromURL(window.location.href, CONFIG.paramKeys);
    loadPosts(params, false);
  });

  // ----------------------
  // Initial run
  // ----------------------
  const initialParams = extractParamsFromURL(
      window.location.href,
      CONFIG.paramKeys
  );
  loadPosts(initialParams, false);
  initSearch();
}
