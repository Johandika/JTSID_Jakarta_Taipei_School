import { createIcons, ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users } from 'lucide';

const icons = { ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users };
const refreshIcons = () => createIcons({ icons, attrs: { 'stroke-width': 1.7, 'aria-hidden': 'true' } });

const showAnnouncement = () => {
    const dialog = document.querySelector('[data-announcement-popup]');
    if (!dialog || dialog.dataset.initialized || typeof dialog.showModal !== 'function') return;
    dialog.dataset.initialized = 'true';
    const key = `jts-announcement-${dialog.dataset.announcementPopup}`;
    try { if (sessionStorage.getItem(key)) return; } catch { /* Storage can be disabled by the visitor. */ }

    const previousFocus = document.activeElement;
    const previousOverflow = document.body.style.overflow;
    dialog.querySelectorAll('[data-close-popup]').forEach(button => button.addEventListener('click', () => dialog.close()));
    dialog.addEventListener('click', event => {
        const bounds = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
    });
    dialog.addEventListener('close', () => {
        document.body.style.overflow = previousOverflow;
        if (previousFocus instanceof HTMLElement) previousFocus.focus();
    });
    dialog.showModal();
    document.body.style.overflow = 'hidden';
    try { sessionStorage.setItem(key, 'shown'); } catch { /* Keep the popup usable without browser storage. */ }
};
document.addEventListener('DOMContentLoaded', showAnnouncement);
document.addEventListener('livewire:navigated', showAnnouncement);
document.addEventListener('DOMContentLoaded', refreshIcons);
document.addEventListener('livewire:navigated', refreshIcons);
document.addEventListener('livewire:init', () => {
    Livewire.hook('morphed', refreshIcons);
});
document.addEventListener('alpine:init', () => {
    Alpine.data('slideshow', (count) => ({
        current: 0, paused: window.matchMedia('(prefers-reduced-motion: reduce)').matches, timer: null,
        init() { this.timer = setInterval(() => { if (!this.paused && !document.hidden) this.next(); }, 5500); },
        next() { this.current = (this.current + 1) % count; },
        previous() { this.current = (this.current - 1 + count) % count; },
        destroy() { clearInterval(this.timer); },
    }));
});
