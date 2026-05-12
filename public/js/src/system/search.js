/**
 * Initialize debounced AJAX search and wire results rendering to the search input.
 *
 * @returns {void}
 */
export default function initSearch() {
    const searchInput = document.querySelector('[data-js-search]');
    if (!searchInput) return;

    const searchResults = document.querySelector('[data-js-results]');
    if (!searchResults) return;

    const clearButton = document.querySelector('[data-js-search-clear]');
    if (!clearButton) return;

    const baseUrl = document.querySelector('meta[name="base-url"]')?.content;
    if (!baseUrl) return;

    const minLength = 2;
    const debounceMs = 250;
    let debounceTimer = null;
    let requestController = null;

    const setSearchResultsVisible = (isVisible) => {
        searchResults.classList.toggle('hidden', !isVisible);
    };

    const syncClearButtonVisibility = () => {
        if (!clearButton) return;

        const hasValue = searchInput.value.trim().length > 0;
        clearButton.classList.toggle('hidden', !hasValue);
    };

    setSearchResultsVisible(false);
    syncClearButtonVisibility();

    const clearResults = () => {
        searchResults.innerHTML = '';
        setSearchResultsVisible(false);
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    // Show a compact error state when the request fails.
    const renderError = () => {
        searchResults.innerHTML = '<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>';
        setSearchResultsVisible(false);
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: requestController.signal,
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const rows = await response.json();
            displayResults(searchResults, rows, query, escapeHtml, setSearchResultsVisible);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            renderError();
        }
    };

    searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim();
        syncClearButtonVisibility();

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

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            if (requestController) {
                requestController.abort();
            }

            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            searchInput.value = '';
            clearResults();
            syncClearButtonVisibility();
            searchInput.focus();
        });
    }

}
/**
 * Render either an empty-state notice or the full search results table.
 *
 * @param {HTMLElement} searchResults Results container element.
 * @param {Array<{creator?: string, title?: string, format?: string, category?: string}>} rows Search rows.
 * @param {string} term Search term.
 * @param {(value: unknown) => string} escapeHtml HTML escaping helper.
 * @param {(isVisible: boolean) => void} setSearchResultsVisible Results visibility helper.
 * @returns {void}
 */
function displayResults(searchResults, rows, term, escapeHtml, setSearchResultsVisible) {
    if (!Array.isArray(rows) || rows.length === 0) {
        searchResults.innerHTML = '';
        setSearchResultsVisible(false);
        return;
    }

    setSearchResultsVisible(true);

    const renderRows = rows.map((row) => `
        <tr>
            <td class="px-2 py-2 lg:px-4 lg:py-3">${highlightSearchTerm(row.creator, term, escapeHtml)}</td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">${highlightSearchTerm(row.title, term, escapeHtml)}</td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">
                <span class="font-heading font-bold">${escapeHtml(row.category)}</span>
            </td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">
                <span class="badge badge-primary badge-sm font-heading font-bold whitespace-nowrap">${escapeHtml(row.format)}</span>
            </td>
        </tr>
    `).join('');

    searchResults.innerHTML = `
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm table-zebra lg:table-md lg:text-base">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Artist</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Title</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Category</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Format</th>
                    </tr>
                </thead>
                <tbody>${renderRows}</tbody>
            </table>
        </div>
    `;
}

/**
 * Highlight query matches inside a text value while preserving HTML escaping.
 *
 * @param {unknown} value Source text value.
 * @param {string} term Active search term.
 * @param {(value: unknown) => string} escapeHtml HTML escaping helper.
 * @returns {string} Escaped HTML string with matched parts wrapped in <mark>.
 */
function highlightSearchTerm(value, term, escapeHtml) {
    const text = String(value ?? '');
    const query = String(term ?? '').trim();
    if (!query) {
        return escapeHtml(text);
    }

    const escapedRegexTerm = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedRegexTerm})`, 'gi');
    const parts = text.split(regex);

    return parts
        .map((part, index) => (
            index % 2 === 1
                ? `<mark class="bg-primary text-white px-1">${escapeHtml(part)}</mark>`
                : escapeHtml(part)
        ))
        .join('');
}
