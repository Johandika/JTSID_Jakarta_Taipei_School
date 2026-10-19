<x-layouts.site :title="__('pages.admissions.seo_title') ?: __('pages.admissions.title')" :description="__('pages.admissions.summary')">
    @php
        $site = \App\Support\Site::class;
        $admissions = __('pages.admissions');
        $feesByLevel = \App\Models\AdmissionFee::where('is_active', true)
            ->with('gradeLevel')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn ($fee) => $fee->gradeLevel?->code ?? 'tk');

        $levelTabs = [
            'tk' => [
                'name' => $admissions['levels']['tk']['name'] ?? 'Taman Kanak-Kanak',
                'sub' => $admissions['levels']['tk']['sub'] ?? '(PK, K1, K2)',
                'icon' => 'rabbit',
            ],
            'sd' => [
                'name' => $admissions['levels']['sd']['name'] ?? 'Sekolah Dasar',
                'sub' => $admissions['levels']['sd']['sub'] ?? '(SD)',
                'icon' => 'book-open',
            ],
            'smp' => [
                'name' => $admissions['levels']['smp']['name'] ?? 'Sekolah Menengah Pertama',
                'sub' => $admissions['levels']['smp']['sub'] ?? '(SMP)',
                'icon' => 'book',
            ],
            'sma' => [
                'name' => $admissions['levels']['sma']['name'] ?? 'Sekolah Menengah Atas',
                'sub' => $admissions['levels']['sma']['sub'] ?? '(SMA)',
                'icon' => 'graduation-cap',
            ],
            'international' => [
                'name' => $admissions['levels']['international']['name'] ?? 'Siswa Internasional',
                'sub' => $admissions['levels']['international']['sub'] ?? '(Semua Jenjang)',
                'icon' => 'globe',
            ],
        ];
        $admissionFormsByLevel = \App\Models\AdmissionForm::downloadableByLevel();
        $downloadForms = collect($levelTabs)
            ->mapWithKeys(fn ($tab, $code) => [
                $code => ($form = $admissionFormsByLevel->get($code)) ? [
                    'url' => $form->download_url,
                    'name' => $form->download_name,
                    'label' => __('site.download_admission_form').' '.($admissions['levels'][$code]['button'] ?? $form->school_level_label),
                ] : null,
            ])
            ->all();
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/admissions-hero.webp') }}" alt="{{ $admissions['title'] }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $admissions['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $admissions['summary'] }}</p>
        </div>
    </div>

    {{-- Admissions Content --}}
    <div class="shell py-12 sm:py-16">
        <div class="space-y-16">

            {{-- 1. Rincian Biaya (Fee Structure) with 5 Interactive Tabs --}}
            <div x-data="{ activeTab: 'tk', downloadForms: {{ \Illuminate\Support\Js::from($downloadForms) }} }" class="w-full">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                            {{ $admissions['fees_heading'] }}
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-muted max-w-2xl">
                            {{ $admissions['fees_subheading'] }}
                        </p>
                    </div>

                    <a
                        :href="downloadForms[activeTab]?.url || '#'"
                        :download="downloadForms[activeTab]?.name || null"
                        :aria-disabled="downloadForms[activeTab] ? 'false' : 'true'"
                        class="btn btn-primary inline-flex w-fit items-center gap-2 shadow-xs transition-all hover:shadow-md"
                        :class="downloadForms[activeTab] ? '' : 'pointer-events-none opacity-60'"
                    >
                        <x-site-icon name="download" class="size-4" />
                        <span x-text="downloadForms[activeTab]?.label || '{{ __('site.download_admission_form') }}'">{{ __('site.download_admission_form') }}</span>
                    </a>
                </div>

                {{-- 5 Level Selection Tabs --}}
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                    @foreach($levelTabs as $code => $tab)
                        <button
                            type="button"
                            @click="activeTab = '{{ $code }}'"
                            class="flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-2xl text-left transition-all duration-300 border focus:outline-none focus:ring-2 focus:ring-[#176443] focus:ring-offset-2 last:col-span-2 sm:last:col-span-1"
                            :class="activeTab === '{{ $code }}'
                                ? 'bg-[#0a4a30] border-[#0a4a30] text-white shadow-lg -translate-y-0.5'
                                : 'bg-white border-line text-ink hover:border-[#176443]/40 hover:bg-[#fbfcfb] shadow-xs'"
                        >
                            {{-- Tab Icon --}}
                            <div
                                class="size-11 sm:size-12 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                :class="activeTab === '{{ $code }}' ? 'bg-white/15 text-white' : 'bg-[#eaf4ed] text-[#176443]'"
                            >
                                @if($tab['icon'] === 'rabbit')
                                    {{-- Cute Rabbit Icon --}}
                                    <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 12c-3.5 0-6 2.5-6 6a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3c0-3.5-2.5-6-6-6zM8.5 3a2 2 0 0 0-2 2v6.5a1 1 0 0 0 2 0V5a1 1 0 0 1 2 0v5a1 1 0 0 0 2 0V5a2 2 0 0 0-4-2zm7 0a2 2 0 0 0-2 2v5a1 1 0 0 0 2 0V5a1 1 0 0 1 2 0v6.5a1 1 0 0 0 2 0V5a2 2 0 0 0-4-2z"/>
                                    </svg>
                                @elseif($tab['icon'] === 'book-open')
                                    <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                @elseif($tab['icon'] === 'book')
                                    <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                        <path d="M6 6h10"/>
                                        <path d="M6 10h10"/>
                                    </svg>
                                @elseif($tab['icon'] === 'graduation-cap')
                                    <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/>
                                        <path d="M22 10v6"/>
                                        <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>
                                    </svg>
                                @elseif($tab['icon'] === 'globe')
                                    <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="2" y1="12" x2="22" y2="12"/>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="text-xs sm:text-sm font-bold leading-tight truncate">
                                    {{ $tab['name'] }}
                                </div>
                                <div
                                    class="text-[11px] sm:text-xs font-medium mt-0.5 truncate"
                                    :class="activeTab === '{{ $code }}' ? 'text-white/80' : 'text-muted'"
                                >
                                    {{ $tab['sub'] }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Table Container per Tab --}}
                <div class="mt-6 rounded-2xl border border-line bg-white shadow-xs overflow-hidden">
                    {{-- Header Table --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 bg-[#f0f4f1] border-b border-line">
                        <div class="px-6 py-4 text-sm font-bold text-ink tracking-tight sm:border-r border-line">
                            {{ $admissions['fee_type'] }}
                        </div>
                        <div class="hidden sm:block px-6 py-4 text-left text-sm font-bold text-ink">
                            @foreach($levelTabs as $code => $tab)
                                <span x-show="activeTab === '{{ $code }}'" x-cloak>
                                    {{ $tab['name'] }} {{ $tab['sub'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rows Content per Tab --}}
                    @foreach($levelTabs as $code => $tab)
                        <div x-show="activeTab === '{{ $code }}'" x-cloak class="divide-y divide-line/70">
                            @forelse($feesByLevel->get($code, collect()) as $fee)
                                @php
                                    $iconColor = $fee->icon_color ?? 'green';
                                    $bgClass = match($iconColor) {
                                        'blue' => 'bg-[#e9f2fa] text-[#1e6ca8] border border-[#d6e7f7]',
                                        'pink' => 'bg-[#fbebee] text-[#d6455b] border border-[#f7d6dc]',
                                        'amber' => 'bg-[#fdf4e4] text-[#b87c12] border border-[#fbe5c3]',
                                        default => 'bg-[#eaf4ed] text-[#176443] border border-[#d5eadc]',
                                    };
                                    $note = $fee->trans('note');
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 items-center hover:bg-[#fafbfa] transition-colors">
                                    {{-- Left Column: Soft Colored Icon + Fee Name --}}
                                    <div class="flex items-center gap-4 sm:gap-5 min-w-0 p-5 sm:p-6 sm:border-r border-line/70">
                                        <div class="size-12 sm:size-14 rounded-2xl {{ $bgClass }} flex items-center justify-center shrink-0 shadow-xs">
                                            @if($fee->icon === 'file-text')
                                                <svg class="size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                                    <path d="M10 9H8"/>
                                                    <path d="M16 13H8"/>
                                                    <path d="M16 17H8"/>
                                                </svg>
                                            @elseif($fee->icon === 'building')
                                                <svg class="size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 21h18"/>
                                                    <path d="M12 2 4 7v14"/>
                                                    <path d="M20 21V7l-8-5"/>
                                                    <path d="M9 10h1"/>
                                                    <path d="M9 14h1"/>
                                                    <path d="M14 10h1"/>
                                                    <path d="M14 14h1"/>
                                                    <path d="M12 17v4"/>
                                                    <path d="M12 2v3"/>
                                                    <path d="M10.5 3.5h3"/>
                                                </svg>
                                            @elseif($fee->icon === 'backpack')
                                                <svg class="size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 10a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                                    <path d="M8 21v-5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v5"/>
                                                    <path d="M8 10h8"/>
                                                </svg>
                                            @elseif($fee->icon === 'book-open')
                                                <svg class="size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                                </svg>
                                            @else
                                                <svg class="size-6 sm:size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <h3 class="text-base sm:text-lg font-bold text-ink tracking-tight">
                                                {{ $fee->trans('name') }}
                                            </h3>
                                        </div>
                                    </div>

                                    {{-- Right Column: Amount, Period & Optional Note Box (Rata Kiri) --}}
                                    <div class="p-5 sm:p-6 text-left flex flex-col justify-center">
                                        <div class="text-xl sm:text-2xl font-bold text-ink tracking-tight">
                                            {{ $fee->amount }}
                                        </div>

                                        <p class="mt-0.5 text-xs sm:text-sm text-muted">
                                            {{ $fee->trans('payment_period') }}
                                        </p>

                                        {{-- Optional Note Box --}}
                                        @if(!empty(trim((string)$note)))
                                            <div class="mt-3 w-full max-w-lg rounded-xl bg-[#eef7fd] border border-[#d6ecfb] p-3 text-xs sm:text-sm text-[#0c4a6e] font-medium leading-relaxed whitespace-pre-line shadow-2xs">{{ trim((string)$note) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-muted text-sm">
                                    {{ __('site.no_results') }}
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. Journey / Steps Section --}}
            <div class="max-w-3xl">
                <h2 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                    {{ $admissions['journey_heading'] }}
                </h2>
                <div class="mt-6 space-y-4">
                    @foreach($admissions['steps'] as $index => $step)
                        <div class="flex items-start gap-4 p-4 sm:p-5 rounded-2xl border border-line bg-[#fbfdfb] transition-colors hover:border-[#176443]/40 shadow-xs">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#176443] text-sm font-bold text-white shadow-xs">
                                {{ $index + 1 }}
                            </span>
                            <p class="text-base text-ink font-medium pt-1 leading-relaxed">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. FAQ Section --}}
            <div class="max-w-3xl">
                <h2 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                    {{ $admissions['faq_heading'] }}
                </h2>
                <div class="mt-6 space-y-4">
                    <div class="p-5 sm:p-6 rounded-2xl border border-line bg-white shadow-xs">
                        <h3 class="text-base sm:text-lg font-semibold text-ink flex items-center gap-2.5">
                            <span class="size-2 rounded-full bg-[#176443]"></span>
                            {{ $admissions['fees_question'] }}
                        </h3>
                        <p class="mt-2.5 text-sm sm:text-base leading-relaxed text-muted pl-4.5">
                            {{ $admissions['fees_answer'] }}
                        </p>
                    </div>

                    <div class="p-5 sm:p-6 rounded-2xl border border-line bg-white shadow-xs">
                        <h3 class="text-base sm:text-lg font-semibold text-ink flex items-center gap-2.5">
                            <span class="size-2 rounded-full bg-[#176443]"></span>
                            {{ $admissions['mandarin_question'] }}
                        </h3>
                        <p class="mt-2.5 text-sm sm:text-base leading-relaxed text-muted pl-4.5">
                            {{ $admissions['mandarin_prefix'] }}<a class="text-[#176443] font-semibold underline hover:text-[#104a31] transition-colors" href="{{ $site::path('/mandarin-program') }}">{{ $admissions['mandarin_link'] }}</a>{{ $admissions['mandarin_suffix'] }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- CTA Box (Reusable Component) --}}
        <div class="mt-14 sm:mt-16">
            <x-cta-banner
                :primary-url="$site::path('/contact')"
                :primary-label="__('pages.school_profile.cta_contact')"
                :secondary-url="$site::path('/school-profile')"
                :secondary-label="__('site.school_profile')"
            />
        </div>
    </div>
</x-layouts.site>
