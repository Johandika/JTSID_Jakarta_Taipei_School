<x-layouts.site :title="__('pages.school_profile.seo_title') ?: __('pages.school_profile.title')" :description="__('pages.school_profile.summary')">
    @php
        $site = \App\Support\Site::class;
        $profile = __('pages.school_profile');
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/school-profile-hero.jpg') }}" alt="Jakarta Taipei School" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $profile['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $profile['summary'] }}</p>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="shell py-12 sm:py-16">
        <div class="w-full space-y-12">

            {{-- 1. Tentang Sekolah & Legalitas SPK --}}
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ $profile['overview_title'] }}</h2>

                <div class="mt-6 overflow-hidden rounded-2xl border border-line shadow-xs">
                    <img src="{{ asset('images/campus.jpg') }}" alt="{{ $profile['overview_title'] }}" class="block w-full h-64 sm:h-80 md:h-[450px] lg:h-[520px] object-cover object-center" loading="lazy" />
                </div>

                <div class="mt-6">
                    <div class="prose" lang="{{ app()->getLocale() }}">
                        <p class="text-base sm:text-lg leading-relaxed text-foreground/90">
                            {{ $profile['overview_p1'] }}
                        </p>
                        <p class="text-base leading-relaxed text-muted">
                            {{ $profile['overview_p2'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Fact Badges --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="rounded-xl border border-line bg-card p-4 text-center shadow-xs">
                    <span class="block text-2xl font-bold text-jts">1991</span>
                    <span class="text-xs text-muted">{{ $profile['stats']['year']['label'] }}</span>
                </div>
                <div class="rounded-xl border border-line bg-card p-4 text-center shadow-xs">
                    <span class="block text-2xl font-bold text-jts">SPK</span>
                    <span class="text-xs text-muted">Kemendikbudristek</span>
                </div>
                <div class="rounded-xl border border-line bg-card p-4 text-center shadow-xs">
                    <span class="block text-2xl font-bold text-jts">2019</span>
                    <span class="text-xs text-muted">Silabus Taiwan MoE</span>
                </div>
                <div class="rounded-xl border border-line bg-card p-4 text-center shadow-xs">
                    <span class="block text-2xl font-bold text-jts">3</span>
                    <span class="text-xs text-muted">Bahasa Pengantar</span>
                </div>
            </div>

            <hr class="border-line" />

            {{-- 2. Pilar Pendidikan Tiga Bahasa (Trilingual) --}}
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                    {{ $profile['trilingual_heading'] }}
                </h2>
                <p class="mt-2 text-sm sm:text-base text-muted">
                    {{ $profile['trilingual_subheading'] }}
                </p>


                {{-- 3 Bahasa Cards (Matching Reference Visual Design) --}}
                <div class="mt-8 grid gap-6 lg:grid-cols-3">
                    {{-- Mandarin --}}
                    @php
                        $mandarinParts = explode(' (', $profile['pillars']['mandarin']['title']);
                    @endphp
                    <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between min-h-[310px]">
                        {{-- Right Landmark Image with Soft Gradient Fade --}}
                        <div class="absolute right-0 top-0 bottom-0 w-[42%] sm:w-[40%] pointer-events-none select-none overflow-hidden">
                            <img src="{{ asset('images/trilingual/mandarin.jpg') }}" alt="Mandarin Pagoda" class="h-full w-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700" />
                            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/30 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-white/70 via-transparent to-transparent"></div>
                        </div>

                        {{-- Card Content --}}
                        <div class="relative z-10 max-w-[68%]">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold tracking-tight text-red-200">01</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 text-red-600 px-2.5 py-0.5 text-xs font-bold border border-red-100 shadow-2xs">
                                    <svg class="h-3 w-4 rounded-2xs shrink-0 shadow-2xs" viewBox="0 0 900 600">
                                        <rect fill="#de2910" width="900" height="600"/>
                                        <polygon fill="#ffde00" points="150,50 165,97 215,97 174,126 190,173 150,144 110,173 126,126 85,97 135,97"/>
                                        <polygon fill="#ffde00" points="300,40 307,59 328,59 311,71 317,90 300,79 283,90 289,71 272,59 293,59"/>
                                        <polygon fill="#ffde00" points="360,100 367,119 388,119 371,131 377,150 360,139 343,150 349,131 332,119 353,119"/>
                                        <polygon fill="#ffde00" points="360,180 367,199 388,199 371,211 377,230 360,219 343,230 349,211 332,199 353,199"/>
                                        <polygon fill="#ffde00" points="300,240 307,259 328,259 311,271 317,290 300,279 283,290 289,271 272,259 293,259"/>
                                    </svg>
                                    <span>Mandarin</span>
                                </span>
                            </div>

                            <h3 class="mt-4 text-xl sm:text-2xl font-bold text-gray-900 leading-snug">
                                {{ $mandarinParts[0] }}
                                @if(isset($mandarinParts[1]))
                                    <br /><span class="font-bold text-gray-900">({{ $mandarinParts[1] }}</span>
                                @endif
                            </h3>

                            <p class="mt-3 text-xs sm:text-[13px] leading-relaxed text-gray-600">
                                {{ $profile['pillars']['mandarin']['desc'] }}
                            </p>
                        </div>
                    </div>

                    {{-- English --}}
                    @php
                        $englishParts = explode(' (', $profile['pillars']['english']['title']);
                    @endphp
                    <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between min-h-[310px]">
                        {{-- Right Landmark Image with Soft Gradient Fade --}}
                        <div class="absolute right-0 top-0 bottom-0 w-[42%] sm:w-[40%] pointer-events-none select-none overflow-hidden">
                            <img src="{{ asset('images/trilingual/english.jpg') }}" alt="Big Ben London" class="h-full w-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700" />
                            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/30 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-white/70 via-transparent to-transparent"></div>
                        </div>

                        {{-- Card Content --}}
                        <div class="relative z-10 max-w-[68%]">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold tracking-tight text-blue-200">02</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 text-blue-600 px-2.5 py-0.5 text-xs font-bold border border-blue-100 shadow-2xs">
                                    <svg class="h-3 w-4 rounded-2xs shrink-0 shadow-2xs" viewBox="0 0 60 30">
                                        <clipPath id="uj-clip"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                                        <clipPath id="uj-cross"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
                                        <g clip-path="url(#uj-clip)">
                                            <path d="M0,0 v30 h60 v-30 z" fill="#012169"/>
                                            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/>
                                            <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#uj-cross)" stroke="#C8102E" stroke-width="4"/>
                                            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/>
                                            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/>
                                        </g>
                                    </svg>
                                    <span>English</span>
                                </span>
                            </div>

                            <h3 class="mt-4 text-xl sm:text-2xl font-bold text-gray-900 leading-snug">
                                {{ $englishParts[0] }}
                                @if(isset($englishParts[1]))
                                    <br /><span class="font-bold text-gray-900">({{ $englishParts[1] }}</span>
                                @endif
                            </h3>

                            <p class="mt-3 text-xs sm:text-[13px] leading-relaxed text-gray-600">
                                {{ $profile['pillars']['english']['desc'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Bahasa Indonesia --}}
                    @php
                        $indonesianParts = explode(' (', $profile['pillars']['indonesian']['title']);
                    @endphp
                    <div class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] transition-all duration-300 flex flex-col justify-between min-h-[310px]">
                        {{-- Right Landmark Image with Soft Gradient Fade --}}
                        <div class="absolute right-0 top-0 bottom-0 w-[42%] sm:w-[40%] pointer-events-none select-none overflow-hidden">
                            <img src="{{ asset('images/trilingual/indonesia.jpg') }}" alt="Bendera Merah Putih" class="h-full w-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700" />
                            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/30 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-white/70 via-transparent to-transparent"></div>
                        </div>

                        {{-- Card Content --}}
                        <div class="relative z-10 max-w-[68%]">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-200">03</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5 text-xs font-bold border border-emerald-100 shadow-2xs">
                                    <svg class="h-3 w-4 rounded-2xs shrink-0 shadow-2xs border border-gray-200" viewBox="0 0 3 2">
                                        <rect width="3" height="1" fill="#e70011"/>
                                        <rect y="1" width="3" height="1" fill="#ffffff"/>
                                    </svg>
                                    <span>Indonesia</span>
                                </span>
                            </div>

                            <h3 class="mt-4 text-xl sm:text-2xl font-bold text-gray-900 leading-snug">
                                {{ $indonesianParts[0] }}
                                @if(isset($indonesianParts[1]))
                                    <br /><span class="font-bold text-gray-900">({{ $indonesianParts[1] }}</span>
                                @endif
                            </h3>

                            <p class="mt-3 text-xs sm:text-[13px] leading-relaxed text-gray-600">
                                {{ $profile['pillars']['indonesian']['desc'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-line" />

            {{-- 3. Kualifikasi Pendidik & Prestasi Global --}}
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                    {{ $profile['excellence_heading'] }}
                </h2>

                <div class="mt-4 space-y-4 text-sm sm:text-base leading-relaxed text-muted">
                    <p>{{ $profile['excellence_p1'] }}</p>
                    <p>{{ $profile['excellence_p2'] }}</p>
                </div>

                {{-- Highlight Box --}}
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-line bg-card p-5 shadow-xs">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                <x-site-icon name="trophy" class="size-5" />
                            </span>
                            <div class="text-sm font-bold text-foreground">{{ $profile['excellence_card1_title'] }}</div>
                        </div>
                        <p class="mt-2 text-xs text-muted leading-relaxed">
                            {{ $profile['excellence_card1_desc'] }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-line bg-card p-5 shadow-xs">
                        <div class="flex items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-jts">
                                <x-site-icon name="graduation-cap" class="size-5" />
                            </span>
                            <div class="text-sm font-bold text-foreground">{{ $profile['excellence_card2_title'] }}</div>
                        </div>
                        <p class="mt-2 text-xs text-muted leading-relaxed">
                            {{ $profile['excellence_card2_desc'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Box (Reusable Component) --}}
            <x-cta-banner
                :title="$profile['cta_title']"
                :description="$profile['cta_desc']"
                :primary-url="$site::path('/admissions')"
                :primary-label="$profile['cta_admissions']"
                :secondary-url="$site::path('/contact')"
                :secondary-label="$profile['cta_contact']"
            />

        </div>
    </div>
</x-layouts.site>
