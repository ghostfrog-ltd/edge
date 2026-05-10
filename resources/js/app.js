import './bootstrap';

window.ghostfrogTheme = {
    apply(theme) {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const resolvedTheme = theme === 'system' ? (prefersDark ? 'dark' : 'light') : theme;

        document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
        document.documentElement.style.colorScheme = resolvedTheme;
        localStorage.setItem('ghostfrog-theme', theme);

        return resolvedTheme;
    },
    current() {
        return localStorage.getItem('ghostfrog-theme') || 'system';
    },
    toggle() {
        const currentTheme = this.current();
        const resolvedTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        const nextTheme = currentTheme === 'system'
            ? (resolvedTheme === 'dark' ? 'light' : 'dark')
            : (currentTheme === 'dark' ? 'light' : 'dark');

        return this.apply(nextTheme);
    },
};

window.ghostfrogCategorySuggestions = function ghostfrogCategorySuggestions(config) {
    return {
        keyword: config.keyword ?? '',
        marketplace: config.marketplace ?? 'ebay-uk',
        selectedCategoryId: config.selectedCategoryId ?? '',
        selectedCategoryLabel: config.selectedCategoryLabel ?? '',
        suggestions: config.initialSuggestions ?? [],
        loading: false,
        error: '',
        async fetchSuggestions() {
            this.error = '';

            if (! this.keyword.trim()) {
                this.error = 'Enter a keyword or niche first so we can ask eBay for matching categories.';
                this.suggestions = [];
                return;
            }

            this.loading = true;

            try {
                const response = await window.axios.get(config.endpoint, {
                    params: {
                        keyword: this.keyword,
                        marketplace: this.marketplace,
                    },
                });

                this.suggestions = response.data.suggestions ?? [];

                if (this.suggestions.length === 0) {
                    this.error = 'eBay returned no category suggestions for that keyword on this marketplace.';
                    this.selectedCategoryId = '';
                    this.selectedCategoryLabel = '';
                } else if (! this.suggestions.some((suggestion) => suggestion.id === this.selectedCategoryId)) {
                    this.selectedCategoryId = this.suggestions[0].id;
                    this.selectedCategoryLabel = this.suggestions[0].label;
                }
            } catch (error) {
                this.suggestions = [];
                this.selectedCategoryId = '';
                this.selectedCategoryLabel = '';
                this.error = error.response?.data?.message ?? 'Could not load categories from eBay right now.';
            } finally {
                this.loading = false;
            }
        },
        syncSelectedLabel() {
            const selected = this.suggestions.find((suggestion) => suggestion.id === this.selectedCategoryId);
            this.selectedCategoryLabel = selected ? selected.label : '';
        },
    };
};
