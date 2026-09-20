@props(['post'])
<article class="group flex flex-col h-full bg-white rounded-xl border border-line/80 shadow-2xs hover:shadow-lg hover:border-jts/40 transition-all duration-300 overflow-hidden">
    {{-- Media Wrapper --}}
    <a href="{{ \App\Support\Site::path('/news/'.$post->slug) }}" class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100 block">
        <img src="{{ \App\Support\Site::image($post->image) }}"
             alt="{{ $post->title }}"
             class="size-full object-cover object-center transform transition-transform duration-400 ease-out group-hover:scale-105"
             loading="lazy" width="600" height="375">

        {{-- Category Badge Floating on Photo --}}
        @if($post->category)
        <div class="absolute top-3 left-3 z-10">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-jts/95 text-white shadow-sm backdrop-blur-xs">
                {{ __('site.'.$post->category->code) }}
            </span>
        </div>
        @endif
    </a>

    {{-- Content Body --}}
    <div class="p-5 sm:p-6 flex flex-col flex-1">
        {{-- Metadata Row: Date & School Level --}}
        <div class="flex items-center justify-between gap-2 text-xs text-muted mb-3">
            <span class="inline-flex items-center gap-1.5 font-medium">
                <x-site-icon name="calendar" class="size-3.5 text-jts/80" />
                {{ $post->upload_date->translatedFormat('d M Y') }}
            </span>
            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-ink">
                {{ $post->school_level_label }}
            </span>
        </div>

        {{-- Headline --}}
        <h3 class="text-base sm:text-lg font-bold leading-snug text-ink group-hover:text-jts transition-colors duration-200 line-clamp-2 mb-2.5">
            <a href="{{ \App\Support\Site::path('/news/'.$post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt / Summary --}}
        <p class="text-xs sm:text-sm leading-relaxed text-muted line-clamp-2 mb-4 flex-1">
            {{ $post->translation()?->summary }}
        </p>

        {{-- Action CTA --}}
        <div class="pt-3 border-t border-line/60 mt-auto">
            <a href="{{ \App\Support\Site::path('/news/'.$post->slug) }}"
               class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-jts hover:text-jts/80 transition-colors">
                <span>{{ __('site.read') }}</span>
                <x-site-icon name="arrow-right" class="size-3.5 transition-transform duration-200 group-hover:translate-x-1" />
            </a>
        </div>
    </div>
</article>
