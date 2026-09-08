<x-layouts.site :title="$page->translation()?->seo_title ?: $page->title" :description="$page->translation()?->seo_description ?: $page->translation()?->summary">
    <x-page-heading :title="$page->title" :description="$page->translation()?->summary"/>
    <div class="shell grid gap-10 py-12 lg:grid-cols-[220px_1fr]">
        <aside class="space-y-1 self-start border-l-2 border-line pl-5">
            @foreach(['about','academics','admissions','staff','contact'] as $path)<a class="block py-3 text-sm {{ $page->slug === $path ? 'font-bold text-jts' : 'text-muted hover:text-jts' }}" href="{{ \App\Support\Site::path('/'.$path) }}">{{ __('site.'.$path) }}</a>@endforeach
        </aside>
        <article class="min-w-0 max-w-3xl">
            @if($page->hero_image)<img src="{{ \App\Support\Site::image($page->hero_image) }}" alt="{{ $page->title }}" class="mb-8 aspect-[16/8] w-full rounded object-cover" width="900" height="450">@endif
            <div class="prose" lang="{{ $page->translation()?->locale }}">{!! $page->html_body !!}</div>
            @if($page->gallery_url)<a class="btn btn-outline mt-6" href="{{ \App\Support\Site::path($page->gallery_url) }}">{{ __('site.gallery') }}<x-site-icon name="arrow-up-right" class="size-4"/></a>@endif
            @if($page->background_image)<figure class="mt-10"><img src="{{ \App\Support\Site::image($page->background_image) }}" alt="{{ $page->title }}" class="max-h-96 w-full rounded object-contain" loading="lazy"></figure>@endif
        </article>
    </div>
</x-layouts.site>
