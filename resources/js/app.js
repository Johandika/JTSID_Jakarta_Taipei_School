import { createIcons, ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, Calendar, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users, Check, Languages, Award, Newspaper, Trophy } from 'lucide';

const icons = { ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, Calendar, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users, Check, Languages, Award, Newspaper, Trophy };
const refreshIcons = () => createIcons({ icons, attrs: { 'stroke-width': 1.7, 'aria-hidden': 'true' } });
const announcementIdleDelay = 90_000;
let cancelAnnouncementWait = () => {};

const showAnnouncement = () => {
    const dialog = document.querySelector('[data-announcement-popup]');
    if (!dialog) {
        cancelAnnouncementWait();
        return;
    }
    if (dialog.dataset.initialized || typeof dialog.showModal !== 'function') return;

    cancelAnnouncementWait();
    dialog.dataset.initialized = 'true';
    const key = `jts-announcement-${dialog.dataset.announcementPopup}-${dialog.dataset.announcementVersion}`;
    let state = null;
    try { state = sessionStorage.getItem(key); } catch { /* Storage can be disabled by the visitor. */ }
    if (state === 'complete') return;

    let deadline;
    let stage;
    let timer;
    let previousFocus;
    let previousOverflow;
    const activityEvents = ['pointermove', 'pointerdown', 'keydown', 'scroll', 'touchstart'];
    const stopWaiting = () => {
        clearTimeout(timer);
        activityEvents.forEach(event => window.removeEventListener(event, resetTimer));
        document.removeEventListener('visibilitychange', resumeTimer);
        cancelAnnouncementWait = () => {};
    };
    const storeState = value => {
        state = value;
        try { sessionStorage.setItem(key, value); } catch { /* Keep the popup usable without browser storage. */ }
    };
    const openDialog = nextStage => {
        stopWaiting();
        stage = nextStage;
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        dialog.showModal();
        document.body.style.overflow = 'hidden';
        storeState(stage === 'initial' ? 'initial-shown' : 'complete');
    };
    function scheduleTimer() {
        clearTimeout(timer);
        const remaining = Math.max(0, deadline - Date.now());
        timer = setTimeout(() => openDialog('idle'), remaining);
    }
    function resetTimer() {
        deadline = Date.now() + announcementIdleDelay;
        scheduleTimer();
    }
    function resumeTimer() {
        if (!document.hidden) scheduleTimer();
    }

    dialog.querySelectorAll('[data-close-popup]').forEach(button => button.addEventListener('click', () => dialog.close()));
    dialog.addEventListener('click', event => {
        const bounds = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
    });
    dialog.addEventListener('close', () => {
        document.body.style.overflow = previousOverflow;
        if (previousFocus instanceof HTMLElement) previousFocus.focus();
        if (stage === 'initial' && state !== 'complete') {
            activityEvents.forEach(event => window.addEventListener(event, resetTimer, { passive: true }));
            document.addEventListener('visibilitychange', resumeTimer);
            cancelAnnouncementWait = stopWaiting;
            resetTimer();
        }
    });

    if (state !== 'initial-shown') {
        openDialog('initial');
        return;
    }

    activityEvents.forEach(event => window.addEventListener(event, resetTimer, { passive: true }));
    document.addEventListener('visibilitychange', resumeTimer);
    cancelAnnouncementWait = stopWaiting;
    resetTimer();
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
