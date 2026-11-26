import './bootstrap';
import Alpine from '@alpinejs/csp';

const themeKey = 'spacehub-theme';
const themePreference = localStorage.getItem(themeKey) ?? 'system';

function applyTheme(preference) {
    const useDark = preference === 'dark'
        || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    document.documentElement.classList.toggle('dark', useDark);
}

applyTheme(themePreference);

window.Alpine = Alpine;

Alpine.data('appShell', () => ({
    sidebarOpen: false,

    toggleSidebar() {
        this.sidebarOpen = ! this.sidebarOpen;
    },

    closeSidebar() {
        this.sidebarOpen = false;
    },
}));

Alpine.data('themeToggle', () => ({
    preference: localStorage.getItem(themeKey) ?? 'system',

    cycle() {
        const preferences = ['system', 'light', 'dark'];
        const nextIndex = (preferences.indexOf(this.preference) + 1) % preferences.length;
        this.preference = preferences[nextIndex];
        localStorage.setItem(themeKey, this.preference);
        applyTheme(this.preference);
    },

    get label() {
        return `Theme: ${this.preference}. Activate to change theme.`;
    },
}));

Alpine.start();
