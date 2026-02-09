(() => {
  // public/js/src/system/search.js
  function initSearch() {
    const searchInput = document.querySelector("[data-js-search]");
    if (!searchInput) return;
    const searchResults = document.querySelector("[data-js-results]");
    if (!searchResults) return;
    console.log("Running");
  }

  // public/js/src/app.js
  initSearch();
})();
