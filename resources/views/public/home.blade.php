<x-layouts.site :transparent-nav="true">
    @php
        $site = \App\Support\Site::class;
        $heroSlides = [
            [
                'image' => asset('images/campus.jpg'),
                'tagline' => __('site.tagline'),
                'title' => __('site.hero_slide_1_title'),
                'description' => __('site.hero_slide_1_desc'),
                'cta_text' => __('site.apply'),
                'cta_url' => $site::path('/admissions'),
            ],
            [
                'image' => asset('images/seed-classroom.jpg'),
                'tagline' => __('site.school'),
                'title' => __('site.hero_slide_2_title'),
                'description' => __('site.hero_slide_2_desc'),
                'cta_text' => __('site.explore'),
                'cta_url' => $site::path('/about'),
            ],
            [
                'image' => asset('images/seed-library.jpg'),
                'tagline' => __('site.academics'),
                'title' => __('site.hero_slide_3_title'),
                'description' => __('site.hero_slide_3_desc'),
                'cta_text' => __('site.read_more'),
                'cta_url' => $site::path('/academics'),
            ],
        ];

        $curriculumPrograms = [
            [
                'col' => 'lg:col-span-5',
                'image' => asset('images/programs/kindergarten.jpg'),
                'title' => __('site.prog_kindergarten_title'),
                'label' => __('site.prog_kindergarten_label'),
                'description' => __('site.prog_kindergarten_desc'),
                'url' => $site::path('/academics'),
            ],
            [
                'col' => 'lg:col-span-7',
                'image' => asset('images/programs/primary.jpg'),
                'title' => __('site.prog_primary_title'),
                'label' => __('site.prog_primary_label'),
                'description' => __('site.prog_primary_desc'),
                'url' => $site::path('/academics'),
            ],
            [
                'col' => 'lg:col-span-7',
                'image' => asset('images/programs/secondary.jpg'),
                'title' => __('site.prog_secondary_title'),
                'label' => __('site.prog_secondary_label'),
                'description' => __('site.prog_secondary_desc'),
                'url' => $site::path('/academics'),
            ],
            [
                'col' => 'lg:col-span-5',
                'image' => asset('images/programs/english.jpg'),
                'title' => __('site.prog_english_title'),
                'label' => __('site.prog_english_label'),
                'description' => __('site.prog_english_desc'),
                'url' => $site::path('/academics'),
            ],
        ];
    @endphp

    {{-- Hero Section: 70svh on Mobile (with next section peeking), Full Screen on Desktop --}}
    <section class="relative h-[70svh] min-h-[500px] md:h-screen md:min-h-[640px] w-full overflow-hidden bg-black"
             x-data="{
                 current: 0,
                 total: {{ count($heroSlides) }},
                 paused: false,
                 timer: null,
                 slides: @js($heroSlides),
                 init() {
                     this.startTimer();
                 },
                 startTimer() {
                     this.timer = setInterval(() => {
                         if (!this.paused) {
                             this.next();
                         }
                     }, 6500);
                 },
                 next() {
                     this.current = (this.current + 1) % this.total;
                 },
                 prev() {
                     this.current = (this.current - 1 + this.total) % this.total;
                 },
                 goTo(i) {
                     this.current = i;
                 }
             }"
             @mouseenter="paused = true"
             @mouseleave="paused = false">

        {{-- Background Image Slides --}}
        @foreach($heroSlides as $idx => $slide)
            <div x-show="current === {{ $idx }}"
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-100"
                 x-transition:enter-end="opacity-100 scale-105"
                 x-transition:leave="transition ease-in duration-800"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 size-full"
                 @if($idx > 0) x-cloak @endif>
                <img src="{{ $slide['image'] }}" alt="Jakarta Taipei School"
                     class="size-full object-cover object-center transform transition-transform duration-10000 ease-out"
                     @if($idx === 0) fetchpriority="high" @else loading="lazy" @endif
                     width="1920" height="1080">
            </div>
        @endforeach

        {{-- Multi-layered Dark Overlay for Text Legibility & Contrast --}}
        <div class="absolute inset-0 bg-black/25 pointer-events-none"></div>
        <div class="absolute top-0 inset-x-0 h-36 sm:h-44 bg-gradient-to-b from-black/85 via-black/35 to-transparent pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent pointer-events-none"></div>

        {{-- Left-Bottom Diagonal Info Panel --}}
        <div class="absolute bottom-0 left-0 z-20 w-[95%] sm:w-[88%] md:w-[78%] lg:w-[62%] xl:w-[52%]">
            <div class="hero-diagonal-panel bg-jts/95 backdrop-blur-md text-white shadow-2xl pt-4 pb-4 pl-4 pr-12 sm:pt-7 sm:pb-7 sm:pl-7 sm:pr-18 lg:pt-9 lg:pb-9 lg:pl-10 lg:pr-24 transition-all duration-300">
                {{-- Tagline / Eyebrow --}}
                <div class="mb-1.5 sm:mb-2.5 flex items-center gap-2 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-accent">
                    <span class="inline-block h-0.5 w-5 sm:w-6 bg-accent"></span>
                    <span x-text="slides[current].tagline"></span>
                </div>

                {{-- Headline --}}
                <h1 class="text-xl sm:text-2xl md:text-4xl lg:text-5xl font-bold leading-tight tracking-tight text-white min-h-[2.2em] flex items-center"
                    x-text="slides[current].title">
                </h1>

                {{-- Description --}}
                <p class="mb-3.5 sm:mb-5 mt-1 sm:mt-2 max-w-xl text-xs sm:text-sm md:text-base leading-relaxed text-white/90 font-normal line-clamp-2 sm:line-clamp-3 md:line-clamp-none min-h-[2.5em]"
                   x-text="slides[current].description">
                </p>

                {{-- White CTA Button --}}
                <div class="flex flex-wrap items-center gap-4">
                    <a :href="slides[current].cta_url"
                       class="inline-flex items-center gap-2.5 rounded bg-white px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-bold text-jts uppercase tracking-wider shadow-lg hover:bg-gray-100 hover:shadow-xl hover:translate-x-0.5 transition-all duration-200 group">
                        <span x-text="slides[current].cta_text"></span>
                        <x-site-icon name="chevron-right" class="size-3.5 sm:size-4 text-jts transition-transform group-hover:translate-x-1"/>
                    </a>
                </div>

                {{-- Mobile Navigation Controls (Inside Green Panel, matching reference) --}}
                <div class="mt-3.5 flex items-center gap-4 md:hidden">
                    <button @click="prev(); paused = true"
                            aria-label="{{ __('site.previous') }}"
                            class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                        <x-site-icon name="arrow-left" class="size-4.5"/>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="goTo(index); paused = true" :aria-label="'Slide ' + (index + 1)"
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="current === index ? 'w-4 bg-accent' : 'w-2 bg-white/50'">
                            </button>
                        </template>
                    </div>

                    <button @click="next(); paused = true"
                            aria-label="{{ __('site.next') }}"
                            class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                        <x-site-icon name="arrow-right" class="size-4.5"/>
                    </button>
                </div>
            </div>
        </div>

        {{-- Desktop Bottom-Right Carousel Controls (Pill) --}}
        <div class="absolute bottom-6 right-5 sm:bottom-8 sm:right-8 lg:right-12 z-30 hidden md:flex items-center gap-4 sm:gap-6 bg-black/40 backdrop-blur-md px-4 py-2.5 rounded-full border border-white/20 shadow-xl">
            {{-- Slide Dots --}}
            <div class="flex items-center gap-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goTo(index); paused = true" :aria-label="'Slide ' + (index + 1)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="current === index ? 'w-7 bg-accent' : 'w-2 bg-white/50 hover:bg-white/80'">
                    </button>
                </template>
            </div>

            <div class="h-4 w-px bg-white/30"></div>

            {{-- Arrows --}}
            <div class="flex items-center gap-1.5">
                <button @click="prev(); paused = true"
                        aria-label="{{ __('site.previous') }}"
                        title="{{ __('site.previous') }}"
                        class="flex size-9 items-center justify-center rounded-full text-white hover:bg-white/20 active:scale-95 transition-all">
                    <x-site-icon name="chevron-left" class="size-5"/>
                </button>
                <button @click="next(); paused = true"
                        aria-label="{{ __('site.next') }}"
                        title="{{ __('site.next') }}"
                        class="flex size-9 items-center justify-center rounded-full text-white hover:bg-white/20 active:scale-95 transition-all">
                    <x-site-icon name="chevron-right" class="size-5"/>
                </button>
            </div>
        </div>
    </section>

    {{-- School Level Filter Bar (Below Hero on Homepage) --}}
    <div class="border-b border-line">
        <div class="shell grid grid-cols-2 sm:grid-cols-4">
            @foreach(['academics' => ['book-open','/academics'], 'admissions' => ['graduation-cap','/admissions'], 'calendar' => ['calendar-days','/resources/calendar'], 'resources' => ['file-text','/resources']] as $key => [$icon,$path])
                <a href="{{ $site::path($path) }}" class="flex min-h-22 items-center justify-between gap-3 border-line py-5 pr-5 text-sm font-semibold hover:text-jts sm:border-r sm:px-6 first:sm:pl-0 last:sm:border-0">
                    <span class="flex items-center gap-3"><x-site-icon :name="$icon" class="size-5 text-jts"/>{{ __('site.'.$key) }}</span><x-site-icon name="arrow-up-right" class="hidden size-4 sm:block"/>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Educational Programs Bento Grid Section --}}
    <section class="shell py-16 sm:py-24 bg-white">
        {{-- Section Heading --}}
        <div class="mx-auto mb-10 sm:mb-14 max-w-3xl text-center">
            <p class="eyebrow justify-center">{{ __('site.academics') }}</p>
            <h2 class="section-title mx-auto">{{ __('site.curriculum_headline') }}</h2>
            <p class="mt-4 text-base leading-7 text-muted mx-auto max-w-2xl">
                {{ __('site.curriculum_desc') }}
            </p>
        </div>

        {{-- Asymmetrical Bento Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            @foreach($curriculumPrograms as $program)
                <div class="{{ $program['col'] }} group relative h-[360px] sm:h-[400px] lg:h-[430px] rounded-lg sm:rounded-xl overflow-hidden cursor-pointer shadow-sm hover:shadow-xl transition-shadow duration-300 select-none bg-gray-100"
                     x-data="{ active: false }"
                     @click="active = !active"
                     @keydown.escape.window="active = false">

                    {{-- Student Activity Background Photo --}}
                    <img src="{{ $program['image'] }}"
                         alt="{{ $program['title'] }}"
                         width="800" height="600"
                         class="size-full object-cover object-center transform transition-transform duration-400 ease-out group-hover:scale-105"
                         loading="lazy">

                    {{-- Solid JTS Green Overlay Panel --}}
                    <div class="absolute bottom-0 left-0 bg-jts text-white p-5 sm:p-7 shadow-2xl transition-all duration-300 ease-out z-10"
                         :class="active ? 'w-full max-w-full rounded-tr-none' : 'w-[92%] sm:w-auto sm:max-w-[80%] rounded-tr-lg sm:rounded-tr-xl group-hover:w-full group-hover:max-w-full group-hover:rounded-tr-none'">

                        {{-- Label / Grade Level --}}
                        <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-accent mb-1">
                            {{ $program['label'] }}
                        </p>

                        {{-- Program Title --}}
                        <h3 class="text-xl sm:text-2xl font-bold text-white leading-tight">
                            {{ $program['title'] }}
                        </h3>

                        {{-- Description & CTA (Reveals on Hover or Tap) --}}
                        <div class="overflow-hidden transition-all duration-300 ease-out"
                             :class="active ? 'max-h-60 opacity-100 mt-3 sm:mt-4' : 'max-h-0 opacity-0 group-hover:max-h-60 group-hover:opacity-100 group-hover:mt-3 group-hover:sm:mt-4'">
                            <p class="text-xs sm:text-sm md:text-base leading-relaxed text-white/95 font-normal">
                                {{ $program['description'] }}
                            </p>

                            <div class="mt-4 pt-1">
                                <a href="{{ $program['url'] }}"
                                   @click.stop
                                   class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-white hover:text-accent transition-colors group/cta">
                                    <span>{{ __('site.curriculum_learn_more') }}</span>
                                    <x-site-icon name="arrow-right" class="size-4 text-white group-hover/cta:text-accent transition-transform group-hover/cta:translate-x-1" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    {{-- Section: Materi Pelajaran (Digital Learning Resources Hub) --}}
    <section class="border-y border-line bg-[#f4f7f5] py-16 sm:py-20">
        <div class="shell grid items-start gap-10 lg:grid-cols-[1fr_1.4fr] lg:gap-14">
            <div>
                <p class="eyebrow">{{ __('site.resources') }}</p>
                <h2 class="section-title mt-1">{{ __('site.learning') }}</h2>
                <p class="mt-4 mb-8 text-sm sm:text-base leading-relaxed text-muted">{{ __('site.learning_copy') }}</p>

                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3 text-sm text-ink/90 font-medium">
                        <span class="flex size-5 shrink-0 items-center justify-center rounded-full bg-jts/15 text-jts">
                            <x-site-icon name="check" class="size-3" />
                        </span>
                        <span>{{ __('site.calendar') }} · {{ __('site.syllabus') }} · {{ __('site.textbook') }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-ink/90 font-medium">
                        <span class="flex size-5 shrink-0 items-center justify-center rounded-full bg-jts/15 text-jts">
                            <x-site-icon name="check" class="size-3" />
                        </span>
                        <span>{{ __('site.resources_copy') }}</span>
                    </div>
                </div>

                <a class="btn btn-primary shadow-xs hover:shadow-md transition-all inline-flex items-center gap-2" href="{{ $site::path('/resources') }}">
                    <span>{{ __('site.view_all_resources') }}</span>
                    <x-site-icon name="arrow-right" class="size-4"/>
                </a>
            </div>

            <div class="space-y-3.5">
                @forelse($resources as $resource)
                    <a class="group flex items-center justify-between gap-4 p-4 sm:p-5 rounded-xl bg-white border border-line/80 shadow-2xs hover:shadow-md hover:border-jts/40 transition-all duration-200" href="{{ $site::path('/download/resources/'.$resource->id) }}">
                        <div class="flex items-center gap-4 min-w-0">
                            <span class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-lg bg-jts/10 text-jts group-hover:bg-jts group-hover:text-white transition-colors duration-200">
                                <x-site-icon name="file-text" class="size-5 sm:size-6"/>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 text-xs text-muted mb-1">
                                    @if($resource->type)
                                        <span class="font-semibold text-jts">{{ __('site.'.$resource->type->code) }}</span>
                                        <span class="text-line-dark text-gray-300">·</span>
                                    @endif
                                    <span>{{ $resource->school_level_label }}</span>
                                </div>
                                <h3 class="text-sm sm:text-base font-bold text-ink group-hover:text-jts transition-colors truncate">
                                    {{ $resource->title }}
                                </h3>
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-line text-xs font-semibold text-ink group-hover:bg-jts group-hover:text-white group-hover:border-jts transition-all shadow-2xs">
                            <x-site-icon name="download" class="size-3.5"/>
                            <span class="hidden sm:inline">{{ __('site.download') }}</span>
                        </span>
                    </a>
                @empty
                    <div class="text-center py-10 bg-white rounded-xl border border-dashed border-line">
                        <x-site-icon name="file-text" class="size-8 mx-auto text-muted mb-2"/>
                        <p class="text-sm font-medium text-muted">{{ __('site.no_results') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Section: Fasilitas JTS (Campus Facilities Marquee Showcase) --}}
    <section class="py-16 sm:py-24 overflow-hidden bg-white">
        {{-- Centered Header --}}
        <div class="shell text-center mb-10 sm:mb-14">
            <div class="flex items-center justify-center gap-3 mb-3">
                <span class="h-px w-7 bg-jts"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-jts">{{ __('site.facilities_eyebrow') }}</span>
                <span class="h-px w-7 bg-jts"></span>
            </div>
            <h2 class="section-title text-center text-balance">
                {{ __('site.facilities_headline') }}
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed text-muted text-center">
                {{ __('site.facilities_desc') }}
            </p>
        </div>

        @if($slides->isNotEmpty())
            {{-- Marquee Full Width Wrapper --}}
            <div class="relative w-full overflow-hidden py-4 sm:py-6">
                {{-- Side Fade Masks --}}
                <div class="pointer-events-none absolute inset-y-0 left-0 w-16 sm:w-36 lg:w-48 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 w-16 sm:w-36 lg:w-48 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

                {{-- Infinite Marquee Track --}}
                <div class="flex animate-marquee gap-6 sm:gap-8 lg:gap-10 hover:[animation-play-state:paused]">
                    {{-- Render two identical sets so the -50% translateX loop is seamless --}}
                    @php
                        $marqueeSlides = $slides->count() < 4 ? $slides->concat($slides)->concat($slides) : $slides->concat($slides);
                    @endphp
                    @foreach($marqueeSlides as $slide)
                        <div class="group relative w-[320px] sm:w-[440px] lg:w-[520px] aspect-[16/10] shrink-0 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-line/70 bg-gray-900">
                            {{-- Facility Image --}}
                            <img src="{{ $site::image($slide->image) }}"
                                 alt="{{ $slide->topic }}"
                                 class="size-full object-cover transform transition-transform duration-500 ease-out group-hover:scale-105"
                                 loading="lazy">

                            {{-- Bottom Dark Gradient Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent pointer-events-none"></div>

                            {{-- Bottom Text / Content --}}
                            <div class="absolute inset-x-0 bottom-0 p-6 sm:p-7 lg:p-8 z-10 pointer-events-none">
                                <span class="block text-xs sm:text-sm font-bold uppercase tracking-wider text-accent mb-1.5 drop-shadow-xs">
                                    {{ $slide->tag ?: __('site.facilities_eyebrow') }}
                                </span>
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-white leading-snug drop-shadow-sm line-clamp-1">
                                    {{ $slide->topic }}
                                </h3>
                            </div>

                            @if($slide->link_url)
                                <a href="{{ $site::path($slide->link_url) }}" class="absolute inset-0 z-20" aria-label="{{ $slide->topic }}"></a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        @endif

        {{-- Centered Bottom CTA --}}
        <div class="mt-10 sm:mt-12 text-center">
            <a href="{{ $site::path('/profile') }}" class="btn btn-primary rounded-lg px-6 py-3 shadow-xs hover:shadow-md transition-all inline-flex items-center gap-2 group">
                <span>{{ __('site.explore_facilities') }}</span>
                <x-site-icon name="arrow-right" class="size-4 transition-transform duration-200 group-hover:translate-x-1"/>
            </a>
        </div>
    </section>

    {{-- Section: Berita & Media (News & Media) --}}
    <section class="shell py-16 sm:py-20">
        <div class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-5">
            <div>
                <p class="eyebrow">{{ __('site.news') }}</p>
                <h2 class="section-title mt-1">{{ __('site.latest') }}</h2>
                <p class="mt-2 text-sm sm:text-base text-muted max-w-xl">{{ __('site.news_copy') }}</p>
            </div>
            <div class="shrink-0">
                <a class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-line text-sm font-semibold text-ink hover:text-jts hover:border-jts/40 hover:bg-jts/5 transition-all shadow-2xs group" href="{{ $site::path('/news') }}">
                    <span>{{ __('site.view_all_news') }}</span>
                    <x-site-icon name="arrow-right" class="size-4 text-jts transition-transform duration-200 group-hover:translate-x-1"/>
                </a>
            </div>
        </div>
        <div class="grid gap-6 sm:gap-8 md:grid-cols-3">
            @forelse($posts as $post)
                <x-news-item :post="$post"/>
            @empty
                <div class="col-span-3 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-line">
                    <x-site-icon name="newspaper" class="size-8 mx-auto text-muted mb-2"/>
                    <p class="text-sm font-medium text-muted">{{ __('site.no_results') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.site>
