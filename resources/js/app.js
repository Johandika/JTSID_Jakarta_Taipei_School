import { createIcons, ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users } from 'lucide';

const icons = { ArrowRight, ArrowUpRight, ArrowLeft, ChevronDown, ChevronLeft, ChevronRight, Search, Menu, X, Phone, Mail, MapPin, Download, CalendarDays, BookOpen, GraduationCap, Globe, Pause, Play, FileText, Users };
const refreshIcons = () => createIcons({ icons, attrs: { 'stroke-width': 1.7, 'aria-hidden': 'true' } });
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
