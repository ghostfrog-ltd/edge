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
