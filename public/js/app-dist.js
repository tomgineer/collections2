(() => {
  // public/js/src/system/search.js
  function initSearch() {
    const searchInput = document.querySelector("[data-js-search]");
    if (!searchInput) return;
    const searchResults = document.querySelector("[data-js-results]");
    if (!searchResults) return;
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content;
    if (!baseUrl) return;
    const mainSelection = document.querySelector('[data-section="main-selection"]');
    const introText = document.querySelector('[data-section="intro-text"]');
    const minLength = 2;
    const debounceMs = 250;
    let debounceTimer = null;
    let requestController = null;
    const setMainSelectionVisible = (isVisible) => {
      if (!mainSelection) return;
      mainSelection.classList.toggle("hidden", !isVisible);
    };
    const setIntroTextVisible = (isVisible) => {
      if (!introText) return;
      introText.classList.toggle("hidden", !isVisible);
    };
    const setDefaultSectionsVisible = (isVisible) => {
      setMainSelectionVisible(isVisible);
      setIntroTextVisible(isVisible);
    };
    const clearResults = () => {
      searchResults.innerHTML = "";
      setDefaultSectionsVisible(true);
    };
    const escapeHtml = (value) => String(value ?? "").replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;").replaceAll("'", "&#039;");
    const renderError = () => {
      searchResults.innerHTML = '<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>';
      setDefaultSectionsVisible(true);
    };
    const runSearch = async (term) => {
      if (requestController) {
        requestController.abort();
      }
      requestController = new AbortController();
      try {
        const query = term;
        const response = await fetch(
          `${baseUrl}ajax/search?q=${encodeURIComponent(query)}`,
          {
            headers: { "X-Requested-With": "XMLHttpRequest" },
            signal: requestController.signal
          }
        );
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }
        const rows = await response.json();
        displayResults(searchResults, rows, query, escapeHtml, setDefaultSectionsVisible);
      } catch (error) {
        if (error.name === "AbortError") {
          return;
        }
        renderError();
      }
    };
    searchInput.addEventListener("input", () => {
      const term = searchInput.value.trim();
      if (debounceTimer) {
        clearTimeout(debounceTimer);
      }
      if (term.length < minLength) {
        if (requestController) {
          requestController.abort();
        }
        clearResults();
        return;
      }
      debounceTimer = setTimeout(() => {
        runSearch(term);
      }, debounceMs);
    });
  }
  function displayResults(searchResults, rows, term, escapeHtml, setDefaultSectionsVisible) {
    if (!Array.isArray(rows) || rows.length === 0) {
      searchResults.innerHTML = '<div class="alert alert-info mt-4"><span>No results found.</span></div>';
      setDefaultSectionsVisible(true);
      return;
    }
    setDefaultSectionsVisible(false);
    const renderRows = rows.map((row) => `
        <tr>
            <td>${highlightSearchTerm(row.creator, term, escapeHtml)}</td>
            <td>${highlightSearchTerm(row.title, term, escapeHtml)}</td>
            <td>${highlightSearchTerm(row.collection, term, escapeHtml)}</td>
            <td class="text-right">
                <span class="badge badge-outline badge-secondary">${escapeHtml(row.type)}</span>
            </td>
        </tr>
    `).join("");
    searchResults.innerHTML = `
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra table-sm md:table-md">
                <thead>
                    <tr>
                        <th>Creator</th>
                        <th>Title</th>
                        <th>Collection</th>
                        <th class="text-right">Type</th>
                    </tr>
                </thead>
                <tbody>${renderRows}</tbody>
            </table>
        </div>
    `;
  }
  function highlightSearchTerm(value, term, escapeHtml) {
    const text = String(value ?? "");
    const query = String(term ?? "").trim();
    if (!query) {
      return escapeHtml(text);
    }
    const escapedRegexTerm = query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const regex = new RegExp(`(${escapedRegexTerm})`, "gi");
    const parts = text.split(regex);
    return parts.map((part, index) => index % 2 === 1 ? `<mark class="bg-yellow-300 font-semibold">${escapeHtml(part)}</mark>` : escapeHtml(part)).join("");
  }

  // public/js/src/app.js
  initSearch();
})();
