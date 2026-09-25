<x-layouts.site :title="__('pages.academics.seo_title') ?: __('pages.academics.title')"
    :description="__('pages.academics.summary')">
    @php
        $site = \App\Support\Site::class;
        $academics = __('pages.academics');
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/academics-hero.jpg') }}" alt="{{ $academics['title'] }}"
                class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}"
                class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3" />
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $academics['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $academics['summary'] }}</p>
        </div>
    </div>

    {{-- Academics Content --}}
    <div class="shell py-12 sm:py-16">
        <div class="prose" lang="{{ app()->getLocale() }}">
            <div class="not-prose">

                {{-- Header Section: Tiga Pilar Kurikulum --}}
                <div class="text-center max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#176443]">
                            {{ $academics['eyebrow'] ?? 'TIGA PILAR KURIKULUM' }}
                        </span>
                        <span class="w-8 h-px bg-[#176443]/40"></span>
                    </div>

                    {{-- 1 baris headline, font sans sesuai school-profile (tanpa font-serif), warna solid (tanpa hijau)
                    --}}
                    <h2
                        class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-ink sm:whitespace-nowrap">
                        {{ $academics['main_heading'] ?? 'Satu Pendidikan, Tiga Keunggulan' }}
                    </h2>

                    <p class="mt-3 text-sm sm:text-base text-muted leading-relaxed max-w-2xl mx-auto">
                        {{ $academics['main_subheading'] ?? 'Kurikulum JTS menggabungkan kekuatan pendidikan Taiwan, Indonesia, dan Cambridge English.' }}
                    </p>
                </div>

                {{-- 3 Curriculum Cards Grid --}}
                <div class="mt-10 sm:mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

                    {{-- Card 1: Taiwan Curriculum --}}
                    <div
                        class="rounded-2xl sm:rounded-3xl border border-line bg-white p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col hover:border-[#176443]/30">
                        {{-- Circular Flag Container --}}
                        <div
                            class="size-14 sm:size-16 rounded-full bg-[#fdf2f2] flex items-center justify-center p-2.5 mb-5 shadow-2xs">
                            <svg class="size-10 sm:size-11 rounded-full shadow-xs" viewBox="0 0 64 64" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <clipPath id="tw-flag-circle">
                                    <circle cx="32" cy="32" r="32" />
                                </clipPath>
                                <g clip-path="url(#tw-flag-circle)">
                                    <rect width="64" height="64" fill="#FE0000" />
                                    <rect width="32" height="32" fill="#000095" />
                                    <g fill="#FFFFFF" transform="translate(16, 16)">
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(30)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(60)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(90)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(120)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(150)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(180)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(210)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(240)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(270)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(300)" />
                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(330)" />
                                        <circle cx="0" cy="0" r="5.6" fill="#000095" />
                                        <circle cx="0" cy="0" r="4.4" fill="#FFFFFF" />
                                    </g>
                                </g>
                            </svg>
                        </div>

                        <h3 class="text-lg sm:text-xl font-bold text-ink tracking-tight">
                            {{ $academics['taiwan_title'] ?? 'Taiwan Curriculum' }}
                        </h3>

                        <p class="mt-2.5 text-xs sm:text-sm text-muted leading-relaxed">
                            {{ $academics['taiwan_desc'] ?? $academics['mandarin_body'] }}
                        </p>

                        {{-- Badges in 1 single line with natural compact spacing --}}
                        <div class="mt-5 flex items-center gap-1.5 sm:gap-2 flex-nowrap">
                            @foreach($academics['taiwan_tags'] ?? ['Traditional Mandarin', 'Academic Foundation'] as $tag)
                                <span
                                    class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-medium whitespace-nowrap shrink-0 bg-[#fef2f2] text-[#e02424] border border-[#fecaca]/70">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Card 2: Indonesian Curriculum --}}
                    <div
                        class="rounded-2xl sm:rounded-3xl border border-line bg-white p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col hover:border-[#176443]/30">
                        {{-- Circular Flag Container --}}
                        <div
                            class="size-14 sm:size-16 rounded-full bg-[#edfdf5] flex items-center justify-center p-2.5 mb-5 shadow-2xs">
                            <svg class="size-10 sm:size-11 rounded-full shadow-xs border border-line/60"
                                viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <clipPath id="id-flag-circle">
                                    <circle cx="32" cy="32" r="32" />
                                </clipPath>
                                <g clip-path="url(#id-flag-circle)">
                                    <rect x="0" y="0" width="64" height="32" fill="#E02424" />
                                    <rect x="0" y="32" width="64" height="32" fill="#FFFFFF" />
                                </g>
                            </svg>
                        </div>

                        <h3 class="text-lg sm:text-xl font-bold text-ink tracking-tight">
                            {{ $academics['indonesia_title'] ?? 'Indonesian Curriculum' }}
                        </h3>

                        <p class="mt-2.5 text-xs sm:text-sm text-muted leading-relaxed">
                            {{ $academics['indonesia_desc'] ?? $academics['indonesian_body'] }}
                        </p>

                        {{-- Badges in 1 single line with natural compact spacing --}}
                        <div class="mt-5 flex items-center gap-1.5 sm:gap-2 flex-nowrap">
                            @foreach($academics['indonesia_tags'] ?? ['Kurikulum Merdeka', 'National Identity'] as $tag)
                                <span
                                    class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-medium whitespace-nowrap shrink-0 bg-[#ecfdf5] text-[#10b981] border border-[#a7f3d0]/70">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Card 3: English Curriculum --}}
                    <div
                        class="rounded-2xl sm:rounded-3xl border border-line bg-white p-6 sm:p-7 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col hover:border-[#176443]/30">
                        {{-- Circular Flag Container --}}
                        <div
                            class="size-14 sm:size-16 rounded-full bg-[#eff6ff] flex items-center justify-center p-2.5 mb-5 shadow-2xs">
                            <svg class="size-10 sm:size-11 rounded-full shadow-xs" viewBox="0 0 64 64" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <clipPath id="uk-flag-circle">
                                    <circle cx="32" cy="32" r="32" />
                                </clipPath>
                                <g clip-path="url(#uk-flag-circle)">
                                    <rect width="64" height="64" fill="#012169" />
                                    <path d="M0 0 L64 64 M64 0 L0 64" stroke="#FFFFFF" stroke-width="10" />
                                    <path d="M0 0 L64 64 M64 0 L0 64" stroke="#C8102E" stroke-width="5" />
                                    <rect x="25" y="0" width="14" height="64" fill="#FFFFFF" />
                                    <rect x="0" y="25" width="64" height="14" fill="#FFFFFF" />
                                    <rect x="28" y="0" width="8" height="64" fill="#C8102E" />
                                    <rect x="0" y="28" width="64" height="8" fill="#C8102E" />
                                </g>
                            </svg>
                        </div>

                        <h3 class="text-lg sm:text-xl font-bold text-ink tracking-tight">
                            {{ $academics['english_title'] ?? 'English Curriculum' }}
                        </h3>

                        <p class="mt-2.5 text-xs sm:text-sm text-muted leading-relaxed">
                            {{ $academics['english_desc'] ?? $academics['english_body'] }}
                        </p>

                        {{-- Badges in 1 single line with natural compact spacing --}}
                        <div class="mt-5 flex items-center gap-1.5 sm:gap-2 flex-nowrap">
                            @foreach($academics['english_tags'] ?? ['Cambridge', 'Global Opportunities'] as $tag)
                                <span
                                    class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-medium whitespace-nowrap shrink-0 bg-[#eff6ff] text-[#3b82f6] border border-[#bfdbfe]/70">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Section: Learning Journey (Curriculum by Level) --}}
                <div x-data="{ activeJourneyTab: 'tk' }" class="mt-16 sm:mt-24 pt-12 sm:pt-16 border-t border-line/60">
                    {{-- Section Header --}}
                    <div class="text-center max-w-4xl mx-auto">
                        <div class="inline-flex items-center gap-2.5">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#176443]">
                                {{ $academics['journey_eyebrow'] ?? 'Learning Journey' }}
                            </span>
                            <span class="h-0.5 w-8 bg-[#176443]/40 rounded-full"></span>
                        </div>
                        <h2 class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-bold text-ink tracking-tight">
                            {{ $academics['journey_heading'] ?? 'Kurikulum Berdasarkan Jenjang Pendidikan' }}
                        </h2>
                        <p class="mt-3 text-sm sm:text-base text-muted max-w-3xl mx-auto leading-relaxed">
                            {{ $academics['journey_subheading'] ?? 'Program pembelajaran yang dirancang sesuai dengan tahap perkembangan siswa, dari Taman Kanak-kanak hingga Sekolah Menengah.' }}
                        </p>
                    </div>

                    {{-- 4 Educational Level Selection Tabs (Reusing Admissions Tab Pattern) --}}
                    <div class="mt-8 sm:mt-10 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($academics['journey_tabs'] ?? [] as $code => $tab)
                            <button type="button" @click="activeJourneyTab = '{{ $code }}'"
                                class="flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-2xl text-left transition-all duration-300 border focus:outline-none focus:ring-2 focus:ring-[#176443] focus:ring-offset-2 cursor-pointer"
                                :class="activeJourneyTab === '{{ $code }}'
                                        ? 'bg-[#0a4a30] border-[#0a4a30] text-white shadow-md -translate-y-0.5'
                                        : 'bg-white border-line text-ink hover:border-[#176443]/40 hover:bg-[#fbfcfb] shadow-2xs'">
                                {{-- Tab Icon --}}
                                <div class="size-11 sm:size-12 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                    :class="activeJourneyTab === '{{ $code }}' ? 'bg-white/15 text-white' : 'bg-[#eaf4ed] text-[#176443]'">
                                    @if($tab['icon'] === 'sprout')
                                        {{-- Sprout Icon (Matching Image 1) --}}
                                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 20h10" />
                                            <path d="M12 20v-8" />
                                            <path d="M12 12a5 5 0 0 1 5-5c0 3-2 5-5 5Z" />
                                            <path d="M12 12a5 5 0 0 0-5-5c0 3 2 5 5 5Z" />
                                        </svg>
                                    @elseif($tab['icon'] === 'book-open')
                                        {{-- Book Open Icon (Matching Image 2) --}}
                                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                                        </svg>
                                    @elseif($tab['icon'] === 'book')
                                        {{-- Notebook/Tablet Icon (Matching Image 3) --}}
                                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="16" height="20" x="4" y="2" rx="2.5" />
                                            <line x1="8" x2="16" y1="6" y2="6" />
                                            <line x1="8" x2="16" y1="10" y2="10" />
                                            <line x1="8" x2="12" y1="14" y2="14" />
                                        </svg>
                                    @elseif($tab['icon'] === 'graduation-cap')
                                        {{-- Graduation Cap Icon (Matching Image 4) --}}
                                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z" />
                                            <path d="M22 10v6" />
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-bold leading-snug">{{ $tab['name'] }}</div>
                                    <div class="text-[11px] sm:text-xs transition-colors mt-0.5"
                                        :class="activeJourneyTab === '{{ $code }}' ? 'text-white/80' : 'text-muted'">
                                        {{ $tab['sub'] }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Tab Panels Content --}}
                    <div class="mt-6 sm:mt-8">
                        @foreach($academics['journey_tabs'] ?? [] as $code => $tab)
                            <div x-show="activeJourneyTab === '{{ $code }}'"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="rounded-2xl sm:rounded-3xl border border-[#e2e8f0]/90 bg-[#f8fbfa]/70 p-6 sm:p-8 lg:p-10 shadow-xs">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
                                    {{-- Left Column: Level Intro / Philosophy (~32% width) --}}
                                    <div class="lg:col-span-4 flex flex-col justify-center pr-0 lg:pr-4">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="text-xs font-bold uppercase tracking-wider text-[#176443]">
                                                {{ $tab['eyebrow'] }}
                                            </span>
                                        </div>
                                        <h3
                                            class="mt-2.5 text-2xl sm:text-3xl font-bold text-ink tracking-tight leading-tight">
                                            {{ $tab['title'] }}
                                        </h3>
                                        <p class="mt-3.5 text-xs sm:text-sm text-muted leading-relaxed">
                                            {{ $tab['desc'] }}
                                        </p>
                                    </div>

                                    {{-- Right Column: 4 Feature Cards Grid (~68% width) --}}
                                    <div
                                        class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 items-stretch">
                                        @foreach($tab['features'] as $feature)
                                            <div
                                                class="rounded-2xl border border-line/70 bg-white p-5 sm:p-5 shadow-2xs hover:shadow-sm transition-all duration-300 hover:border-line">
                                                {{-- Circular Pastel Icon --}}
                                                    <div class="size-12 rounded-full flex items-center justify-center mb-4
                                                                @if($feature['color'] === 'yellow') bg-[#fef3c7] text-[#d97706]
                                                                @elseif($feature['color'] === 'green') bg-[#dcfce7] text-[#059669]
                                                                @elseif($feature['color'] === 'blue') bg-[#dbeafe] text-[#2563eb]
                                                                @elseif($feature['color'] === 'red') bg-[#fee2e2] text-[#dc2626]
                                                                @endif">
                                                        @if($feature['icon'] === 'smile')
                                                            {{-- Smile Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <path d="M8 14s1.5 2 4 2 4-2 4-2" />
                                                                <line x1="9" x2="9.01" y1="9" y2="9" />
                                                                <line x1="15" x2="15.01" y1="9" y2="9" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'shield-check')
                                                            {{-- Shield Check Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path
                                                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                                                <path d="m9 12 2 2 4-4" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'users')
                                                            {{-- Users Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                                <circle cx="9" cy="7" r="4" />
                                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'home')
                                                            {{-- Home Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'book-open')
                                                            {{-- Book Open Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'layers')
                                                            {{-- Layers / Stack Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path
                                                                    d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z" />
                                                                <path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65" />
                                                                <path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'globe')
                                                            {{-- Globe Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <line x1="2" x2="22" y1="12" y2="12" />
                                                                <path
                                                                    d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'chart')
                                                            {{-- Bar Chart Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="currentColor">
                                                                <rect width="4" height="6" x="4" y="14" rx="1" />
                                                                <rect width="4" height="11" x="10" y="9" rx="1" />
                                                                <rect width="4" height="16" x="16" y="4" rx="1" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'translate')
                                                            {{-- Translate / Languages Icon --}}
                                                            <svg class="size-6" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="m5 8 6 6" />
                                                                <path d="m4 14 6-6 2-3" />
                                                                <path d="M2 5h12" />
                                                                <path d="M7 2h1" />
                                                                <path d="m22 22-5-10-5 10" />
                                                                <path d="M14 18h6" />
                                                            </svg>
                                                        @elseif($feature['icon'] === 'flag-tw')
                                                            {{-- Taiwan Circular Flag Badge --}}
                                                            <svg class="size-8 rounded-full shadow-xs shrink-0" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <clipPath id="flag-tw-{{ $code }}-{{ $loop->index }}">
                                                                    <circle cx="32" cy="32" r="32" />
                                                                </clipPath>
                                                                <g clip-path="url(#flag-tw-{{ $code }}-{{ $loop->index }})">
                                                                    <rect width="64" height="64" fill="#FE0000" />
                                                                    <rect width="32" height="32" fill="#000095" />
                                                                    <g fill="#FFFFFF" transform="translate(16, 16)">
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(30)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(60)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(90)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(120)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(150)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(180)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(210)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(240)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(270)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(300)" />
                                                                        <path d="M0 -11 L2.2 -5 L-2.2 -5 Z" transform="rotate(330)" />
                                                                        <circle cx="0" cy="0" r="5.6" fill="#000095" />
                                                                        <circle cx="0" cy="0" r="4.4" fill="#FFFFFF" />
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        @elseif($feature['icon'] === 'flag-id')
                                                            {{-- Indonesian Circular Flag Badge --}}
                                                            <svg class="size-8 rounded-full shadow-xs border border-line/60 shrink-0" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <clipPath id="flag-id-{{ $code }}-{{ $loop->index }}">
                                                                    <circle cx="32" cy="32" r="32" />
                                                                </clipPath>
                                                                <g clip-path="url(#flag-id-{{ $code }}-{{ $loop->index }})">
                                                                    <rect x="0" y="0" width="64" height="32" fill="#E02424" />
                                                                    <rect x="0" y="32" width="64" height="32" fill="#FFFFFF" />
                                                                </g>
                                                            </svg>
                                                        @elseif($feature['icon'] === 'flag-uk')
                                                            {{-- UK Circular Flag Badge --}}
                                                            <svg class="size-8 rounded-full shadow-xs shrink-0" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <clipPath id="flag-uk-{{ $code }}-{{ $loop->index }}">
                                                                    <circle cx="32" cy="32" r="32" />
                                                                </clipPath>
                                                                <g clip-path="url(#flag-uk-{{ $code }}-{{ $loop->index }})">
                                                                    <rect width="64" height="64" fill="#012169" />
                                                                    <path d="M0 0 L64 64 M64 0 L0 64" stroke="#FFFFFF" stroke-width="10" />
                                                                    <path d="M0 0 L64 64 M64 0 L0 64" stroke="#C8102E" stroke-width="5" />
                                                                    <rect x="25" y="0" width="14" height="64" fill="#FFFFFF" />
                                                                    <rect x="0" y="25" width="64" height="14" fill="#FFFFFF" />
                                                                    <rect x="28" y="0" width="8" height="64" fill="#C8102E" />
                                                                    <rect x="0" y="28" width="64" height="8" fill="#C8102E" />
                                                                </g>
                                                            </svg>
                                                        @elseif($feature['icon'] === 'flag-cn')
                                                            {{-- China Circular Flag Badge --}}
                                                            <svg class="size-8 rounded-full shadow-xs shrink-0" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <clipPath id="flag-cn-{{ $code }}-{{ $loop->index }}">
                                                                    <circle cx="32" cy="32" r="32" />
                                                                </clipPath>
                                                                <g clip-path="url(#flag-cn-{{ $code }}-{{ $loop->index }})">
                                                                    <rect width="64" height="64" fill="#DE2910" />
                                                                    <g fill="#FFDE00">
                                                                        {{-- Large Star --}}
                                                                        <polygon points="0,-1 0.225,-0.309 0.951,-0.309 0.363,0.118 0.588,0.809 0,0.382 -0.588,0.809 -0.363,0.118 -0.951,-0.309 -0.225,-0.309" transform="translate(19, 21) scale(7.5)" />
                                                                        {{-- 4 Small Stars pointing towards the big star --}}
                                                                        <polygon points="0,-1 0.225,-0.309 0.951,-0.309 0.363,0.118 0.588,0.809 0,0.382 -0.588,0.809 -0.363,0.118 -0.951,-0.309 -0.225,-0.309" transform="translate(32, 12) rotate(55) scale(2.5)" />
                                                                        <polygon points="0,-1 0.225,-0.309 0.951,-0.309 0.363,0.118 0.588,0.809 0,0.382 -0.588,0.809 -0.363,0.118 -0.951,-0.309 -0.225,-0.309" transform="translate(38, 18) rotate(80) scale(2.5)" />
                                                                        <polygon points="0,-1 0.225,-0.309 0.951,-0.309 0.363,0.118 0.588,0.809 0,0.382 -0.588,0.809 -0.363,0.118 -0.951,-0.309 -0.225,-0.309" transform="translate(38, 27) rotate(105) scale(2.5)" />
                                                                        <polygon points="0,-1 0.225,-0.309 0.951,-0.309 0.363,0.118 0.588,0.809 0,0.382 -0.588,0.809 -0.363,0.118 -0.951,-0.309 -0.225,-0.309" transform="translate(32, 33) rotate(132) scale(2.5)" />
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    {{-- Feature Title --}}
                                                    <h4
                                                        class="text-sm font-bold text-ink tracking-tight leading-snug min-h-[36px] flex items-center">
                                                        {{ $feature['title'] }}
                                                    </h4>

                                                    {{-- Feature Description --}}
                                                    <p class="mt-2 text-xs text-muted leading-relaxed whitespace-pre-line">
                                                        {{ $feature['desc'] }}
                                                    </p>
                                                </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Section: English Learning Pathway --}}
                <div class="mt-16 sm:mt-24 pt-12 sm:pt-16 border-t border-line/60">
                    {{-- Section Header --}}
                    <div class="max-w-3xl">
                        <div class="inline-flex items-center gap-2.5">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#176443]">
                                {{ $academics['pathway_eyebrow'] ?? 'English Curriculum' }}
                            </span>
                            <span class="h-0.5 w-8 bg-[#176443]/40 rounded-full"></span>
                        </div>
                        <h2 class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-bold text-ink tracking-tight">
                            {{ $academics['pathway_heading'] ?? 'English Learning Pathway' }}
                        </h2>
                        <p class="mt-3 text-sm sm:text-base text-muted leading-relaxed">
                            {{ $academics['pathway_subheading'] ?? 'Program English yang terstruktur sesuai jenjang, berbasis Oxford/Cambridge, untuk mempersiapkan siswa menghadapi standar global.' }}
                        </p>
                    </div>

                    {{-- Pathway Cards Grid (Primary, Secondary, High School) --}}
                    <div class="mt-8 sm:mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">

                        {{-- Card 1: Primary School (Blue Theme) --}}
                        <div
                            class="rounded-2xl sm:rounded-3xl border border-[#dbeafe] bg-[#f8fbff] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:shadow-md hover:border-[#93c5fd] transition-all duration-300">
                            <div>
                                {{-- Card Header: Title, Grades, and Arrow --}}
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-bold text-[#1d4ed8] tracking-tight">
                                            {{ $academics['pathway_primary_title'] ?? 'Primary School' }}
                                        </h3>
                                        <p class="text-sm font-semibold text-[#2563eb] mt-0.5">
                                            {{ $academics['pathway_primary_grade'] ?? '(G1 – G6)' }}
                                        </p>
                                    </div>
                                    <div
                                        class="size-9 sm:size-10 rounded-full bg-white border border-[#bfdbfe] flex items-center justify-center text-[#2563eb] shadow-2xs shrink-0">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Curriculum Program Info --}}
                                <div class="mt-5 min-h-[46px] flex flex-col justify-center">
                                    <p class="font-bold text-ink text-sm sm:text-base leading-snug">
                                        {{ $academics['pathway_primary_prog'] ?? 'Oxford / Cambridge English' }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-muted mt-0.5">
                                        {{ $academics['pathway_primary_subprog'] ?? 'Second → First Language' }}
                                    </p>
                                </div>

                                {{-- 3 Skill Pillars with Icons --}}
                                <div
                                    class="mt-6 mb-6 pt-5 border-t border-[#dbeafe]/80 grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#2563eb] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_primary_skills']['grammar'] ?? 'Grammar & Vocabulary' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#2563eb] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_primary_skills']['four_skills'] ?? '4 Language Skills' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#2563eb] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_primary_skills']['project'] ?? 'Project Learning' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Assessment Box --}}
                            <div
                                class="rounded-xl sm:rounded-2xl bg-[#ebf4fe] border border-[#d0e4fc] p-3.5 sm:p-4 grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_primary_box']['assessment_label'] ?? 'Assessment' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_primary_box']['assessment_value'] ?? 'KET' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_primary_box']['target_label'] ?? 'Target' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_primary_box']['target_value'] ?? 'CEFR A2 – B1' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Card 2: Secondary School (Green Theme) --}}
                        <div
                            class="rounded-2xl sm:rounded-3xl border border-[#a7f3d0] bg-[#f4fbf7] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:shadow-md hover:border-[#6ee7b7] transition-all duration-300">
                            <div>
                                {{-- Card Header: Title, Grades, and Arrow --}}
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-bold text-[#047857] tracking-tight">
                                            {{ $academics['pathway_secondary_title'] ?? 'Secondary School' }}
                                        </h3>
                                        <p class="text-sm font-semibold text-[#059669] mt-0.5">
                                            {{ $academics['pathway_secondary_grade'] ?? '(G7 – G9)' }}
                                        </p>
                                    </div>
                                    <div
                                        class="size-9 sm:size-10 rounded-full bg-white border border-[#a7f3d0] flex items-center justify-center text-[#059669] shadow-2xs shrink-0">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Curriculum Program Info --}}
                                <div class="mt-5 min-h-[46px] flex flex-col justify-center">
                                    <p class="font-bold text-ink text-sm sm:text-base leading-snug">
                                        {{ $academics['pathway_secondary_prog'] ?? 'Cambridge IGCSE English' }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-muted mt-0.5">
                                        {{ $academics['pathway_secondary_subprog'] ?? 'English as a Second Language' }}
                                    </p>
                                </div>

                                {{-- 3 Skill Pillars with Icons --}}
                                <div
                                    class="mt-6 mb-6 pt-5 border-t border-[#a7f3d0]/80 grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#059669] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_secondary_skills']['comm'] ?? 'Communication Skills' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#059669] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_secondary_skills']['writing'] ?? 'Writing Skills' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#059669] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_secondary_skills']['speaking'] ?? 'Public Speaking' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Assessment Box --}}
                            <div
                                class="rounded-xl sm:rounded-2xl bg-[#e3f7ec] border border-[#bee9d2] p-3.5 sm:p-4 grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_secondary_box']['assessment_label'] ?? 'Assessment' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_secondary_box']['assessment_value'] ?? 'PET' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_secondary_box']['target_label'] ?? 'Target' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_secondary_box']['target_value'] ?? 'CEFR B1 – B2' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Card 3: High School (Amber / Gold Theme) --}}
                        <div
                            class="rounded-2xl sm:rounded-3xl border border-[#fde68a] bg-[#fefcf3] p-6 sm:p-7 flex flex-col justify-between shadow-2xs hover:shadow-md hover:border-[#fcd34d] transition-all duration-300">
                            <div>
                                {{-- Card Header: Title, Grades, and Arrow --}}
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-bold text-[#b45309] tracking-tight">
                                            {{ $academics['pathway_high_title'] ?? 'High School' }}
                                        </h3>
                                        <p class="text-sm font-semibold text-[#d97706] mt-0.5">
                                            {{ $academics['pathway_high_grade'] ?? '(G10 – G12)' }}
                                        </p>
                                    </div>
                                    <div
                                        class="size-9 sm:size-10 rounded-full bg-white border border-[#fde68a] flex items-center justify-center text-[#d97706] shadow-2xs shrink-0">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Curriculum Program Info --}}
                                <div class="mt-5 min-h-[46px] flex flex-col justify-center">
                                    <p class="font-bold text-ink text-sm sm:text-base leading-snug">
                                        {{ $academics['pathway_high_prog'] ?? 'Academic English & University Preparation' }}
                                    </p>
                                </div>

                                {{-- 3 Skill Pillars with Icons --}}
                                <div
                                    class="mt-6 mb-6 pt-5 border-t border-[#fde68a]/80 grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#d97706] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                                <path
                                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 14v6m-4-3v3m8-3v3" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_high_skills']['ielts'] ?? 'IELTS & FCE Preparation' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#d97706] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_high_skills']['exam'] ?? 'University Entrance Exam' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="h-8 flex items-center justify-center text-[#d97706] mb-2">
                                            <svg class="size-6 sm:size-7" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[11px] sm:text-xs font-medium text-ink leading-tight min-h-[32px] flex items-center justify-center">
                                            {{ $academics['pathway_high_skills']['skills'] ?? 'Academic English Skills' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Assessment Box --}}
                            <div
                                class="rounded-xl sm:rounded-2xl bg-[#fef5d8] border border-[#fde492] p-3.5 sm:p-4 grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_high_box']['target_label'] ?? 'Target' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_high_box']['target_value'] ?? 'IELTS 6.5 – 8.5' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[11px] sm:text-xs text-muted font-medium block">
                                        {{ $academics['pathway_high_box']['cefr_label'] ?? 'CEFR' }}
                                    </span>
                                    <span class="text-base sm:text-lg font-bold text-ink tracking-tight block mt-0.5">
                                        {{ $academics['pathway_high_box']['cefr_value'] ?? 'B2' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- CTA Box (Reusable Component) --}}
        <div class="mt-14 sm:mt-16">
            <x-cta-banner :primary-url="$site::path('/admissions')"
                :primary-label="__('pages.school_profile.cta_admissions')" :secondary-url="$site::path('/contact')"
                :secondary-label="__('pages.school_profile.cta_contact')" />
        </div>
    </div>
</x-layouts.site>