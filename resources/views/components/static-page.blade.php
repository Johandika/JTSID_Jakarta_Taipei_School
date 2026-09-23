@props([
    'title',
    'summary',
    'seoTitle' => null,
    'heroImage' => null,
    'galleryUrl' => null,
    'backgroundImage' => null,
])

<x-layouts.site :title="$seoTitle ?: $title" :description="$summary">
    <x-page-heading :title="$title" :description="$summary" />
    <div class="shell py-12">
        <article class="min-w-0 max-w-3xl">
            @if($heroImage)
                <img src="{{ asset($heroImage) }}" alt="{{ $title }}"
                    class="mb-8 aspect-[16/8] w-full rounded object-cover" width="900" height="450">
            @endif
            <div class="prose" lang="{{ app()->getLocale() }}">{{ $slot }}</div>
            @if($galleryUrl)
                <a class="btn btn-outline mt-6" href="{{ \App\Support\Site::path($galleryUrl) }}">
                    {{ __('site.gallery') }}<x-site-icon name="arrow-up-right" class="size-4" />
                </a>
            @endif
            @if($backgroundImage)
                <figure class="mt-10">
                    <img src="{{ asset($backgroundImage) }}" alt="{{ $title }}"
                        class="max-h-96 w-full rounded object-contain" loading="lazy">
                </figure>
            @endif
        </article>
    </div>
</x-layouts.site>
