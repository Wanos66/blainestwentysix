/**
 * Shared navigation state manager.
 *
 * NOTE: Not yet imported anywhere – safe to scaffold.
 */

export const navState = {
  openMenu: null,  // e.g., 'mobile', 'mega', 'submenu-3'
  listeners: [],

  set(menu) {
    this.openMenu = menu;
    this.notify();
  },

  clear() {
    this.openMenu = null;
    this.notify();
  },

  onChange(callback) {
    this.listeners.push(callback);
    return () => {
      this.listeners = this.listeners.filter(fn => fn !== callback);
    };
  },

  notify() {
    this.listeners.forEach(fn => fn(this.openMenu));
  }
};
