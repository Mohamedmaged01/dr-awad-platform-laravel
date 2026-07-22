import './bootstrap';

import Alpine from 'alpinejs';

// Global dark-mode store shared by every theme toggle (Header + admin top bar).
// Initial `.dark` class is set by the inline head script before paint (no FOUC).
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    });
});

window.Alpine = Alpine;

Alpine.start();
