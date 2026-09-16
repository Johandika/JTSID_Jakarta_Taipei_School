<x-layouts.site :transparent-nav="true" :show-level-filter="false">
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
    <x-school-level-filter/>
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
    <section class="shell py-14 sm:py-18">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-5">
            <div><p class="eyebrow">{{ __('site.news') }}</p><h2 class="section-title">{{ __('site.latest') }}</h2></div>
            <a class="text-link" href="{{ $site::path('/news') }}">{{ __('site.all_news') }}<x-site-icon name="arrow-right" class="size-4"/></a>
        </div>
        <div class="grid gap-9 md:grid-cols-3">@forelse($posts as $post)<x-news-item :post="$post"/>@empty<p class="text-muted">{{ __('site.no_results') }}</p>@endforelse</div>
    </section>
    <section class="border-y border-line bg-[#f4f7f5] py-14 sm:py-18">
        <div class="shell grid items-start gap-10 lg:grid-cols-[1fr_1.6fr]">
            <div><p class="eyebrow">{{ __('site.resources') }}</p><h2 class="section-title">{{ __('site.learning') }}</h2><p class="mb-6 mt-5 max-w-sm text-sm leading-7 text-muted">{{ __('site.learning_copy') }}</p><a class="btn btn-primary" href="{{ $site::path('/resources') }}">{{ __('site.all_resources') }}<x-site-icon name="arrow-right" class="size-4"/></a></div>
            <div>@foreach($resources as $resource)<a class="flex items-center gap-4 border-b border-line py-5 first:pt-0" href="{{ $site::path('/download/resources/'.$resource->id) }}"><span class="flex size-11 shrink-0 items-center justify-center rounded bg-white text-jts"><x-site-icon name="file-text"/></span><div class="min-w-0 flex-1"><span class="mb-1 block text-xs text-muted">{{ __('site.'.$resource->type->code) }} · {{ $resource->school_level_label }}</span><h3 class="text-sm font-semibold">{{ $resource->title }}</h3></div><x-site-icon name="download" class="size-4 text-jts"/></a>@endforeach</div>
        </div>
    </section>
    <section class="shell grid items-center gap-10 py-14 sm:py-18 lg:grid-cols-[1.5fr_1fr]">
        <div><p class="eyebrow">{{ __('site.about') }}</p><h2 class="section-title max-w-lg">{{ __('site.intro') }}</h2><p class="my-6 max-w-lg text-base leading-7 text-muted">{{ __('site.intro_copy') }}</p><a class="text-link" href="{{ $site::path('/academics') }}">{{ __('site.read_more') }}<x-site-icon name="arrow-right" class="size-4"/></a></div>
        @if($slides->isNotEmpty())
        <div x-data="slideshow({{ $slides->count() }})" role="region" aria-label="{{ __('site.highlights') }}" class="min-w-0">
            <div class="relative aspect-[4/3] overflow-hidden rounded bg-gray-100">
                @foreach($slides as $index => $slide)
                <div x-show="current === {{ $index }}" @if($index > 0) x-cloak @endif class="absolute inset-0">
                    @if($slide->link_url)<a href="{{ $site::path($slide->link_url) }}">@endif
                    <img src="{{ $site::image($slide->image) }}" alt="{{ $slide->topic }}" class="size-full object-contain" loading="lazy">
                    @if($slide->link_url)</a>@endif
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-between gap-3"><h3 class="text-sm font-semibold">{{ __('site.highlights') }}</h3><div class="flex gap-2">
                <button class="icon-btn" @click="previous(); paused = true" title="{{ __('site.previous') }}" aria-label="{{ __('site.previous') }}"><x-site-icon name="chevron-left"/></button>
                <button class="icon-btn" @click="paused = !paused" :aria-label="paused ? @js(__('site.play')) : @js(__('site.pause'))"><span x-show="!paused"><x-site-icon name="pause"/></span><span x-show="paused" x-cloak><x-site-icon name="play"/></span></button>
                <button class="icon-btn" @click="next(); paused = true" title="{{ __('site.next') }}" aria-label="{{ __('site.next') }}"><x-site-icon name="chevron-right"/></button>
            </div></div>
        </div>
        @endif
    </section>
</x-layouts.site>
