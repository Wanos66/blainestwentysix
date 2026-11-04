// src/js/blog-filters.js
// Unified REST / AJAX filtering for Blog (News) + Kitchens

document.addEventListener("DOMContentLoaded", () => {
  const contextEl = document.querySelector("[data-filter-context]");
  if (!contextEl) return;

  const context = contextEl.dataset.filterContext;

  const CONFIGS = {
    blog: {
      endpoint: "/wp-json/blt/v1/posts",
      paramKeys: ["category", "tag", "s", "page"],
      paginationSelector: "#js-pagination",
      searchInputId: "js-filter-search",
      searchClearId: "js-filter-search-clear",
    },
    kitchens: {
      endpoint: "/wp-json/blt/v1/kitchens",
      paramKeys: ["style", "finish", "brand", "s", "page"],
      paginationSelector: "#js-kitchen-pagination",
      searchInputId: "js-kitchen-filter-search",
      searchClearId: "js-kitchen-filter-search-clear",
      taxonomyParamMap: {
        kitchen_style: "style",
        kitchen_finish: "finish",
        kitchen_brand: "brand",
      },
    },
  };

  const config = CONFIGS[context];
  if (!config) return;

  let isLoading = false;

  // ----------------------
  // Helpers
  // ----------------------
  function extractParamsFromURL(urlLike, keys) {
    const u = new URL(urlLike, window.location.origin);
    const obj = {};
    keys.forEach((key) => {
      obj[key] = u.searchParams.get(key) || "";
    });
    if (!obj.page) obj.page = "1";
    return obj;
  }

  function buildApiUrl(base, params) {
    const apiUrl = new URL(base, window.location.origin);
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        apiUrl.searchParams.set(key, value);
      }
    });
    return apiUrl.toString();
  }

  function updateBrowserUrl(params) {
    const url = new URL(window.location.href);
    config.paramKeys.forEach((key) => {
      const value = params[key];
      if (!value || value === "") url.searchParams.delete(key);
      else url.searchParams.set(key, value);
    });
    window.history.pushState(params, "", url.toString());
  }

  // ----------------------
  // DOM Updaters
  // ----------------------
  function applyBlogResponse(data) {
    const contentArea = document.getElementById("js-content-area");
    if (!contentArea || !data.html) return;

    const wrapper = document.createElement("div");
    wrapper.innerHTML = data.html.trim();
    const newContent = wrapper.firstElementChild;
    if (!newContent || newContent.id !== "js-content-area") return;

    // Title block
    const oldTitle = document.getElementById("js-title-block");
    const newTitle = newContent.querySelector("#js-title-block");
    if (oldTitle && newTitle) {
      oldTitle.innerHTML = newTitle.innerHTML;
    }

    // Active filters
    const oldFilters = document.getElementById("js-active-filters");
    const newFilters = newContent.querySelector("#js-active-filters");
    if (oldFilters && newFilters) {
      oldFilters.innerHTML = newFilters.innerHTML;
    }

    // Grid
    const oldGrid = document.getElementById("js-post-grid-wrapper");
    const newGrid = newContent.querySelector("#js-post-grid-wrapper");
    if (oldGrid && newGrid) {
      oldGrid.innerHTML = newGrid.innerHTML;
    }

    // Replace tags list
    if (typeof data.tags_html === "string") {
      const tagList = document.getElementById("js-tag-list");
      if (tagList) tagList.innerHTML = data.tags_html;
    }

    // Replace categories list
    if (typeof data.cats_html === "string") {
      const catList = document.getElementById("js-category-list");
      if (catList) catList.innerHTML = data.cats_html;
    }

    // Replace pagination
    const pagContainer = document.getElementById("js-pagination");
    if (pagContainer) {
      pagContainer.innerHTML = data.pagination ? data.pagination : "";
    }

    initSearch(); // re-bind search input
  }

  function applyKitchenResponse(data) {
    // For Kitchens we only update grid + pagination
    if (typeof data.grid_html === "string") {
      const grid = document.getElementById("js-kitchen-grid");
      if (grid) grid.innerHTML = data.grid_html;
    }

    const pagContainer = document.getElementById("js-kitchen-pagination");
    if (pagContainer) {
      pagContainer.innerHTML = data.pagination ? data.pagination : "";
    }

    initSearch();
  }

  // ----------------------
  // Kitchen pill active states
  // ----------------------
  function setKitchenPillState(btn, isActive) {
    const inactiveClasses = [
      "border",
      "border-gray-300",
      "bg-white",
      "text-gray-700",
    ];
    const activeClasses = ["bg-black", "text-white", "font-semibold"];

    if (isActive) {
      inactiveClasses.forEach((c) => btn.classList.remove(c));
      activeClasses.forEach((c) => btn.classList.add(c));
    } else {
      activeClasses.forEach((c) => btn.classList.remove(c));
      inactiveClasses.forEach((c) => {
        if (!btn.classList.contains(c)) btn.classList.add(c);
      });
    }
  }

  function updateKitchenActiveStates(params) {
    if (context !== "kitchens") return;
    const map = config.taxonomyParamMap || {};
    const buttons = document.querySelectorAll(
        "[data-filter-link][data-taxonomy][data-term]"
    );

    buttons.forEach((btn) => {
      const tax = btn.dataset.taxonomy || "";
      const term = btn.dataset.term || "";
      const key = map[tax];
      if (!key) {
        setKitchenPillState(btn, false);
        return;
      }
      const currentVal = params[key] || "";
      const isActive = term === currentVal;
      setKitchenPillState(btn, isActive);
      btn.setAttribute("aria-pressed", isActive ? "true" : "false");
    });
  }

  // ----------------------
  // Fetch + apply
  // ----------------------
  async function loadPosts(params, pushState = true) {
    if (isLoading) return;
    isLoading = true;

    try {
      const apiUrl = buildApiUrl(config.endpoint, params);
      const response = await fetch(apiUrl, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });
      if (!response.ok) throw new Error(`REST error: ${response.status}`);

      const data = await response.json();

      if (context === "blog") {
        applyBlogResponse(data);
      } else if (context === "kitchens") {
        applyKitchenResponse(data);
        updateKitchenActiveStates(params);
      }

      if (pushState) {
        updateBrowserUrl(params);
      }
    } catch (err) {
      console.error("REST fetch error:", err);
    } finally {
      isLoading = false;
    }
  }

  // ----------------------
  // Search handlers
  // ----------------------
  function initSearch() {
    if (!config.searchInputId) return;
    const input = document.getElementById(config.searchInputId);
    const clearBtn = document.getElementById(config.searchClearId);
    if (!input) return;

    // Initial state
    if (clearBtn) {
      clearBtn.classList.toggle("hidden", input.value.length === 0);
    }

    let timer;
    input.oninput = () => {
      if (clearBtn) {
        clearBtn.classList.toggle("hidden", input.value.length === 0);
      }

      clearTimeout(timer);
      timer = setTimeout(() => {
        const params = extractParamsFromURL(
            window.location.href,
            config.paramKeys
        );
        params.s = input.value;
        params.page = "1";
        loadPosts(params);
      }, 250);
    };

    if (clearBtn) {
      clearBtn.onclick = () => {
        input.value = "";
        clearBtn.classList.add("hidden");

        const params = extractParamsFromURL(
            window.location.href,
            config.paramKeys
        );
        params.s = "";
        params.page = "1";
        loadPosts(params);
      };
    }
  }

  // ----------------------
  // Global click handler
  // ----------------------
  document.body.addEventListener(
      "click",
      (e) => {
        // FILTER LINKS
        const filterLink = e.target.closest("[data-filter-link]");
        if (filterLink) {
          e.preventDefault();
          e.stopPropagation();

          const current = extractParamsFromURL(
              window.location.href,
              config.paramKeys
          );
          const params = { ...current };

          if (context === "blog") {
            // Blog: category / tag
            if ("category" in filterLink.dataset) {
              params.category = filterLink.dataset.category || "";
            }
            if ("tag" in filterLink.dataset) {
              params.tag = filterLink.dataset.tag || "";
            }

            const clearingAll =
                (!filterLink.dataset.category ||
                    filterLink.dataset.category === "") &&
                (!filterLink.dataset.tag || filterLink.dataset.tag === "");

            if (clearingAll) {
              const input = document.getElementById(config.searchInputId);
              const clearBtn = document.getElementById(config.searchClearId);
              if (input) input.value = "";
              if (clearBtn) clearBtn.classList.add("hidden");
              params.s = "";
            }
          } else if (context === "kitchens") {
            const map = config.taxonomyParamMap || {};
            const tax = filterLink.dataset.taxonomy || "";
            const term = filterLink.dataset.term || "";

            if (tax && map[tax]) {
              params[map[tax]] = term;
            }

            if (filterLink.dataset.clearAll === "true") {
              // Clear all filters + search
              Object.values(map).forEach((key) => {
                params[key] = "";
              });
              const input = document.getElementById(config.searchInputId);
              const clearBtn = document.getElementById(config.searchClearId);
              if (input) input.value = "";
              if (clearBtn) clearBtn.classList.add("hidden");
              params.s = "";
            }
          }

          params.page = "1";
          loadPosts(params);
          return;
        }

        // PAGINATION LINKS
        const paginationLink = e.target.closest(
            config.paginationSelector + " a"
        );
        if (paginationLink) {
          e.preventDefault();

          const paramsFromLink = extractParamsFromURL(
              paginationLink.href,
              config.paramKeys
          );
          const current = extractParamsFromURL(
              window.location.href,
              config.paramKeys
          );

          const params = { ...current, ...paramsFromLink };
          if (!params.page) params.page = "1";

          loadPosts(params);
        }
      },
      true
  );

  // ----------------------
  // History
  // ----------------------
  window.addEventListener("popstate", (e) => {
    const params =
        e.state || extractParamsFromURL(window.location.href, config.paramKeys);
    loadPosts(params, false);
  });

  // ----------------------
  // Initial load
  // ----------------------
  const initialParams = extractParamsFromURL(
      window.location.href,
      config.paramKeys
  );
  loadPosts(initialParams, false);
  initSearch();
});
