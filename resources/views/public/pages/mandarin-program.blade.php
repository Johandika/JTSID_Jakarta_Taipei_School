<x-layouts.site :title="__('pages.mandarin_program.seo_title') ?: __('pages.mandarin_program.title')" :description="__('pages.mandarin_program.summary')">
    @php
        $site = \App\Support\Site::class;
        $page = __('pages.mandarin_program');
        $programs = \App\Models\MandarinProgram::where('is_active', true)
            ->orderByRaw("CASE WHEN status_override = 'program_done' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'desc')
            ->get();

        $programsData = $programs->map(function ($p) {
            $gallery = $p->gallery_image_urls;
            if (empty($gallery) && $p->hasCoverImage()) {
                $gallery = [$p->cover_image_url];
            } elseif ($p->hasCoverImage() && ! in_array($p->cover_image_url, $gallery, true)) {
                array_unshift($gallery, $p->cover_image_url);
            }

            return [
                'id' => $p->id,
                'category' => $p->category,
                'category_label' => $p->getCategoryLabel(),
                'title' => $p->trans('title'),
                'short_description' => $p->trans('short_description'),
                'description' => $p->trans('description'),
                'cover_image' => $p->cover_image_url,
                'gallery_images' => $gallery,
                'program_date_label' => $p->getProgramDateLabelText(),
                'program_date' => $p->getFormattedProgramPeriod(),
                'registration_date' => $p->getFormattedRegistrationPeriod(),
                'location' => $p->trans('location'),
                'contact_info' => $p->trans('contact_info'),
                'registration_url' => $p->registration_url,
                'status' => $p->computed_status,
                'status_label' => $p->getStatusLabel(),
                'is_done' => $p->computed_status === \App\Models\MandarinProgram::STATUS_PROGRAM_DONE,
                'can_register' => $p->canRegister(),
                'show_details' => (bool) $p->show_details,
                'extra_info_title' => $p->trans('extra_info_title'),
                'extra_info_items' => $p->extra_info_items ?: [],
            ];
        })->values()->all();
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/trilingual/mandarin.webp') }}" alt="{{ $page['title'] }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $page['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $page['summary'] }}</p>
        </div>
    </div>

    {{-- Programs Section --}}
    <div
        id="programs-section-header"
        class="shell py-12 sm:py-16 scroll-mt-6"
        x-data="{
            activeCategory: 'all',
            isModalOpen: false,
            selectedProgram: null,
            activeImage: '',
            pageSize: 9,
            currentPage: 1,
            programs: {{ Js::from($programsData) }},
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const pageParam = parseInt(urlParams.get('page'), 10);
                if (pageParam && pageParam > 0) {
                    this.currentPage = pageParam;
                }
                const catParam = urlParams.get('category');
                if (catParam) {
                    this.activeCategory = catParam;
                }
            },
            get filteredPrograms() {
                if (this.activeCategory === 'all') {
                    return this.programs;
                }
                return this.programs.filter(p => p.category === this.activeCategory);
            },
            get totalFilteredCount() {
                return this.filteredPrograms.length;
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.totalFilteredCount / this.pageSize));
            },
            get firstItemIndex() {
                if (this.totalFilteredCount === 0) return 0;
                return (this.currentPage - 1) * this.pageSize + 1;
            },
            get lastItemIndex() {
                return Math.min(this.currentPage * this.pageSize, this.totalFilteredCount);
            },
            isItemVisible(programId) {
                const list = this.filteredPrograms;
                const startIndex = (this.currentPage - 1) * this.pageSize;
                const endIndex = startIndex + this.pageSize;
                const pageItems = list.slice(startIndex, endIndex);
                return pageItems.some(p => p.id === programId);
            },
            goToPage(p) {
                if (p < 1 || p > this.totalPages) return;
                this.currentPage = p;
                this.syncUrl();
                this.scrollToGrid();
            },
            prevPage() {
                if (this.currentPage > 1) {
                    this.goToPage(this.currentPage - 1);
                }
            },
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.goToPage(this.currentPage + 1);
                }
            },
            setCategory(cat) {
                this.activeCategory = cat;
                this.currentPage = 1;
                this.syncUrl();
            },
            syncUrl() {
                const url = new URL(window.location.href);
                if (this.currentPage > 1) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.delete('page');
                }
                if (this.activeCategory !== 'all') {
                    url.searchParams.set('category', this.activeCategory);
                } else {
                    url.searchParams.delete('category');
                }
                window.history.replaceState({}, '', url.toString());
            },
            scrollToGrid() {
                const el = document.getElementById('programs-section-header');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },
            openModal(item) {
                this.selectedProgram = item;
                this.activeImage = item.gallery_images && item.gallery_images.length ? item.gallery_images[0] : item.cover_image;
                this.isModalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            closeModal() {
                this.isModalOpen = false;
                this.selectedProgram = null;
                this.activeImage = '';
                document.body.style.overflow = '';
            }
        }"
    >
        {{-- Section Header & Interactive Filter Bar --}}
        <div class="pb-8 border-b border-line/60">
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#0e3b2a]">{{ $page['heading'] }}</h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600 max-w-2xl">{{ $page['subheading'] }}</p>
            </div>

            {{-- Interactive Category Filter Pills (Under subtitle, left-aligned) --}}
            <div class="mt-6 flex flex-wrap items-center gap-2 sm:gap-2.5">
                <button
                    type="button"
                    @click="setCategory('all')"
                    class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 focus:outline-none"
                    :class="activeCategory === 'all' ? 'bg-[#0d6b46] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900'"
                >
                    {{ $page['filter_all'] }}
                </button>
                <button
                    type="button"
                    @click="setCategory('mandarin_class')"
                    class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 focus:outline-none"
                    :class="activeCategory === 'mandarin_class' ? 'bg-[#0d6b46] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900'"
                >
                    {{ $page['filter_mandarin_class'] }}
                </button>
                <button
                    type="button"
                    @click="setCategory('tocfl')"
                    class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 focus:outline-none"
                    :class="activeCategory === 'tocfl' ? 'bg-[#0d6b46] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900'"
                >
                    {{ $page['filter_tocfl'] }}
                </button>
                <button
                    type="button"
                    @click="setCategory('workshop_event')"
                    class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold transition-all duration-200 focus:outline-none"
                    :class="activeCategory === 'workshop_event' ? 'bg-[#0d6b46] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900'"
                >
                    {{ $page['filter_workshop'] }}
                </button>
            </div>
        </div>

        {{-- Programs Responsive Grid --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
            @forelse($programs as $index => $program)
                @php
                    $isDone = $program->computed_status === \App\Models\MandarinProgram::STATUS_PROGRAM_DONE;
                    $canRegister = $program->canRegister();
                    $category = $program->category;
                    $programDataJson = json_encode($programsData[$index] ?? []);
                @endphp
                <div
                    x-show="isItemVisible({{ $program->id }})"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="group relative flex flex-col justify-between rounded-2xl border bg-white p-5 transition-all duration-300 hover:shadow-lg {{ $isDone ? 'border-slate-200 bg-slate-50/50 opacity-90 hover:opacity-100' : 'border-slate-200 hover:border-[#176443]/30 shadow-xs' }}"
                >
                    {{-- PROGRAM DONE Stamp treatment --}}
                    @if($isDone)
                        <div class="pointer-events-none absolute right-4 top-28 sm:top-32 z-10 rotate-[-12deg] rounded-md border-2 border-slate-400/80 bg-white/95 px-2.5 py-0.5 text-[11px] font-black uppercase tracking-wider text-slate-500 shadow-xs">
                            {{ $page['program_done_stamp'] }}
                        </div>
                    @endif

                    <div>
                        {{-- Top Details Row (Cover Flyer on Left + Info on Right) --}}
                        <div class="flex items-start gap-4">
                            {{-- Cover Flyer Thumbnail --}}
                            <div class="relative w-28 sm:w-32 aspect-[3/4] shrink-0 overflow-hidden rounded-xl border border-slate-200/80 bg-slate-100 shadow-2xs">
                                <img
                                    src="{{ $program->cover_image_url }}"
                                    alt="{{ $program->trans('title') }}"
                                    class="size-full object-cover object-center group-hover:scale-102 transition-all duration-300 {{ $isDone ? 'grayscale opacity-75 contrast-95' : '' }}"
                                    loading="lazy"
                                />
                                @if($isDone)
                                    <div class="absolute inset-0 bg-slate-900/10 pointer-events-none"></div>
                                @endif
                            </div>

                            {{-- Right Info (Badges, Title, Short Desc, Metadata) --}}
                            <div class="flex-1 min-w-0">
                                {{-- Badges Row --}}
                                <div class="flex flex-wrap items-center gap-1.5">
                                    {{-- Category Badge --}}
                                    @php
                                        $catColor = match($category) {
                                            'mandarin_class' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                            'tocfl' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'workshop_event' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                        $statusColor = match($program->computed_status) {
                                            'registration_open' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                            'upcoming' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'registration_closed' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            'program_done' => 'bg-slate-100 text-slate-500 border-slate-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $catColor }}">
                                        {{ $program->getCategoryLabel() }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium border {{ $statusColor }}">
                                        {{ $program->getStatusLabel() }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h3 class="mt-2 text-base sm:text-lg font-bold text-slate-900 leading-snug group-hover:text-[#0e3b2a] transition-colors line-clamp-2">
                                    {{ $program->trans('title') }}
                                </h3>

                                {{-- Short Description --}}
                                @if($program->trans('short_description'))
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $program->trans('short_description') }}
                                    </p>
                                @endif

                                {{-- Metadata Items List --}}
                                <div class="mt-3 space-y-1.5 text-xs text-slate-600">
                                    {{-- Program Date / Period --}}
                                    @if($program->getFormattedProgramPeriod())
                                        <div class="flex items-start gap-1.5">
                                            <x-site-icon name="calendar" class="size-3.5 text-slate-400 mt-0.5 shrink-0" />
                                            <div class="min-w-0">
                                                <span class="font-medium text-slate-700 block text-[11px]">{{ $program->getProgramDateLabelText() }}</span>
                                                <span class="text-slate-600 truncate block">{{ $program->getFormattedProgramPeriod() }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Registration Period --}}
                                    @if($program->getFormattedRegistrationPeriod())
                                        <div class="flex items-start gap-1.5">
                                            <x-site-icon name="file-text" class="size-3.5 text-slate-400 mt-0.5 shrink-0" />
                                            <div class="min-w-0">
                                                <span class="font-medium text-slate-700 block text-[11px]">{{ $page['registration_period'] ?? 'Registration Period' }}</span>
                                                <span class="text-slate-600 truncate block">{{ $program->getFormattedRegistrationPeriod() }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Location --}}
                                    @if($program->trans('location'))
                                        <div class="flex items-center gap-1.5">
                                            <x-site-icon name="map-pin" class="size-3.5 text-slate-400 shrink-0" />
                                            <span class="text-slate-600 truncate">{{ $program->trans('location') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Action Row (Registration CTA + View Details) --}}
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        {{-- Left CTA Button --}}
                        @if($canRegister)
                            <a
                                href="{{ $program->registration_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0d6b46] hover:bg-[#094c31] text-white text-xs font-semibold shadow-xs transition-colors"
                            >
                                <span>{{ $page['registration_now'] }}</span>
                                <x-site-icon name="arrow-up-right" class="size-3.5" />
                            </a>
                        @elseif($isDone)
                            <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 text-slate-400 text-xs font-medium border border-slate-200/60 select-none cursor-not-allowed">
                                <x-site-icon name="check" class="size-3.5 text-slate-400" />
                                <span>{{ $page['program_completed'] }}</span>
                            </div>
                        @else
                            <div></div>
                        @endif

                        {{-- Right "View Details ->" Action --}}
                        @if($program->show_details)
                            <button
                                type="button"
                                @click="openModal({{ $programDataJson }})"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-[#0d6b46] transition-colors py-1 cursor-pointer focus:outline-none"
                            >
                                <span>{{ $page['view_details'] }}</span>
                                <x-site-icon name="arrow-right" class="size-3.5 transition-transform group-hover:translate-x-0.5" />
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-500">
                    <p>{{ $page['no_programs'] }}</p>
                </div>
            @endforelse

            {{-- Empty State when filtered --}}
            <div x-show="totalFilteredCount === 0" x-cloak class="col-span-full py-14 text-center text-slate-500">
                <x-site-icon name="search" class="mx-auto mb-3 size-8 text-slate-300" />
                <p class="text-base font-medium text-slate-600">{{ $page['no_programs'] }}</p>
            </div>
        </div>

        {{-- Pagination Navigation (Shown when totalFilteredCount > pageSize, max 3x3 grid) --}}
        <div x-show="totalFilteredCount > pageSize" x-cloak class="mt-10">
            <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
                {{-- Mobile Pagination (< sm) --}}
                <div class="flex gap-2 items-center justify-between sm:hidden w-full">
                    <button
                        type="button"
                        @click="prevPage()"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'cursor-not-allowed text-gray-400 bg-gray-50' : 'text-gray-800 bg-white hover:bg-gray-100'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium border border-gray-300 leading-5 rounded-md transition ease-in-out duration-150"
                    >
                        {!! __('pagination.previous') !!}
                    </button>

                    <span class="text-xs text-gray-600 font-medium">
                        <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                    </span>

                    <button
                        type="button"
                        @click="nextPage()"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'cursor-not-allowed text-gray-400 bg-gray-50' : 'text-gray-800 bg-white hover:bg-gray-100'"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium border border-gray-300 leading-5 rounded-md transition ease-in-out duration-150"
                    >
                        {!! __('pagination.next') !!}
                    </button>
                </div>

                {{-- Desktop Pagination (sm+) --}}
                <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 leading-5">
                            {!! __('Showing') !!}
                            <span class="font-medium" x-text="firstItemIndex"></span>
                            {!! __('to') !!}
                            <span class="font-medium" x-text="lastItemIndex"></span>
                            {!! __('of') !!}
                            <span class="font-medium" x-text="totalFilteredCount"></span>
                            {!! __('results') !!}
                        </p>
                    </div>

                    <div>
                        <span class="inline-flex shadow-xs rounded-md">
                            {{-- Previous Page Button --}}
                            <button
                                type="button"
                                @click="prevPage()"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'cursor-not-allowed text-gray-300' : 'text-gray-500 hover:text-gray-700 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500'"
                                class="inline-flex items-center px-2 py-2 text-sm font-medium bg-white border border-gray-300 rounded-l-md leading-5 transition ease-in-out duration-150"
                                aria-label="{{ __('pagination.previous') }}"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Numeric Page Buttons --}}
                            <template x-for="p in totalPages" :key="p">
                                <button
                                    type="button"
                                    @click="goToPage(p)"
                                    class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border border-gray-300 leading-5 transition ease-in-out duration-150"
                                    :class="currentPage === p ? 'bg-gray-200 text-gray-800 font-bold cursor-default' : 'bg-white text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                    x-text="p"
                                ></button>
                            </template>

                            {{-- Next Page Button --}}
                            <button
                                type="button"
                                @click="nextPage()"
                                :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'cursor-not-allowed text-gray-300' : 'text-gray-500 hover:text-gray-700 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500'"
                                class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium bg-white border border-gray-300 rounded-r-md leading-5 transition ease-in-out duration-150"
                                aria-label="{{ __('pagination.next') }}"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>
            </nav>
        </div>

        {{-- PROGRAM DETAILS POPUP MODAL --}}
        <div
            x-show="isModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            @keydown.escape.window="closeModal()"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="isModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeModal()"
                class="fixed inset-0 bg-slate-900/65 backdrop-blur-xs transition-opacity"
            ></div>

            {{-- Modal Content Card --}}
            <div
                x-show="isModalOpen"
                x-transition:enter="ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-xl sm:max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 max-h-[90vh] flex flex-col overflow-hidden z-10"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900">{{ $page['program_details'] }}</h3>
                    <button
                        type="button"
                        @click="closeModal()"
                        class="size-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none"
                        aria-label="{{ $page['close'] ?? 'Close' }}"
                    >
                        <x-site-icon name="x" class="size-4" />
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="px-6 sm:px-8 py-6 overflow-y-auto space-y-6" x-if="selectedProgram">
                    <template x-if="selectedProgram">
                        <div>
                            {{-- Top Row: Gallery Preview + Main Program Info --}}
                            <div class="flex flex-col sm:flex-row items-start gap-6">
                                {{-- Left: Big Image & Thumbnail Row --}}
                                <div class="w-full sm:w-52 shrink-0">
                                    <div class="relative aspect-[3/4] w-full rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-50">
                                        <img
                                            :src="activeImage || '{{ asset('images/placeholder.svg') }}'"
                                            :alt="selectedProgram.title"
                                            class="size-full object-cover object-center transition-all duration-300"
                                            :class="selectedProgram && selectedProgram.is_done ? 'grayscale opacity-75 contrast-95' : ''"
                                        />
                                        <div
                                            x-show="selectedProgram && selectedProgram.is_done"
                                            class="absolute inset-0 bg-slate-900/10 pointer-events-none"
                                        ></div>
                                    </div>

                                    {{-- Thumbnails Row (if multiple images) --}}
                                    <template x-if="selectedProgram.gallery_images && selectedProgram.gallery_images.length > 1">
                                        <div class="mt-3 flex items-center gap-2 overflow-x-auto pb-1">
                                            <template x-for="(img, idx) in selectedProgram.gallery_images" :key="idx">
                                                <button
                                                    type="button"
                                                    @click="activeImage = img"
                                                    class="size-12 shrink-0 rounded-lg overflow-hidden border transition-all"
                                                    :class="activeImage === img ? 'ring-2 ring-[#0d6b46] border-transparent opacity-100' : 'border-slate-200 opacity-60 hover:opacity-100'"
                                                >
                                                    <img :src="img" class="size-full object-cover" :class="selectedProgram && selectedProgram.is_done ? 'grayscale opacity-75' : ''" />
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                {{-- Right: Badges, Title, Meta, CTA --}}
                                <div class="flex-1 min-w-0">
                                    {{-- Badges --}}
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                            :class="{
                                                'bg-rose-50 text-rose-700 border-rose-200': selectedProgram.category === 'mandarin_class',
                                                'bg-sky-50 text-sky-700 border-sky-200': selectedProgram.category === 'tocfl',
                                                'bg-amber-50 text-amber-700 border-amber-200': selectedProgram.category === 'workshop_event',
                                            }"
                                            x-text="selectedProgram.category_label"
                                        ></span>

                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': selectedProgram.status === 'registration_open',
                                                'bg-sky-50 text-sky-700 border-sky-200': selectedProgram.status === 'upcoming',
                                                'bg-amber-50 text-amber-700 border-amber-200': selectedProgram.status === 'registration_closed',
                                                'bg-slate-100 text-slate-500 border-slate-200': selectedProgram.status === 'program_done',
                                            }"
                                            x-text="selectedProgram.status_label"
                                        ></span>
                                    </div>

                                    {{-- Title --}}
                                    <h4 class="mt-2 text-2xl font-bold text-slate-900 leading-tight" x-text="selectedProgram.title"></h4>

                                    {{-- Short Description --}}
                                    <p class="mt-2 text-sm text-slate-600 leading-relaxed" x-text="selectedProgram.short_description"></p>

                                    {{-- Metadata List --}}
                                    <div class="mt-4 space-y-2 text-sm text-slate-600 border-t border-slate-100 pt-3">
                                        <template x-if="selectedProgram.program_date">
                                            <div class="flex items-start gap-2">
                                                <x-site-icon name="calendar" class="size-4 text-slate-400 mt-0.5 shrink-0" />
                                                <div>
                                                    <span class="font-medium text-slate-700 block text-xs" x-text="selectedProgram.program_date_label"></span>
                                                    <span class="text-slate-800 font-semibold" x-text="selectedProgram.program_date"></span>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="selectedProgram.registration_date">
                                            <div class="flex items-start gap-2">
                                                <x-site-icon name="file-text" class="size-4 text-slate-400 mt-0.5 shrink-0" />
                                                <div>
                                                    <span class="font-medium text-slate-700 block text-xs">Registration Period</span>
                                                    <span class="text-slate-800 font-semibold" x-text="selectedProgram.registration_date"></span>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="selectedProgram.location">
                                            <div class="flex items-center gap-2">
                                                <x-site-icon name="map-pin" class="size-4 text-slate-400 shrink-0" />
                                                <div>
                                                    <span class="font-medium text-slate-700 block text-xs">{{ $page['location'] }}</span>
                                                    <span class="text-slate-800 font-semibold" x-text="selectedProgram.location"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Registration Button inside Modal --}}
                                    <div class="mt-5">
                                        <template x-if="selectedProgram.can_register">
                                            <a
                                                :href="selectedProgram.registration_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#0d6b46] hover:bg-[#094c31] text-white font-semibold text-sm shadow-sm transition-colors"
                                            >
                                                <span>{{ $page['registration_now'] }}</span>
                                                <x-site-icon name="arrow-up-right" class="size-4" />
                                            </a>
                                        </template>
                                        <template x-if="selectedProgram.is_done">
                                            <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-400 text-sm font-medium border border-slate-200 select-none">
                                                <x-site-icon name="check" class="size-4 text-slate-400" />
                                                <span>{{ $page['program_completed'] }}</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-slate-100 my-6"></div>

                            {{-- Section: About the Program --}}
                            <template x-if="selectedProgram.description">
                                <div class="space-y-2">
                                    <h5 class="text-base font-bold text-slate-900">{{ $page['about_program'] }}</h5>
                                    <p class="text-sm leading-relaxed text-slate-600 whitespace-pre-line" x-text="selectedProgram.description"></p>
                                </div>
                            </template>

                            {{-- Section: Available Levels / Extra Information Cards --}}
                            <template x-if="selectedProgram.extra_info_items && selectedProgram.extra_info_items.length > 0">
                                <div class="mt-6 space-y-3">
                                    <h5 class="text-base font-bold text-slate-900" x-text="selectedProgram.extra_info_title || '{{ $page['available_levels'] }}'"></h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        <template x-for="(item, i) in selectedProgram.extra_info_items" :key="i">
                                            <div
                                                class="rounded-xl p-3 border text-center transition-all"
                                                :class="{
                                                    'bg-rose-50/70 border-rose-200 text-rose-700': item.color === 'rose',
                                                    'bg-amber-50/70 border-amber-200 text-amber-700': item.color === 'amber',
                                                    'bg-blue-50/70 border-blue-200 text-blue-700': item.color === 'blue',
                                                    'bg-emerald-50/70 border-emerald-200 text-emerald-700': item.color === 'emerald',
                                                    'bg-slate-50/70 border-slate-200 text-slate-700': !item.color || item.color === 'slate',
                                                }"
                                            >
                                                <div class="font-bold text-xs sm:text-sm" x-text="item.label"></div>
                                                <div class="text-xs mt-0.5 opacity-90 font-medium" x-text="item.value"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            {{-- Section: Contact Information --}}
                            <template x-if="selectedProgram.contact_info">
                                <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">{{ $page['contact_info'] }}</h5>
                                    <p class="text-sm font-medium text-slate-800" x-text="selectedProgram.contact_info"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layouts.site>
