<x-layouts.site :transparent-nav="true">
    @php
        $site = \App\Support\Site::class;
        $slides = collect($heroSlides ?? [])->map(function ($s) use ($site) {
            $imageUrl = method_exists($s, 'getImageOrPosterUrl') ? $s->getImageOrPosterUrl() : null;
            if (! $imageUrl && ! empty($s->image)) {
                $image = $s->image;
                $imageUrl = str_starts_with($image, 'http')
                    ? $image
                    : (file_exists(public_path(ltrim($image, '/'))) ? asset(ltrim($image, '/')) : asset('storage/' . ltrim($image, '/')));
            }

            $ctaUrl = $s->cta_url;
            if ($ctaUrl && ! str_starts_with($ctaUrl, 'http') && ! str_starts_with($ctaUrl, '#')) {
                $ctaUrl = $site::path($ctaUrl);
            }

            return [
                'image' => $imageUrl,
                'video_embed_url' => method_exists($s, 'getYouTubeEmbedUrl') ? $s->getYouTubeEmbedUrl() : null,
                'direct_video_url' => (method_exists($s, 'isDirectVideo') && $s->isDirectVideo()) ? $s->video_url : null,
                'show_text' => (bool) ($s->show_text ?? true),
                'tagline' => method_exists($s, 'trans') ? $s->trans('tagline') : ($s->tagline ?? ''),
                'title' => method_exists($s, 'trans') ? $s->trans('title') : ($s->title ?? ''),
                'description' => method_exists($s, 'trans') ? $s->trans('description') : ($s->description ?? ''),
                'cta_text' => method_exists($s, 'trans') ? $s->trans('cta_text') : ($s->cta_text ?? ''),
                'cta_url' => $ctaUrl ?: '#',
            ];
        })->values()->all();


        $rawPrograms = collect($academicPrograms ?? []);
        if ($rawPrograms->isEmpty()) {
            $rawPrograms = collect([
                [
                    'image' => 'images/programs/kindergarten.webp',
                    'title' => __('site.prog_kindergarten_title'),
                    'label' => __('site.prog_kindergarten_label'),
                    'description' => __('site.prog_kindergarten_desc'),
                    'url' => '/academics',
                ],
                [
                    'image' => 'images/programs/primary.webp',
                    'title' => __('site.prog_primary_title'),
                    'label' => __('site.prog_primary_label'),
                    'description' => __('site.prog_primary_desc'),
                    'url' => '/academics',
                ],
                [
                    'image' => 'images/programs/secondary.webp',
                    'title' => __('site.prog_secondary_title'),
                    'label' => __('site.prog_secondary_label'),
                    'description' => __('site.prog_secondary_desc'),
                    'url' => '/academics',
                ],
                [
                    'image' => 'images/programs/english.webp',
                    'title' => __('site.prog_english_title'),
                    'label' => __('site.prog_english_label'),
                    'description' => __('site.prog_english_desc'),
                    'url' => '/academics',
                ],
            ]);
        }

        $programCount = $rawPrograms->count();
        $curriculumPrograms = $rawPrograms->values()->map(function ($program, $index) use ($site, $programCount) {
            $isModel = $program instanceof \App\Models\AcademicProgram;
            $image = $isModel ? $program->image_url : (is_array($program) ? ($program['image'] ?? null) : null);
            if (! $image) {
                $image = asset('images/logo.webp');
            } elseif (! str_starts_with($image, 'http')) {
                $clean = ltrim($image, '/');
                $image = file_exists(public_path($clean)) ? asset($clean) : asset('storage/' . $clean);
            }

            $rawUrl = $isModel ? $program->url : (is_array($program) ? ($program['url'] ?? '/academics') : '/academics');
            $url = $rawUrl;
            if ($url && ! str_starts_with($url, 'http') && ! str_starts_with($url, '#')) {
                $url = $site::path($url);
            }

            if ($programCount % 2 !== 0 && $index === $programCount - 1) {
                $col = 'lg:col-span-12';
            } elseif ($index % 4 === 0 || $index % 4 === 3) {
                $col = 'lg:col-span-5';
            } else {
                $col = 'lg:col-span-7';
            }

            return [
                'col' => $col,
                'image' => $image,
                'title' => $isModel ? $program->trans('title') : ($program['title'] ?? ''),
                'label' => $isModel ? $program->trans('label') : ($program['label'] ?? ''),
                'description' => $isModel ? $program->trans('description') : ($program['description'] ?? ''),
                'url' => $url ?: $site::path('/academics'),
            ];
        })->all();

        $curriculumLocale = app()->getLocale();
        $curriculumEyebrow = $academicsHeader['eyebrow'][$curriculumLocale] ?? $academicsHeader['eyebrow']['en'] ?? __('site.academics');
        $curriculumHeadline = $academicsHeader['heading'][$curriculumLocale] ?? $academicsHeader['heading']['en'] ?? __('site.curriculum_headline');
        $curriculumDesc = $academicsHeader['subheading'][$curriculumLocale] ?? $academicsHeader['subheading']['en'] ?? __('site.curriculum_desc');
        $curriculumCtaLabel = $academicsHeader['cta_label'][$curriculumLocale] ?? $academicsHeader['cta_label']['en'] ?? __('site.curriculum_learn_more');
    @endphp

    {{-- Hero Section: 70svh on Mobile (with next section peeking), Full Screen on Desktop --}}
    @if(count($slides) > 0)
    <section class="relative h-[70svh] min-h-[500px] md:h-screen md:min-h-[640px] w-full overflow-hidden bg-black"
             x-data="{
                 current: 0,
                 total: {{ count($slides) }},
                 isHovered: false,
                 timer: null,
                 videoReady: {},
                 slides: @js($slides),
                 markVideoReady(idx) {
                     setTimeout(() => {
                         this.videoReady[idx] = true;
                     }, 1800);
                 },
                 init() {
                     this.syncPlayback();
                     window.addEventListener('message', (event) => {
                         try {
                             const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
                             if (!data || data.event !== 'onStateChange') return;

                             // State 1 = Playing: wait 1000ms until YouTube pause flash finishes fading, then reveal video
                             if (data.info === 1) {
                                 setTimeout(() => {
                                     this.videoReady[this.current] = true;
                                 }, 1000);
                             }

                             // State 0 = Ended: replay immediately
                             if (data.info === 0) {
                                 document.querySelectorAll('.hero-yt-iframe').forEach((iframe) => {
                                     iframe.contentWindow?.postMessage(JSON.stringify({event: 'command', func: 'seekTo', args: [0, true]}), '*');
                                     iframe.contentWindow?.postMessage(JSON.stringify({event: 'command', func: 'playVideo', args: ''}), '*');
                                 });
                             }
                         } catch (e) {}
                     });
                 },
                 startTimer() {
                     this.stopTimer();
                     this.timer = setInterval(() => {
                         if (!this.isHovered && !this.isVideoSlide()) {
                             this.next();
                         }
                     }, 6500);
                 },
                 stopTimer() {
                     if (this.timer) {
                         clearInterval(this.timer);
                         this.timer = null;
                     }
                 },
                 isVideoSlide() {
                     return Boolean(this.slides[this.current]?.video_embed_url || this.slides[this.current]?.direct_video_url);
                 },
                 syncPlayback() {
                     if (this.isVideoSlide()) {
                         this.stopTimer();
                     } else {
                         this.startTimer();
                     }
                 },
                 next() {
                     this.current = (this.current + 1) % this.total;
                     this.syncPlayback();
                 },
                 prev() {
                     this.current = (this.current - 1 + this.total) % this.total;
                     this.syncPlayback();
                 },
                 goTo(i) {
                     this.current = i;
                     this.syncPlayback();
                 }
             }"
             @mouseenter="isHovered = true"
             @mouseleave="isHovered = false">

        {{-- Background Image & Video Slides --}}
        @foreach($slides as $idx => $slide)
            <div x-show="current === {{ $idx }}"
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-100"
                 x-transition:enter-end="opacity-100 scale-105"
                 x-transition:leave="transition ease-in duration-800"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 size-full overflow-hidden"
                 @if($idx > 0) x-cloak @endif>
                {{-- Fallback / Poster Image --}}
                @if(!empty($slide['image']))
                <img src="{{ $slide['image'] }}" alt="Jakarta Taipei School"
                     class="size-full object-cover object-center transform transition-transform duration-10000 ease-out"
                     @if($idx === 0) fetchpriority="high" @else loading="lazy" @endif
                     width="1920" height="1080">
                @endif

                @if(!empty($slide['direct_video_url']))
                    {{-- Native HTML5 Direct Video (.mp4 / .webm) - Zero Controls, Zero Overlays --}}
                    <div class="absolute inset-0 size-full overflow-hidden pointer-events-none select-none">
                        <video autoplay muted loop playsinline
                               class="size-full object-cover object-center pointer-events-none">
                            <source src="{{ $slide['direct_video_url'] }}" type="video/mp4">
                        </video>
                    </div>
                @elseif(!empty($slide['video_embed_url']))
                    {{-- Responsive Autoplay YouTube Background Embed (Smooth Cross-Fade after pause indicator disappears) --}}
                    <div class="absolute inset-0 size-full overflow-hidden pointer-events-none select-none">
                        <iframe
                            src="{{ $slide['video_embed_url'] }}"
                            title="Jakarta Taipei School Video"
                            @load="markVideoReady({{ $idx }})"
                            :class="videoReady[{{ $idx }}] ? 'opacity-100' : 'opacity-0'"
                            class="hero-yt-iframe absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[177.77777778vh] min-w-full h-[56.25vw] min-h-full object-cover border-0 pointer-events-none select-none transition-opacity duration-1000 ease-in-out"
                            style="pointer-events: none !important;"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            tabindex="-1"
                            aria-hidden="true">
                        </iframe>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Click Shield Overlay & Multi-layered Dark Gradients --}}
        {{-- Shield Layer: Captures mouse/touch clicks on background so they NEVER penetrate to the YouTube player --}}
        <div class="absolute inset-0 z-10 bg-black/25 select-none"></div>
        <div class="absolute top-0 inset-x-0 z-10 h-36 sm:h-44 bg-gradient-to-b from-black/85 via-black/35 to-transparent pointer-events-none"></div>
        <div class="absolute inset-0 z-10 bg-gradient-to-t from-black/90 via-black/40 to-transparent pointer-events-none"></div>

        {{-- Left-Bottom Diagonal Info Panel (Rendered only when show_text is active) --}}
        <div x-show="slides[current].show_text"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-6"
             class="absolute bottom-0 left-0 z-20 w-[95%] sm:w-[88%] md:w-[78%] lg:w-[62%] xl:w-[52%]">
            <div class="hero-diagonal-panel bg-jts/95 backdrop-blur-md text-white shadow-2xl pt-4 pb-4 pl-4 pr-12 sm:pt-7 sm:pb-7 sm:pl-7 sm:pr-18 lg:pt-9 lg:pb-9 lg:pl-10 lg:pr-24 transition-all duration-300">
                {{-- Tagline / Eyebrow --}}
                <div x-show="slides[current].tagline" class="mb-1.5 sm:mb-2.5 flex items-center gap-2 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-accent">
                    <span class="inline-block h-0.5 w-5 sm:w-6 bg-accent"></span>
                    <span x-text="slides[current].tagline"></span>
                </div>

                {{-- Headline --}}
                <h1 x-show="slides[current].title" class="text-xl sm:text-2xl md:text-4xl lg:text-5xl font-bold leading-tight tracking-tight text-white min-h-[2.2em] flex items-center"
                    x-text="slides[current].title">
                </h1>

                {{-- Description --}}
                <p x-show="slides[current].description" class="mb-3.5 sm:mb-5 mt-1 sm:mt-2 max-w-xl text-xs sm:text-sm md:text-base leading-relaxed text-white/90 font-normal line-clamp-2 sm:line-clamp-3 md:line-clamp-none min-h-[2.5em]"
                   x-text="slides[current].description">
                </p>

                {{-- White CTA Button --}}
                <div x-show="slides[current].cta_text" class="flex flex-wrap items-center gap-4">
                    <a :href="slides[current].cta_url"
                       class="inline-flex items-center gap-2.5 rounded bg-white px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-bold text-jts uppercase tracking-wider shadow-lg hover:bg-gray-100 hover:shadow-xl hover:translate-x-0.5 transition-all duration-200 group">
                        <span x-text="slides[current].cta_text"></span>
                        <x-site-icon name="chevron-right" class="size-3.5 sm:size-4 text-jts transition-transform group-hover:translate-x-1"/>
                    </a>
                </div>

                {{-- Mobile Navigation Controls (Inside Green Panel, when panel is visible) --}}
                <div class="mt-3.5 flex items-center gap-4 md:hidden">
                    <button @click="prev()"
                            aria-label="{{ __('site.previous') }}"
                            class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                        <x-site-icon name="arrow-left" class="size-4.5"/>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="goTo(index)" :aria-label="'Slide ' + (index + 1)"
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="current === index ? 'w-4 bg-accent' : 'w-2 bg-white/50'">
                            </button>
                        </template>
                    </div>

                    <button @click="next()"
                            aria-label="{{ __('site.next') }}"
                            class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                        <x-site-icon name="arrow-right" class="size-4.5"/>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Floating Navigation Controls (When Green Panel is Hidden) --}}
        <div x-show="!slides[current].show_text"
             class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex md:hidden items-center gap-3 bg-black/60 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 shadow-xl"
             x-cloak>
            <button @click="prev()"
                    aria-label="{{ __('site.previous') }}"
                    class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                <x-site-icon name="arrow-left" class="size-4.5"/>
            </button>

            <div class="flex items-center gap-1.5">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goTo(index)" :aria-label="'Slide ' + (index + 1)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="current === index ? 'w-4 bg-accent' : 'w-2 bg-white/50'">
                    </button>
                </template>
            </div>

            <button @click="next()"
                    aria-label="{{ __('site.next') }}"
                    class="text-white hover:text-accent active:scale-90 transition-transform p-1">
                <x-site-icon name="arrow-right" class="size-4.5"/>
            </button>
        </div>

        {{-- Desktop Bottom-Right Carousel Controls (Pill) --}}
        <div class="absolute bottom-6 right-5 sm:bottom-8 sm:right-8 lg:right-12 z-30 hidden md:flex items-center gap-4 sm:gap-6 bg-black/40 backdrop-blur-md px-4 py-2.5 rounded-full border border-white/20 shadow-xl">
            {{-- Slide Dots --}}
            <div class="flex items-center gap-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goTo(index)" :aria-label="'Slide ' + (index + 1)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="current === index ? 'w-7 bg-accent' : 'w-2 bg-white/50 hover:bg-white/80'">
                    </button>
                </template>
            </div>

            <div class="h-4 w-px bg-white/30"></div>

            {{-- Arrows --}}
            <div class="flex items-center gap-1.5">
                <button @click="prev()"
                        aria-label="{{ __('site.previous') }}"
                        title="{{ __('site.previous') }}"
                        class="flex size-9 items-center justify-center rounded-full text-white hover:bg-white/20 active:scale-95 transition-all">
                    <x-site-icon name="chevron-left" class="size-5"/>
                </button>
                <button @click="next()"
                        aria-label="{{ __('site.next') }}"
                        title="{{ __('site.next') }}"
                        class="flex size-9 items-center justify-center rounded-full text-white hover:bg-white/20 active:scale-95 transition-all">
                    <x-site-icon name="chevron-right" class="size-5"/>
                </button>
            </div>
        </div>
    </section>
    @endif

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
            <p class="eyebrow justify-center">{{ $curriculumEyebrow }}</p>
            <h2 class="section-title mx-auto">{{ $curriculumHeadline }}</h2>
            <p class="mt-4 text-base leading-7 text-muted mx-auto max-w-2xl">
                {{ $curriculumDesc }}
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
                                    <span>{{ $curriculumCtaLabel }}</span>
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

    {{-- Section: Fasilitas JTS (Campus Facilities Bento Grid & Pillars) --}}
    <section class="py-16 sm:py-24 bg-white">
        <div class="shell">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 xl:gap-14 items-center">
                {{-- Left Column (Desktop): Bento Grid of 4 Cards --}}
                <div class="lg:col-span-7 order-2 lg:order-1">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 sm:gap-4 lg:gap-5">
                        {{-- Left Half of Bento: 1 Tall Card (Ruang Kelas Modern) --}}
                        <div class="sm:col-span-6 flex">
                            <div class="group relative w-full h-[320px] sm:h-[460px] lg:h-[490px] rounded-2xl overflow-hidden border border-line/70 bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300">
                                <img src="{{ asset('images/programs/primary.webp') }}"
                                     alt="{{ __('site.facility_classroom') }}"
                                     class="size-full object-cover transform transition-transform duration-700 ease-out group-hover:scale-105"
                                     loading="lazy">

                                {{-- Dark gradient overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent transition-opacity duration-300 pointer-events-none group-hover:from-black/90"></div>

                                {{-- Action badge --}}
                                <div class="absolute top-4 right-4 size-8.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/90 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 shadow-sm z-10 pointer-events-none">
                                    <x-site-icon name="arrow-up-right" class="size-4" />
                                </div>

                                {{-- Bottom text --}}
                                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 z-10 pointer-events-none">
                                    <span class="inline-block text-[11px] sm:text-xs font-bold uppercase tracking-wider text-accent mb-1 drop-shadow-xs">
                                        {{ __('site.facility_tag_learning') }}
                                    </span>
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white leading-snug drop-shadow-sm group-hover:text-amber-200 transition-colors">
                                        {{ __('site.facility_classroom') }}
                                    </h3>
                                </div>

                                <a href="{{ $site::path('/facilities') }}" class="absolute inset-0 z-20" aria-label="{{ __('site.facility_classroom') }}"></a>
                            </div>
                        </div>

                        {{-- Right Half of Bento: 1 Top Horizontal Card + 2 Bottom Cards --}}
                        <div class="sm:col-span-6 flex flex-col gap-3.5 sm:gap-4 lg:gap-5 h-full">
                            {{-- Top Horizontal Card (Laboratorium Sains) --}}
                            <div class="group relative w-full h-[180px] sm:h-[220px] lg:h-[235px] rounded-2xl overflow-hidden border border-line/70 bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300">
                                <img src="{{ asset('images/programs/secondary.webp') }}"
                                     alt="{{ __('site.facility_lab') }}"
                                     class="size-full object-cover transform transition-transform duration-700 ease-out group-hover:scale-105"
                                     loading="lazy">

                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent transition-opacity duration-300 pointer-events-none group-hover:from-black/90"></div>

                                <div class="absolute top-4 right-4 size-8.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/90 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 shadow-sm z-10 pointer-events-none">
                                    <x-site-icon name="arrow-up-right" class="size-4" />
                                </div>

                                <div class="absolute inset-x-0 bottom-0 p-5 z-10 pointer-events-none">
                                    <span class="inline-block text-[11px] sm:text-xs font-bold uppercase tracking-wider text-accent mb-1 drop-shadow-xs">
                                        {{ __('site.facility_tag_exploration') }}
                                    </span>
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white leading-snug drop-shadow-sm group-hover:text-amber-200 transition-colors">
                                        {{ __('site.facility_lab') }}
                                    </h3>
                                </div>

                                <a href="{{ $site::path('/facilities') }}" class="absolute inset-0 z-20" aria-label="{{ __('site.facility_lab') }}"></a>
                            </div>

                            {{-- Bottom Row: 2 Cards Side-by-Side --}}
                            <div class="grid grid-cols-2 gap-3.5 sm:gap-4 lg:gap-5 flex-1">
                                {{-- Card 3: Perpustakaan --}}
                                <div class="group relative w-full h-[160px] sm:h-[220px] lg:h-[235px] rounded-2xl overflow-hidden border border-line/70 bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300">
                                    <img src="{{ asset('images/library.webp') }}"
                                         alt="{{ __('site.facility_library') }}"
                                         class="size-full object-cover transform transition-transform duration-700 ease-out group-hover:scale-105"
                                         loading="lazy">

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent transition-opacity duration-300 pointer-events-none group-hover:from-black/90"></div>

                                    <div class="absolute top-3.5 right-3.5 size-7.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/90 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 shadow-sm z-10 pointer-events-none">
                                        <x-site-icon name="arrow-up-right" class="size-3.5" />
                                    </div>

                                    <div class="absolute inset-x-0 bottom-0 p-4 sm:p-4.5 z-10 pointer-events-none">
                                        <span class="inline-block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-accent mb-0.5 drop-shadow-xs">
                                            {{ __('site.facility_tag_literacy') }}
                                        </span>
                                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-white leading-snug drop-shadow-sm group-hover:text-amber-200 transition-colors">
                                            {{ __('site.facility_library') }}
                                        </h3>
                                    </div>

                                    <a href="{{ $site::path('/facilities') }}" class="absolute inset-0 z-20" aria-label="{{ __('site.facility_library') }}"></a>
                                </div>

                                {{-- Card 4: Fasilitas Olahraga --}}
                                <div class="group relative w-full h-[160px] sm:h-[220px] lg:h-[235px] rounded-2xl overflow-hidden border border-line/70 bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300">
                                    <img src="{{ asset('images/classroom.webp') }}"
                                         alt="{{ __('site.facility_sports') }}"
                                         class="size-full object-cover transform transition-transform duration-700 ease-out group-hover:scale-105"
                                         loading="lazy">

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent transition-opacity duration-300 pointer-events-none group-hover:from-black/90"></div>

                                    <div class="absolute top-3.5 right-3.5 size-7.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/90 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 shadow-sm z-10 pointer-events-none">
                                        <x-site-icon name="arrow-up-right" class="size-3.5" />
                                    </div>

                                    <div class="absolute inset-x-0 bottom-0 p-4 sm:p-4.5 z-10 pointer-events-none">
                                        <span class="inline-block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-accent mb-0.5 drop-shadow-xs">
                                            {{ __('site.facility_tag_activity') }}
                                        </span>
                                        <h3 class="text-xs sm:text-sm lg:text-base font-bold text-white leading-snug drop-shadow-sm group-hover:text-amber-200 transition-colors">
                                            {{ __('site.facility_sports') }}
                                        </h3>
                                    </div>

                                    <a href="{{ $site::path('/facilities') }}" class="absolute inset-0 z-20" aria-label="{{ __('site.facility_sports') }}"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column (Desktop): Header, Pillars & Link --}}
                <div class="lg:col-span-5 flex flex-col justify-center order-1 lg:order-2">
                    {{-- Eyebrow --}}
                    <div class="flex items-center gap-2.5 mb-3.5">
                        <span class="h-0.5 w-6 bg-jts"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-jts">{{ __('site.facilities_eyebrow') }}</span>
                    </div>

                    {{-- Section Title --}}
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[40px] font-bold tracking-tight text-ink leading-[1.2] mb-4">
                        {{ __('site.facilities_headline') }}
                    </h2>

                    {{-- Description --}}
                    <p class="text-sm sm:text-base leading-relaxed text-muted mb-7 lg:mb-8 max-w-lg">
                        {{ __('site.facilities_desc') }}
                    </p>

                    {{-- 3 Feature Cards --}}
                    <div class="space-y-3 sm:space-y-3.5 mb-7 lg:mb-8">
                        {{-- Pillar 1: Trilingual --}}
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-line shadow-2xs hover:shadow-sm hover:border-jts/40 transition-all duration-200">
                            <span class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-jts border border-emerald-100">
                                <x-site-icon name="languages" class="size-5 sm:size-6 text-jts" />
                            </span>
                            <span class="text-sm sm:text-base font-bold text-ink">
                                {{ __('site.pillar_trilingual_title') }}
                            </span>
                        </div>

                        {{-- Pillar 2: Curriculum --}}
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-line shadow-2xs hover:shadow-sm hover:border-jts/40 transition-all duration-200">
                            <span class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-jts border border-emerald-100">
                                <x-site-icon name="book-open" class="size-5 sm:size-6 text-jts" />
                            </span>
                            <span class="text-sm sm:text-base font-bold text-ink">
                                {{ __('site.pillar_curriculum_title') }}
                            </span>
                        </div>

                        {{-- Pillar 3: Character --}}
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-line shadow-2xs hover:shadow-sm hover:border-jts/40 transition-all duration-200">
                            <span class="flex size-11 sm:size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-jts border border-emerald-100">
                                <x-site-icon name="award" class="size-5 sm:size-6 text-jts" />
                            </span>
                            <span class="text-sm sm:text-base font-bold text-ink">
                                {{ __('site.pillar_character_title') }}
                            </span>
                        </div>
                    </div>

                    {{-- Text Link with Arrow --}}
                    <div>
                        <a href="{{ $site::path('/facilities') }}" class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-jts hover:text-[#104a31] transition-colors group">
                            <span>{{ __('site.explore_facilities') }}</span>
                            <x-site-icon name="arrow-right" class="size-4.5 transition-transform duration-200 group-hover:translate-x-1.5" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Berita & Media (News & Media - 1x3 Layout with Highlight Card & 3 Compact Cards) --}}
    <section class="border-t border-line bg-[#f4f7f5] py-16 sm:py-24">
        <div class="shell">
            {{-- Section Header --}}
            <div class="mb-10 sm:mb-12 max-w-3xl">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-ink">{{ __('site.latest') }}</h2>
                <p class="mt-2 text-sm sm:text-base text-muted max-w-2xl">
                    {{ __('site.news_copy') }}
                </p>
            </div>

            @php
                $featuredPost = $posts->first();
                $secondaryPosts = $posts->slice(1, 3);
            @endphp

            @if($featuredPost)
                {{-- 1. Main Highlight Card (Top Horizontal Card) --}}
                <article class="group relative flex flex-col lg:flex-row bg-white rounded-2xl border border-line/80 shadow-2xs hover:shadow-xl hover:border-jts/40 transition-all duration-300 overflow-hidden mb-8 sm:mb-10">
                    {{-- Left: Cover Image (~50% width on Desktop) --}}
                    <a href="{{ $site::path('/news/'.$featuredPost->slug) }}" class="relative w-full lg:w-1/2 aspect-[16/10] lg:aspect-auto overflow-hidden bg-gray-100 block shrink-0">
                        <img src="{{ $site::image($featuredPost->image) }}"
                             alt="{{ $featuredPost->title }}"
                             class="size-full object-cover object-center transform transition-transform duration-500 ease-out group-hover:scale-105"
                             loading="lazy" width="800" height="500">

                        {{-- Floating Pill Badges on Photo --}}
                        <div class="absolute top-4 left-4 z-10 flex flex-wrap gap-2">
                            @if($featuredPost->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-jts text-white shadow-md">
                                    {{ __('site.'.$featuredPost->category->code) }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-jts/90 text-white shadow-md backdrop-blur-xs">
                                {{ $featuredPost->school_level_label }}
                            </span>
                        </div>
                    </a>

                    {{-- Right: Content Body (~50% width on Desktop) --}}
                    <div class="p-6 sm:p-8 lg:p-10 flex flex-col justify-between flex-1">
                        <div>
                            {{-- Publication Date --}}
                            <div class="text-xs sm:text-sm text-muted font-medium mb-3">
                                {{ $featuredPost->upload_date->translatedFormat('d F Y') }}
                            </div>

                            {{-- Headline --}}
                            <h3 class="text-xl sm:text-2xl font-bold leading-tight text-ink group-hover:text-jts transition-colors duration-200 mb-3.5 line-clamp-2 sm:line-clamp-3">
                                <a href="{{ $site::path('/news/'.$featuredPost->slug) }}">
                                    {{ $featuredPost->title }}
                                </a>
                            </h3>

                            {{-- Excerpt Summary Paragraph --}}
                            <p class="text-sm sm:text-base leading-relaxed text-muted line-clamp-3 mb-6">
                                {{ $featuredPost->translation()?->summary }}
                            </p>
                        </div>

                        {{-- Learn More Button (Exclusively on Highlight Card) --}}
                        <div class="pt-4 border-t border-line/60 mt-auto">
                            <a href="{{ $site::path('/news/'.$featuredPost->slug) }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold uppercase tracking-wider text-jts hover:text-jts/80 group/link transition-all">
                                <span>{{ __('site.learn_more') }}</span>
                                <x-site-icon name="arrow-right" class="size-4 transition-transform duration-200 group-hover/link:translate-x-1" />
                            </a>
                        </div>
                    </div>
                </article>

                {{-- 2. Three Cards Underneath (3-Column Grid) --}}
                @if($secondaryPosts->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 mb-10 sm:mb-12">
                        @foreach($secondaryPosts as $post)
                            <article class="group relative flex flex-col bg-white rounded-2xl border border-line/80 shadow-2xs hover:shadow-xl hover:border-jts/40 transition-all duration-300 overflow-hidden">
                                {{-- Thumbnail Image with Badge --}}
                                <a href="{{ $site::path('/news/'.$post->slug) }}" class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100 block">
                                    <img src="{{ $site::image($post->image) }}"
                                         alt="{{ $post->title }}"
                                         class="size-full object-cover object-center transform transition-transform duration-500 ease-out group-hover:scale-105"
                                         loading="lazy" width="400" height="250">

                                    {{-- Floating Category Pill Badge (Green) --}}
                                    <div class="absolute top-3.5 left-3.5 z-10 flex flex-wrap gap-1.5">
                                        @if($post->category)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-jts text-white shadow-md">
                                                {{ __('site.'.$post->category->code) }}
                                            </span>
                                        @endif
                                    </div>
                                </a>

                                {{-- Card Body --}}
                                <div class="p-5 sm:p-6 flex flex-col flex-1 justify-between">
                                    <div>
                                        {{-- Publication Date --}}
                                        <div class="text-xs text-muted font-medium mb-3">
                                            {{ $post->upload_date->translatedFormat('d F Y') }}
                                        </div>

                                        {{-- Title & Circular Arrow Button Row --}}
                                        <div class="flex items-start justify-between gap-3">
                                            <h4 class="text-sm sm:text-base font-bold leading-snug text-ink group-hover:text-jts transition-colors duration-200 line-clamp-3 flex-1">
                                                <a href="{{ $site::path('/news/'.$post->slug) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h4>

                                            {{-- Circular Arrow Button --}}
                                            <a href="{{ $site::path('/news/'.$post->slug) }}"
                                               aria-label="{{ $post->title }}"
                                               class="size-9 sm:size-10 rounded-full bg-[#eef5f0] text-jts flex items-center justify-center shrink-0 group-hover:bg-jts group-hover:text-white transition-all duration-200 shadow-2xs hover:scale-105">
                                                <x-site-icon name="arrow-right" class="size-4" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                {{-- 3. Centered Bottom CTA Button (Lihat Berita Lainnya) --}}
                <div class="text-center">
                    <a href="{{ $site::path('/news') }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-jts px-6 py-3 text-xs sm:text-sm font-bold uppercase tracking-wider text-jts hover:bg-jts hover:text-white transition-all shadow-2xs hover:shadow-md group">
                        <span>{{ __('site.see_more_news') }}</span>
                        <x-site-icon name="arrow-right" class="size-4 transition-transform duration-200 group-hover:translate-x-1" />
                    </a>
                </div>
            @else
                {{-- Empty State Fallback --}}
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-line">
                    <x-site-icon name="newspaper" class="size-10 mx-auto text-muted mb-3"/>
                    <p class="text-base font-semibold text-ink">{{ __('site.no_results') }}</p>
                    <p class="text-sm text-muted mt-1">{{ __('site.news_copy') }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Section: Logo Kampus Lulusan JTS (University Logos Marquee) --}}
    @if(isset($universityLogos) && $universityLogos->isNotEmpty())
        @php
            $marqueeLogos = $universityLogos->count() < 8
                ? $universityLogos->concat($universityLogos)->concat($universityLogos)
                : $universityLogos->concat($universityLogos);
        @endphp
        <section class="border-t border-line bg-white py-14 sm:py-18 overflow-hidden">
            <div class="shell mb-8 sm:mb-10 text-center">
                <div class="flex items-center justify-center gap-3 mb-2.5">
                    <span class="h-px w-6 bg-jts"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-jts">{{ __('site.alumni_eyebrow') }}</span>
                    <span class="h-px w-6 bg-jts"></span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink text-center text-balance">
                    {{ __('site.alumni_headline') }}
                </h2>
            </div>

            {{-- Infinite Marquee Track with Side Fade Masks --}}
            <div class="relative w-full overflow-hidden py-6 sm:py-8">
                {{-- Left Fade Mask --}}
                <div class="pointer-events-none absolute inset-y-0 left-0 w-20 sm:w-44 lg:w-56 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
                {{-- Right Fade Mask --}}
                <div class="pointer-events-none absolute inset-y-0 right-0 w-20 sm:w-44 lg:w-56 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

                {{-- Scrolling Track --}}
                <div class="flex animate-marquee items-center gap-14 sm:gap-20 lg:gap-24 hover:[animation-play-state:paused]">
                    @foreach($marqueeLogos as $uni)
                        <div class="shrink-0 flex items-center justify-center h-16 sm:h-20 lg:h-22 w-auto min-w-[140px] sm:min-w-[180px] max-w-[240px] sm:max-w-[280px] px-4 grayscale opacity-90 hover:grayscale-0 hover:opacity-100 hover:scale-105 transition-all duration-300"
                             title="{{ $uni->title ?? 'University Logo' }}">
                            <img src="{{ $site::image($uni->logo) }}"
                                 alt="{{ $uni->title ?? 'University Logo' }}"
                                 class="h-12 sm:h-16 lg:h-18 w-auto max-w-full object-contain pointer-events-none select-none"
                                 loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Section: CTA Banner (Paling bawah sebelum footer) --}}
    <section class="border-t border-line bg-white py-12 sm:py-16">
        <div class="shell">
            <x-cta-banner />
        </div>
    </section>
</x-layouts.site>
