@props(['title' => null, 'description' => null, 'image' => null, 'transparentNav' => false])
@php
    $site = \App\Support\Site::class;
    $contact = config('site.contact');
    $headerNavigation = [
        [
            'title' => __('site.about'),
            'url' => null,
            'children' => [
                ['title' => __('site.board_of_directors'), 'url' => '/board-of-directors'],
                ['title' => __('site.school_profile'), 'url' => '/school-profile'],
                ['title' => __('site.facilities'), 'url' => '/facilities'],
                ['title' => __('site.staff'), 'url' => '/staff'],
                ['title' => __('site.contact'), 'url' => '/contact'],
            ],
        ],
        ['title' => __('site.academics'), 'url' => '/academics', 'children' => []],
        [
            'title' => __('site.enrollment'),
            'url' => null,
            'children' => [
                ['title' => __('site.school_admissions'), 'url' => '/admissions'],
                ['title' => __('site.mandarin_program'), 'url' => '/mandarin-program'],
            ],
        ],
        ['title' => __('site.news'), 'url' => '/news', 'children' => []],
        ['title' => __('site.resources'), 'url' => '/resources', 'children' => []],
        ['title' => __('site.universities'), 'url' => '/university-admission', 'children' => []],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' | ' : '' }}Jakarta Taipei School</title>
    <meta name="description" content="{{ $description ?: __('site.hero_copy') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach(['en', 'zh-TW', 'id', 'x-default'] as $alternate)
        <link rel="alternate" hreflang="{{ $alternate }}"
            href="{{ url($site::switchLocale($alternate === 'x-default' ? 'en' : $alternate)) }}">
    @endforeach
    <meta property="og:title" content="{{ $title ?: 'Jakarta Taipei School' }}">
    <meta property="og:description" content="{{ $description ?: __('site.hero_copy') }}">
    <meta property="og:image" content="{{ $image ?: asset('images/campus.webp') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ asset('images/jtsidlogo.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-white text-ink antialiased">
    <a href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:p-4">{{ __('site.skip') }}</a>

    <header x-data="{ mobile: false, scrolled: false, isTransparent: {{ $transparentNav ? 'true' : 'false' }} }"
        x-init="if (isTransparent) { scrolled = window.pageYOffset > 30; }"
        @scroll.window="if (isTransparent) { scrolled = window.pageYOffset > 30; }" :class="{
            'fixed top-0 inset-x-0 z-50 transition-all duration-300': isTransparent,
            'sticky top-0 z-50 bg-white transition-all duration-300': !isTransparent,
            'bg-white/95 backdrop-blur-md shadow-md text-ink border-b border-line': scrolled || !isTransparent,
            'bg-gradient-to-b from-black/80 via-black/40 to-transparent text-white border-b border-white/10': !scrolled && isTransparent
        }">

        {{-- Utility Bar --}}
        <div :class="{
            'bg-jts text-white': scrolled || !isTransparent,
            'bg-transparent text-white border-b border-white/15': !scrolled && isTransparent
         }" class="transition-colors duration-300">
            <div class="shell flex min-h-9 items-center justify-between gap-2 text-xs">
                <a class="flex min-w-0 items-center gap-2 py-2 transition-opacity hover:opacity-80"
                    href="{{ $contact['phones']['primary']['url'] }}">
                    <x-site-icon name="phone" class="size-3.5" />
                    <span class="hidden sm:inline">{{ __('site.admissions') }}</span>
                    <span class="whitespace-nowrap">{{ $contact['phones']['primary']['display'] }}</span>
                </a>

                <div class="flex shrink-0 items-center gap-3 sm:gap-4">
                    <div class="relative hidden md:block" x-data="{ open: false }"
                        @click.outside="open = false"
                        @keydown.escape.stop.prevent="open = false; $refs.trigger.focus()"
                        @focusout="if (!$el.contains($event.relatedTarget)) { open = false; }">
                        <button type="button" x-ref="trigger" @click="open = !open"
                            aria-expanded="false" :aria-expanded="open" aria-controls="header-whatsapp-options"
                            aria-label="{{ __('site.whatsapp_select_contact') }}"
                            class="inline-flex items-center gap-1 py-2 transition-opacity hover:opacity-80">
                            <span>WhatsApp</span>
                            <x-site-icon name="chevron-down" class="size-3" />
                        </button>
                        <div id="header-whatsapp-options" x-show="open" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute right-0 top-full z-50 w-72 rounded-lg border border-line bg-white p-2 text-ink shadow-xl">
                            @foreach($contact['whatsapp'] as $channel => $whatsapp)
                                <a href="{{ $whatsapp['url'] }}" target="_blank" rel="noopener noreferrer"
                                    @click="open = false"
                                    class="block rounded-md px-3 py-2.5 transition-colors hover:bg-gray-50 hover:text-jts focus-visible:bg-gray-50 focus-visible:text-jts">
                                    <span class="block text-sm font-semibold">{{ __('site.footer_whatsapp_' . $channel) }}</span>
                                    <span class="mt-1 block text-xs">{{ $whatsapp['display'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <a class="hidden md:inline transition-opacity hover:opacity-80"
                        href="{{ $contact['social']['instagram'] }}" target="_blank" rel="noopener noreferrer">Instagram</a>
                    <nav aria-label="Language" class="flex items-center gap-2 sm:gap-3">
                        @foreach(['en' => 'EN', 'zh-TW' => '繁中', 'id' => 'ID'] as $code => $label)
                            <a href="{{ $site::switchLocale($code) }}" lang="{{ $code }}"
                                class="py-1.5 px-1 rounded text-xs transition-all {{ app()->getLocale() === $code ? 'font-bold underline underline-offset-4' : 'opacity-80 hover:opacity-100' }}"
                                @if(app()->getLocale() === $code) aria-current="true" @endif>{{ $label }}</a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>

        {{-- Main Navbar --}}
        <div class="shell flex min-h-20 sm:min-h-22 items-center gap-3 transition-all duration-300">
            <a href="{{ $site::path('/') }}" aria-label="Jakarta Taipei School" class="group flex min-w-0 shrink items-center">
                <img src="{{ asset('images/logo-white.webp') }}" alt="Jakarta Taipei School" width="234" height="57"
                    x-show="!scrolled && isTransparent"
                    class="h-auto w-40 max-w-[calc(100vw-6.5rem)] sm:w-56 transition-opacity duration-300 drop-shadow-[0_2px_6px_rgba(0,0,0,0.4)]">
                <img src="{{ asset('images/logo.webp') }}" alt="Jakarta Taipei School" width="234" height="57"
                    x-show="scrolled || !isTransparent"
                    class="h-auto w-40 max-w-[calc(100vw-6.5rem)] sm:w-56 transition-opacity duration-300">
            </a>

            <nav class="ml-auto hidden items-center gap-6 xl:flex" aria-label="{{ __('site.main_navigation') }}">
                @foreach($headerNavigation as $item)
                    @php($isActive = $item['url'] ? request()->getPathInfo() === $site::path($item['url']) : collect($item['children'])->contains(fn ($child) => request()->getPathInfo() === $site::path($child['url'])))
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false"
                        @keydown.escape.window="open = false">
                        <div class="flex items-center gap-1">
                            @if($item['url'])
                                <a href="{{ $site::path($item['url']) }}" :class="{
                                   'text-white hover:text-white/80 font-medium': !scrolled && isTransparent,
                                   'text-ink hover:text-jts font-semibold': scrolled || !isTransparent,
                                   'text-accent font-bold': (!scrolled && isTransparent) && @js($isActive),
                                   'text-jts': (scrolled || !isTransparent) && @js($isActive)
                               }" class="py-6 text-[14px] transition-colors">
                                    {{ $item['title'] }}
                                </a>
                            @endif
                            @if($item['children'])
                                <button type="button" @click="open = !open" :aria-expanded="open"
                                    @if(! $item['url']) aria-controls="{{ $loop->first ? 'about-desktop-submenu' : 'enrollment-desktop-submenu' }}" @endif
                                    aria-label="{{ $item['title'] }}" :class="{
                                                'text-white hover:text-white/80': !scrolled && isTransparent,
                                                'text-ink hover:text-jts': scrolled || !isTransparent,
                                                'text-accent': (!scrolled && isTransparent) && @js($isActive),
                                                'text-jts': (scrolled || !isTransparent) && @js($isActive)
                                            }" class="inline-flex items-center gap-1 py-6 text-[14px] transition-colors {{ $item['url'] ? 'p-1' : 'font-semibold' }}">
                                    @if(! $item['url'])<span>{{ $item['title'] }}</span>@endif
                                    <x-site-icon name="chevron-down" class="size-3" />
                                </button>
                            @endif
                        </div>
                        @if($item['children'])
                            <div @if(! $item['url']) id="{{ $loop->first ? 'about-desktop-submenu' : 'enrollment-desktop-submenu' }}" @endif x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute left-0 top-full w-60 rounded-lg border border-line bg-white p-2 shadow-xl text-ink">
                                @foreach($item['children'] as $child)
                                    <a href="{{ $site::path($child['url']) }}"
                                        class="block rounded-md px-3 py-2.5 text-sm font-medium text-ink hover:bg-gray-50 hover:text-jts transition-colors">
                                        {{ $child['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            {{-- Mobile Menu Trigger --}}
            <button class="icon-btn ml-auto xl:hidden transition-all duration-300" :class="{
                    'bg-white/20 border-white/30 text-white hover:bg-white/30': !scrolled && isTransparent,
                    'bg-white border-line text-ink hover:bg-gray-50': scrolled || !isTransparent
                }" @click="mobile = !mobile" :aria-expanded="mobile" aria-controls="mobile-menu"
                aria-label="{{ __('site.menu') }}">
                <x-site-icon name="menu" class="size-5" />
            </button>
        </div>

        {{-- Mobile Menu Drawer --}}
        <nav id="mobile-menu" x-show="mobile" x-cloak @keydown.escape.window="mobile = false"
            class="max-h-[75svh] overflow-y-auto border-t border-line bg-white text-ink px-6 pb-6 pt-2 shadow-2xl backdrop-blur-lg xl:hidden"
            aria-label="{{ __('site.mobile_navigation') }}">
            @foreach($headerNavigation as $item)
                <div class="border-b border-line/60 py-2" @if(! $item['url']) x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" @endif>
                    @if($item['url'])
                        <a href="{{ $site::path($item['url']) }}"
                            class="block py-2.5 font-semibold text-ink hover:text-jts text-base">{{ $item['title'] }}</a>
                    @else
                        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="{{ $loop->first ? 'about-mobile-submenu' : 'enrollment-mobile-submenu' }}"
                            class="flex w-full items-center justify-between py-2.5 text-left text-base font-semibold text-ink hover:text-jts">
                            <span>{{ $item['title'] }}</span><x-site-icon name="chevron-down" class="size-4" />
                        </button>
                    @endif
                    @if($item['children'])
                        <div @if(! $item['url']) id="{{ $loop->first ? 'about-mobile-submenu' : 'enrollment-mobile-submenu' }}" x-show="open" x-cloak @endif class="space-y-1 pb-1 pl-4">
                            @foreach($item['children'] as $child)
                                <a href="{{ $site::path($child['url']) }}"
                                    class="block py-2 text-sm text-muted hover:text-jts">{{ $child['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
    </header>
    <main id="main">{{ $slot }}</main>
    <footer class="mt-16 relative text-white overflow-hidden bg-[#071d13] border-t border-emerald-950">
        {{-- Background Campus Image with Visible Architecture & Slightly Darker Forest Overlay --}}
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="{{ asset('images/campus.webp') }}" alt="JTS Campus"
                class="w-full h-full object-cover object-center brightness-75 contrast-110">
            <div class="absolute inset-0 bg-gradient-to-b from-[#08281a]/82 via-[#051e13]/86 to-[#02100a]/92"></div>
        </div>

        {{-- Main Top Content (3 Columns Layout) --}}
        <div class="relative z-10 shell py-12 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_1.3fr_1fr] lg:grid-cols-[1.1fr_1.4fr_1.1fr] gap-10 md:gap-8 items-start">
                {{-- Left Column: Campus Address & Contact Info --}}
                <div class="space-y-4 text-left">
                    <h3 class="text-xs font-bold tracking-widest text-emerald-400 uppercase">{{ __('site.footer_campus_office') }}</h3>
                    <div class="space-y-1 text-sm text-gray-200 leading-relaxed drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]">
                        <p class="font-semibold text-white tracking-wide">Jakarta Taipei School (JTS)</p>
                        <p class="text-gray-300">{{ $contact['address']['street'] }}</p>
                        <p class="text-gray-300">{{ $contact['address']['city'] }}</p>
                        <p class="font-semibold tracking-wider text-emerald-300 uppercase">{{ $contact['address']['country'] }}</p>
                    </div>

                    <div class="pt-2 space-y-1.5 text-sm text-gray-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]">
                        <p class="flex items-center gap-2">
                            <span class="text-emerald-400 font-semibold text-xs tracking-wider">T:</span>
                            <a href="{{ $contact['phones']['primary']['url'] }}" class="hover:text-emerald-300 transition-colors">{{ $contact['phones']['primary']['display'] }}</a>
                            <span class="text-white/40">/</span>
                            <a href="{{ $contact['phones']['secondary']['url'] }}" class="hover:text-emerald-300 transition-colors">{{ $contact['phones']['secondary']['display'] }}</a>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="text-emerald-400 font-semibold text-xs tracking-wider">E:</span>
                            <a href="{{ $contact['email']['url'] }}" class="hover:text-emerald-300 transition-colors">{{ $contact['email']['display'] }}</a>
                        </p>
                        @foreach($contact['whatsapp'] as $channel => $whatsapp)
                            <p class="pt-1">
                                <span class="block text-xs font-semibold text-emerald-300">{{ __('site.footer_whatsapp_'.$channel) }}</span>
                                <a href="{{ $whatsapp['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-300 transition-colors">{{ $whatsapp['display'] }}</a>
                            </p>
                        @endforeach
                    </div>
                </div>

                {{-- Center Column: Brand Identity, Logo & Social Hub --}}
                <div class="flex flex-col items-center justify-center text-center px-2">
                    <a href="{{ $site::path('/') }}" class="inline-block transition-transform hover:scale-[1.02] duration-200" title="Jakarta Taipei School">
                        <img src="{{ asset('images/logo-white.webp') }}"
                            alt="Jakarta Taipei School"
                            width="280" height="70"
                            class="h-14 md:h-16 w-auto max-w-[280px] object-contain drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)] mx-auto brightness-110">
                    </a>

                    <p class="mt-3 text-[11px] font-semibold tracking-wider text-emerald-200/90 uppercase max-w-xs drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]">
                        Pendidikan Tiga Bahasa • Kurikulum Terpadu Taiwan &amp; Nasional
                    </p>

                    {{-- Social Icons & QR Trigger (BSJ Style Clean Buttons) --}}
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5">
                        {{-- Instagram --}}
                        <a href="{{ $contact['social']['instagram'] }}"
                            target="_blank" rel="noopener noreferrer"
                            class="size-11 rounded-lg border border-white/20 bg-white/10 hover:bg-white/25 hover:border-emerald-400 text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 backdrop-blur-sm"
                            title="Instagram @jakartataipeischoolofficial" aria-label="Instagram">
                            <svg class="size-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        {{-- TikTok --}}
                        <a
                            @if(filled($contact['social']['tiktok']))
                                href="{{ $contact['social']['tiktok'] }}" target="_blank" rel="noopener noreferrer"
                            @else
                                aria-disabled="true"
                            @endif
                            class="size-11 rounded-lg border border-white/20 bg-white/10 hover:bg-white/25 hover:border-emerald-400 text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 backdrop-blur-sm"
                            title="TikTok Jakarta Taipei School" aria-label="TikTok">
                            <svg class="size-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12.53 2h3.53c.27 2.23 1.5 3.65 3.64 4.04v3.59a7.85 7.85 0 0 1-3.62-1.04v6.93a6.15 6.15 0 1 1-5.3-6.1v3.66a2.67 2.67 0 1 0 1.75 2.52V2Z"/>
                            </svg>
                        </a>

                        {{-- YouTube --}}
                        <a href="{{ $contact['social']['youtube'] }}"
                            target="_blank" rel="noopener noreferrer"
                            class="size-11 rounded-lg border border-white/20 bg-white/10 hover:bg-white/25 hover:border-emerald-400 text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 backdrop-blur-sm"
                            title="YouTube Jakarta Taipei School" aria-label="YouTube">
                            <svg class="size-5 fill-current" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>

                        {{-- Barcode / QR Trigger Button --}}
                        <button type="button"
                            onclick="document.getElementById('jts-qr-modal')?.showModal()"
                            class="size-11 rounded-lg border border-white/20 bg-white/10 hover:bg-white/25 hover:border-emerald-400 text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 cursor-pointer backdrop-blur-sm"
                            title="{{ __('site.footer_qr_button') }}" aria-label="{{ __('site.footer_qr_button') }}">
                            <svg class="size-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="5" height="5" x="3" y="3" rx="1"/>
                                <rect width="5" height="5" x="16" y="3" rx="1"/>
                                <rect width="5" height="5" x="3" y="16" rx="1"/>
                                <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
                                <path d="M21 21v.01"/>
                                <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
                                <path d="M3 12h.01"/>
                                <path d="M12 3h.01"/>
                                <path d="M12 16v.01"/>
                                <path d="M16 12h1"/>
                                <path d="M21 12v.01"/>
                                <path d="M12 21v-1"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Right Column: Institutional & Policy Quick Links (BSJ Uppercase Style) --}}
                <div class="space-y-3.5 text-left md:text-right">
                    <h3 class="text-xs font-bold tracking-widest text-emerald-400 uppercase">Informasi &amp; Legalitas</h3>
                    <nav class="flex flex-col space-y-2.5 text-xs md:text-sm font-semibold tracking-wider text-gray-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.85)]" aria-label="Footer Navigasi">
                        <a href="{{ $site::path('/contact') }}" class="hover:text-emerald-300 hover:translate-x-0.5 md:hover:-translate-x-0.5 transition-all uppercase">
                            Perlindungan Data &amp; Aksesibilitas
                        </a>
                        <a href="{{ $site::path('/school-profile') }}" class="hover:text-emerald-300 hover:translate-x-0.5 md:hover:-translate-x-0.5 transition-all uppercase">
                            {{ __('site.school_profile') }}
                        </a>
                        <a href="{{ $site::path('/academics') }}" class="hover:text-emerald-300 hover:translate-x-0.5 md:hover:-translate-x-0.5 transition-all uppercase">
                            Kurikulum &amp; Akademik
                        </a>
                        <a href="{{ $site::path('/admissions') }}" class="hover:text-emerald-300 hover:translate-x-0.5 md:hover:-translate-x-0.5 transition-all uppercase">
                            Pendaftaran Siswa Baru
                        </a>
                        <a href="{{ $site::path('/mandarin-program') }}" class="hover:text-emerald-300 hover:translate-x-0.5 md:hover:-translate-x-0.5 transition-all uppercase">
                            Program Bahasa Mandarin
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Lower Ribbon: Accreditation & Certification Monochrome Logos Strip (BSJ Style) --}}
        <div class="relative z-10 border-y border-white/10 bg-black/45 backdrop-blur-md py-6">
            <div class="shell">
                <div class="flex flex-wrap items-center justify-center sm:justify-between gap-6 md:gap-8 lg:gap-10">
                    {{-- 1. SPK Kemendikbudristek --}}
                    <div class="flex items-center gap-3 text-white/85 hover:text-white transition-opacity group cursor-default"
                        title="Satuan Pendidikan Kerja Sama (SPK) diakui resmi oleh Kemendikbudristek RI">
                        <svg class="h-8 w-8 shrink-0 text-white drop-shadow" viewBox="0 0 36 36" fill="currentColor">
                            {{-- Shield + Education Torch Icon --}}
                            <path d="M18 2L5 7v10c0 8 5.5 15.5 13 17 7.5-1.5 13-9 13-17V7L18 2zm0 3.2l9 3.5v8.3c0 6.2-4.2 12-9 13.5-4.8-1.5-9-7.3-9-13.5V8.7l9-3.5z"/>
                            <path d="M18 10c-1.1 0-2 .9-2 2 0 1.5 1.2 2.8 2 4.5.8-1.7 2-3 2-4.5 0-1.1-.9-2-2-2z"/>
                            <path d="M14 18h8v2h-8zm1.5 3h5v2h-5z"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <span class="block text-[11px] font-bold tracking-wider uppercase text-white">SPK TERAKREDITASI</span>
                            <span class="block text-[9px] font-medium tracking-wide text-gray-300 uppercase">KEMENDIKBUDRISTEK RI</span>
                        </div>
                    </div>

                    {{-- 2. MOE Taiwan Curriculum --}}
                    <div class="flex items-center gap-3 text-white/85 hover:text-white transition-opacity group cursor-default"
                        title="Kurikulum Resmi Kementerian Pendidikan Taiwan (MOE 2019)">
                        <svg class="h-8 w-8 shrink-0 text-white drop-shadow" viewBox="0 0 36 36" fill="currentColor">
                            {{-- Plum Blossom / MOE Taiwan Motif --}}
                            <circle cx="18" cy="18" r="4"/>
                            <circle cx="18" cy="8" r="4.5"/>
                            <circle cx="27.5" cy="15" r="4.5"/>
                            <circle cx="24" cy="26" r="4.5"/>
                            <circle cx="12" cy="26" r="4.5"/>
                            <circle cx="8.5" cy="15" r="4.5"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <span class="block text-[11px] font-bold tracking-wider uppercase text-white">KURIKULUM TAIWAN</span>
                            <span class="block text-[9px] font-medium tracking-wide text-gray-300 uppercase">MINISTRY OF EDUCATION</span>
                        </div>
                    </div>

                    {{-- 3. Cambridge English Assessment (KET / PET) --}}
                    <div class="flex items-center gap-3 text-white/85 hover:text-white transition-opacity group cursor-default"
                        title="Cambridge English Assessment (KET/PET) CEFR European Standard">
                        <svg class="h-8 w-8 shrink-0 text-white drop-shadow" viewBox="0 0 36 36" fill="currentColor">
                            {{-- Cambridge Crest / Open Book Emblem --}}
                            <path d="M18 4C10.3 4 4 10.3 4 18s6.3 14 14 14 14-6.3 14-14S25.7 4 18 4zm0 2.5c6.4 0 11.5 5.1 11.5 11.5S24.4 29.5 18 29.5 6.5 24.4 6.5 18 11.6 6.5 18 6.5z"/>
                            <path d="M11 12h5.5c1.4 0 2.5.6 2.5 1.8 0 .8-.5 1.4-1.2 1.6.9.2 1.5.9 1.5 1.8 0 1.3-1.2 1.8-2.6 1.8H11V12zm2 1.6v1.8h3.2c.6 0 1-.2 1-.8s-.4-.8-1-.8H13zm0 3.2v2h3.4c.7 0 1.1-.3 1.1-.9 0-.7-.4-1.1-1.1-1.1H13zM21 12h4v7h-2v-5.4h-2V12z"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <span class="block text-[11px] font-bold tracking-wider uppercase text-white">CAMBRIDGE ENGLISH</span>
                            <span class="block text-[9px] font-medium tracking-wide text-gray-300 uppercase">ASSESSMENT (KET / PET)</span>
                        </div>
                    </div>

                    {{-- 4. Cambridge IGCSE & IELTS --}}
                    <div class="flex items-center gap-3 text-white/85 hover:text-white transition-opacity group cursor-default"
                        title="Materi Standar Internasional Persiapan Cambridge IGCSE & IELTS">
                        <svg class="h-8 w-8 shrink-0 text-white drop-shadow" viewBox="0 0 36 36" fill="currentColor">
                            {{-- Laurel Wreath & Academic Pathway --}}
                            <path d="M18 7l12 6-12 6-12-6 12-6zm-8 9.5v5.5c0 4.4 3.6 8 8 8s8-3.6 8-8v-5.5l-8 4-8-4z"/>
                            <path d="M29 15v7h2v-7h-2z"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <span class="block text-[11px] font-bold tracking-wider uppercase text-white">CAMBRIDGE IGCSE &amp; IELTS</span>
                            <span class="block text-[9px] font-medium tracking-wide text-gray-300 uppercase">EXAM PREPARATION PATHWAY</span>
                        </div>
                    </div>

                    {{-- 5. TOCFL (華語文能力測驗) --}}
                    <div class="flex items-center gap-3 text-white/85 hover:text-white transition-opacity group cursor-default"
                        title="TOCFL (Test of Chinese as a Foreign Language) Pusat Sertifikasi Kemahiran Bahasa Mandarin">
                        <svg class="h-8 w-8 shrink-0 text-white drop-shadow" viewBox="0 0 36 36" fill="currentColor">
                            {{-- Huayu Seal Insignia --}}
                            <rect x="5" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="2.2"/>
                            <path d="M12 11h12v2H12zm2 5h8v2h-8zm-2 5h12v2H12z"/>
                            <path d="M18 10v16" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <span class="block text-[11px] font-bold tracking-wider uppercase text-white">TOCFL RESMI TAIWAN</span>
                            <span class="block text-[9px] font-medium tracking-wide text-gray-300 uppercase">CHINESE PROFICIENCY TEST</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="relative z-10 shell flex flex-col md:flex-row items-center justify-between gap-4 py-5 text-xs text-gray-400">
            <p class="text-center md:text-left">&copy; {{ date('Y') }} Jakarta Taipei School. Hak Cipta Dilindungi Undang-Undang. Satuan Pendidikan Kerja Sama (SPK).</p>
            <div class="flex items-center justify-center gap-4 text-gray-400 shrink-0 whitespace-nowrap">
                <a href="{{ $site::path('/contact') }}" class="hover:text-emerald-300 transition-colors">Privasi</a>
                <span class="text-white/20">•</span>
                <a href="{{ $site::path('/contact') }}" class="hover:text-emerald-300 transition-colors">Aksesibilitas</a>
            </div>
        </div>

        {{-- QR Code Barcode Modal Popup --}}
        <dialog id="jts-qr-modal"
            x-data="{ whatsappContact: 'admissions' }"
            aria-labelledby="jts-qr-title"
            class="backdrop:bg-black/75 backdrop:backdrop-blur-sm rounded-2xl p-0 shadow-2xl border border-white/20 bg-[#0d2a1d] text-white max-w-sm w-full mx-auto my-auto overflow-hidden">
            <div class="relative p-6 text-center">
                <button type="button"
                    onclick="document.getElementById('jts-qr-modal')?.close()"
                    class="absolute right-4 top-4 text-white/70 hover:text-white rounded-full p-1.5 hover:bg-white/10 transition-colors cursor-pointer"
                    aria-label="{{ __('site.close') }}">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="inline-flex p-3 bg-white/10 rounded-2xl mb-3 ring-1 ring-white/20">
                    <svg class="size-7 text-emerald-300 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8">
                        <rect width="5" height="5" x="3" y="3" rx="1"/>
                        <rect width="5" height="5" x="16" y="3" rx="1"/>
                        <rect width="5" height="5" x="3" y="16" rx="1"/>
                        <path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M12 21v-1"/>
                    </svg>
                </div>

                <h4 id="jts-qr-title" class="text-base font-bold text-white mb-1">{{ __('site.footer_qr_title') }}</h4>
                <p class="text-xs text-emerald-200/80 mb-4">{{ __('site.footer_qr_description') }}</p>

                <label for="jts-whatsapp-contact" class="mb-2 block text-left text-xs font-semibold text-emerald-200">{{ __('site.whatsapp_select_contact') }}</label>
                <select id="jts-whatsapp-contact" x-model="whatsappContact" class="mb-5 w-full rounded-lg border border-white/30 bg-[#0d2a1d] px-3 py-2.5 text-sm text-white focus:border-emerald-300 focus:outline-none">
                    @foreach($contact['whatsapp'] as $channel => $whatsapp)
                        <option value="{{ $channel }}">{{ __('site.footer_whatsapp_'.$channel) }}</option>
                    @endforeach
                </select>

                @foreach($contact['whatsapp'] as $channel => $whatsapp)
                    <div x-show="whatsappContact === '{{ $channel }}'" data-whatsapp-channel="{{ $channel }}" @if($channel !== 'admissions') x-cloak @endif>
                        <div class="bg-white p-3.5 rounded-xl inline-block shadow-lg mb-4">
                            <img src="{{ asset($whatsapp['qr_image']) }}"
                                alt="{{ __('site.footer_whatsapp_qr_alt', ['contact' => __('site.footer_whatsapp_'.$channel)]) }}"
                                width="192" height="192"
                                class="size-48 object-contain mx-auto" />
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-emerald-300">{{ __('site.footer_whatsapp_'.$channel) }}</p>
                            <a href="{{ $whatsapp['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-block text-sm font-semibold text-white underline underline-offset-4 hover:text-emerald-300">{{ $whatsapp['display'] }}</a>
                            <p class="text-[11px] text-gray-300">{{ $contact['address']['street'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </dialog>
    </footer>
    @if($announcementPopup)<x-announcement-popup :popup="$announcementPopup" />@endif
    @livewireScripts
</body>

</html>
