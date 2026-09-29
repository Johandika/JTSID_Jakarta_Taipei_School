<x-layouts.site :title="__($kind === 'news' ? 'site.news' : 'site.resources')">
    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset($kind === 'news' ? 'images/news-hero.webp' : 'images/resources-hero.webp') }}" alt="{{ __($kind === 'news' ? 'site.news' : 'site.resources') }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ \App\Support\Site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ __($kind === 'news' ? 'site.news' : 'site.resources') }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ __($kind === 'news' ? 'site.news_copy' : 'site.resources_copy') }}</p>
        </div>
    </div>
    @if($kind === 'resources')
    <nav class="shell flex flex-wrap gap-x-6 gap-y-1 border-b border-line py-5" aria-label="{{ __('site.type') }}">
        @foreach(['calendar' => 'calendar', 'classroom_regulations' => 'classroom_regulations', 'syllabus' => 'syllabus', 'textbook' => 'textbook', 'lesson_progress' => 'lesson_progress', 'weekly_news' => 'weekly_news'] as $code => $label)
        <a href="{{ \App\Support\Site::path('/resources'.($code ? '/'.$code : '')) }}" class="py-2 text-sm {{ $type === $code ? 'font-bold text-jts underline underline-offset-8' : 'text-muted hover:text-jts' }}">{{ __('site.'.$label) }}</a>
        @endforeach
    </nav>
    @endif
    <div class="shell py-9"><livewire:content-catalog :kind="$kind" :type="$type"/></div>
</x-layouts.site>
