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

    const baseUrl = document.querySelector('meta[name="base-url"]')?.content;
    if (!baseUrl) return;

    const minLength = 2;
    const debounceMs = 250;
    let debounceTimer = null;
    let requestController = null;

    const setSearchResultsVisible = (isVisible) => {
        searchResults.classList.toggle('hidden', !isVisible);
    };

    setSearchResultsVisible(false);

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
/**
 * Render either an empty-state notice or the full search results table.
 *
 * @param {HTMLElement} searchResults Results container element.
 * @param {Array<{creator?: string, title?: string, collection?: string, type?: string}>} rows Search rows.
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
            <td>${highlightSearchTerm(row.creator, term, escapeHtml)}</td>
            <td>${highlightSearchTerm(row.title, term, escapeHtml)}</td>
            <td>${highlightSearchTerm(row.collection, term, escapeHtml)}</td>
            <td class="text-right">
                <span class="badge badge-secondary badge-sm font-heading font-bold">${escapeHtml(row.type)}</span>
            </td>
        </tr>
    `).join('');

    searchResults.innerHTML = `
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra lg:text-base">
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