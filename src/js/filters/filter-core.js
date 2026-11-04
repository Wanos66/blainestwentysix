/**
 * Shared filtering core for Blog, Kitchens, Bathrooms, Bedrooms, etc.
 *
 * NOTE: This is only scaffolding. It is NOT imported anywhere yet.
 * When we begin refactoring the filters, each filter script will import
 * from here instead of duplicating logic.
 */

// Read URL parameters into an object
export function getParams() {
  const params = new URLSearchParams(window.location.search);
  const obj = {};
  params.forEach((value, key) => {
    obj[key] = value;
  });
  return obj;
}

// Update URL without refreshing
export function updateURL(params) {
  const url = new URL(window.location);
  Object.keys(params).forEach(key => {
    if (params[key]) {
      url.searchParams.set(key, params[key]);
    } else {
      url.searchParams.delete(key);
    }
  });
  window.history.pushState({}, '', url);
}

// Fetch REST results
export async function fetchResults(endpoint, params = {}) {
  const url = new URL(endpoint, window.location.origin);

  Object.entries(params).forEach(([key, val]) => {
    if (val) url.searchParams.set(key, val);
  });

  const res = await fetch(url);
  return res.json();
}

// Replace DOM content in the content-area (grid, pagination, title, chips)
export function updateContentArea(html) {
  const container = document.querySelector('#js-content-area');
  if (!container) return;
  container.innerHTML = html;
}

// Render active filter chips
export function renderChips(containerSelector, filters = {}) {
  const el = document.querySelector(containerSelector);
  if (!el) return;

  el.innerHTML = '';

  Object.entries(filters).forEach(([key, value]) => {
    if (!value) return;

    const chip = document.createElement('span');
    chip.className =
        'inline-flex items-center px-2 py-1 bg-slate-200 text-xs rounded-full';
    chip.textContent = `${key}: ${value}`;

    el.appendChild(chip);
  });
}
