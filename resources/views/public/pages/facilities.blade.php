<x-layouts.site :title="__('pages.facilities.seo_title') ?: __('pages.facilities.title')" :description="__('pages.facilities.summary')">
    @php
        $site = \App\Support\Site::class;
        $facilitiesPage = __('pages.facilities');
        $initialTab = $categoriesWithFacilities->first()['key'] ?? '';
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/campus.webp') }}" alt="{{ $facilitiesPage['title'] }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $facilitiesPage['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $facilitiesPage['summary'] }}</p>
        </div>
    </div>

    {{-- Main Facilities Gallery Section --}}
    <div
        x-data="{
            activeTab: '{{ $initialTab }}',
            previewModal: null
        }"
        class="shell py-12 sm:py-16 lg:py-20"
    >
        {{-- Section Header matching visual reference --}}
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-jts">
                <span class="inline-block h-0.5 w-6 bg-jts"></span>
                <span>{{ $facilitiesPage['eyebrow'] }}</span>
                <span class="inline-block h-0.5 w-6 bg-jts"></span>
            </div>

            <h2 class="mt-3 text-3xl font-bold tracking-tight text-ink sm:text-4xl md:text-5xl">
                @if(app()->getLocale() === 'en')
                    Explore <span class="text-jts">Jakarta Taipei School</span> Facilities
                @elseif(app()->getLocale() === 'zh' || app()->getLocale() === 'zh_TW')
                    探索 <span class="text-jts">雅加達臺灣學校</span> 校園設施
                @else
                    Jelajahi Fasilitas <span class="text-jts">Jakarta Taipei School</span>
                @endif
            </h2>

            <p class="mt-3 text-base sm:text-lg text-muted max-w-2xl mx-auto">
                {{ $facilitiesPage['subheading'] }}
            </p>
        </div>

        {{-- Interactive Filter Tabs (Hanya menampilkan kategori yang memiliki foto) --}}
        @if($categoriesWithFacilities->isNotEmpty())
            <div class="mt-8 sm:mt-10 flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
                @foreach($categoriesWithFacilities as $category)
                    <button
                        type="button"
                        @click="activeTab = '{{ $category['key'] }}'"
                        class="px-5 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 border focus:outline-none focus:ring-2 focus:ring-jts/40"
                        :class="activeTab === '{{ $category['key'] }}'
                            ? 'bg-jts text-white border-jts shadow-sm -translate-y-0.5'
                            : 'bg-white border-line text-ink hover:border-jts/40 hover:bg-[#fbfcfb]'"
                    >
                        {{ $category['name'] }}
                    </button>
                @endforeach
            </div>

            {{-- Facilities Grid per Category --}}
            <div class="mt-10 sm:mt-12">
                @foreach($categoriesWithFacilities as $category)
                    <div
                        x-show="activeTab === '{{ $category['key'] }}'"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
                    >
                        @forelse($category['items'] as $facility)
                            <div
                                @click="previewModal = {
                                    image: '{{ $facility->image_url }}',
                                    name: '{{ addslashes($facility->trans('name')) }}',
                                    category: '{{ addslashes($category['name']) }}'
                                }"
                                class="group relative aspect-[16/10] overflow-hidden rounded-2xl bg-gray-100 border border-line/60 shadow-xs cursor-pointer hover:shadow-md transition-all duration-300"
                            >
                                {{-- Facility Photo --}}
                                <img
                                    src="{{ $facility->image_url }}"
                                    alt="{{ $facility->trans('name') }}"
                                    class="size-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                                    loading="lazy"
                                />

                                {{-- Dark gradient shadow overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent pointer-events-none"></div>

                                {{-- Bottom Content --}}
                                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 flex items-end justify-between">
                                    <h3 class="text-lg sm:text-xl font-bold text-white drop-shadow-sm tracking-tight">
                                        {{ $facility->trans('name') }}
                                    </h3>

                                    {{-- Micro-interaction zoom icon --}}
                                    <span class="size-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-3">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-16 text-center text-muted border border-dashed border-line rounded-2xl">
                                {{ $facilitiesPage['empty'] }}
                            </div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-12 py-16 text-center text-muted border border-dashed border-line rounded-2xl">
                {{ $facilitiesPage['empty'] }}
            </div>
        @endif

        {{-- Lightbox Modal Preview --}}
        <div
            x-show="previewModal"
            x-cloak
            @keydown.escape.window="previewModal = null"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="previewModal = null"
                class="relative max-w-4xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="relative bg-black flex items-center justify-center max-h-[75vh]">
                    <img :src="previewModal?.image" :alt="previewModal?.name" class="max-h-[75vh] w-auto max-w-full object-contain" />
                    <button
                        type="button"
                        @click="previewModal = null"
                        class="absolute top-4 right-4 size-10 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black/90 transition-colors focus:outline-none"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="p-5 sm:p-6 flex items-center justify-between bg-white border-t border-line">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-jts" x-text="previewModal?.category"></span>
                        <h4 class="text-xl font-bold text-ink mt-0.5" x-text="previewModal?.name"></h4>
                    </div>
                    <button
                        type="button"
                        @click="previewModal = null"
                        class="btn btn-outline text-xs px-4 py-2"
                    >
                        {{ __('site.continue_site') ?? 'Tutup' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.site>
