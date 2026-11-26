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

const dialogBehavior = () => ({
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
});

Alpine.data('dialog', dialogBehavior);

Alpine.data('toast', () => ({
    visible: false,
    message: '',
    variant: 'success',
    timeout: null,

    init() {
        const message = this.$el.dataset.initialMessage;

        if (message) {
            this.showToast({
                detail: {
                    message,
                    variant: this.$el.dataset.variant ?? 'success',
                },
            });
        }
    },

    showToast(event) {
        this.message = event.detail?.message ?? '';
        this.variant = event.detail?.variant ?? 'success';
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

Alpine.data('bookingDrawer', () => ({
    duration: '',

    init() {
        this.updateDuration();

        if (this.$el.dataset.openOnLoad === 'true') {
            window.setTimeout(() => this.openDrawer(), 0);
        }
    },

    openBooking(event) {
        const booking = event.detail ?? {};

        if (booking.roomId) {
            this.$refs.room.value = booking.roomId;
        }

        if (booking.startTime) {
            this.$refs.startTime.value = booking.startTime;
        }

        if (booking.endTime) {
            this.$refs.endTime.value = booking.endTime;
        }

        this.updateDuration();
        this.openDrawer();
    },

    openDrawer() {
        this.$dispatch('spacehub:dialog-open', { id: 'booking-create' });
        this.$nextTick(() => {
            const invalidField = this.$el.querySelector('[aria-invalid="true"]');
            (invalidField ?? this.$refs.title).focus();
        });
    },

    updateDuration() {
        const start = new Date(this.$refs.startTime.value);
        const end = new Date(this.$refs.endTime.value);
        const minutes = (end - start) / 60000;

        this.duration = Number.isFinite(minutes) && minutes > 0
            ? `${Math.floor(minutes / 60)}h ${minutes % 60}m`
            : '';
    },
}));

Alpine.data('bookingDetails', () => ({
    booking: {},

    openDetails(event) {
        this.booking = event.detail ?? {};
        this.$dispatch('spacehub:dialog-open', { id: 'booking-details' });
    },

    askCancel() {
        this.$dispatch('spacehub:dialog-open', { id: 'cancel-confirm' });
    },

    acceptCancel(event) {
        if (event.detail?.id !== 'cancel-confirm') {
            return;
        }

        this.$dispatch('spacehub:dialog-close', { id: 'cancel-confirm' });
        this.$refs.cancelForm.requestSubmit();
    },
}));

Alpine.data('confirmDialog', () => ({
    ...dialogBehavior(),

    accept() {
        this.$dispatch('spacehub:confirm-accepted', {
            id: this.$el.dataset.confirmId,
        });
    },
}));

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
        return;
    }

    const detailsTrigger = event.target.closest('[data-open-booking-details]');

    if (detailsTrigger) {
        event.preventDefault();
        document.dispatchEvent(new CustomEvent('spacehub:booking-details', {
            detail: {
                id: detailsTrigger.dataset.bookingId,
                title: detailsTrigger.dataset.bookingTitle,
                bookedBy: detailsTrigger.dataset.bookedBy,
                room: detailsTrigger.dataset.roomName,
                startsAt: detailsTrigger.dataset.startsAt,
                endsAt: detailsTrigger.dataset.endsAt,
                status: detailsTrigger.dataset.bookingStatus,
                cancelUrl: detailsTrigger.dataset.cancelUrl,
            },
        }));

        return;
    }

    const bookingTrigger = event.target.closest('[data-open-booking]');

    if (bookingTrigger && document.getElementById('booking-create')) {
        event.preventDefault();
        document.dispatchEvent(new CustomEvent('spacehub:booking-open', {
            detail: {
                roomId: bookingTrigger.dataset.roomId,
                startTime: bookingTrigger.dataset.startTime,
                endTime: bookingTrigger.dataset.endTime,
            },
        }));
    }
});

Alpine.start();
