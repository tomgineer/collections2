export default function autoSearchByTerm() {
    const searchInput = document.querySelector('[data-js-search]');
    if (!searchInput) return;

    const searchTermButtons = document.querySelectorAll('button[data-search-term]');
    if (!searchTermButtons.length) return;

    searchTermButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const term = button.dataset.searchTerm?.trim();
            if (!term) return;

            searchInput.value = term;
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
            searchInput.focus();
        });
    });
}
