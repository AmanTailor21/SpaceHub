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

    get sidebarClass() {
        return this.sidebarOpen ? 'translate-x-0' : '-translate-x-full';
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

Alpine.data('dialog', () => ({
    returnFocus: null,

    openDialog(event) {
        const dialog = this.$refs.dialog;

        if (event.detail?.id !== dialog.id || dialog.open) {
            return;
        }

        this.returnFocus = document.activeElement;
        dialog.showModal();
        dialog.querySelector('[autofocus], button, input, select, textarea, a[href]')?.focus();
    },

    closeDialog(event) {
        const dialog = this.$refs.dialog;

        if (event?.detail?.id && event.detail.id !== dialog.id) {
            return;
        }

        if (dialog.open) {
            dialog.close();
        }
    },

    onDialogClose() {
        this.returnFocus?.focus?.();
        this.returnFocus = null;
    },
}));

Alpine.data('toast', () => ({
    visible: false,
    message: '',
    timeout: null,

    showToast(event) {
        this.message = event.detail?.message ?? '';
        this.visible = true;
        window.clearTimeout(this.timeout);
        this.timeout = window.setTimeout(() => {
            this.visible = false;
        }, 5000);
    },

    dismiss() {
        this.visible = false;
        window.clearTimeout(this.timeout);
    },
}));

Alpine.start();
