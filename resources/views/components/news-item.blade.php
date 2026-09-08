@props(['post'])
<article class="group min-w-0">
    <a href="{{ \App\Support\Site::path('/news/'.$post->slug) }}" class="block overflow-hidden rounded">
        <img src="{{ \App\Support\Site::image($post->image) }}" alt="{{ $post->title }}" class="aspect-[16/10] w-full object-cover transition-transform duration-300 group-hover:scale-[1.025]" loading="lazy" width="600" height="375">
    </a>
    <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs"><span class="font-semibold text-jts">{{ __('site.'.$post->category->code) }}</span><span class="text-muted">{{ $post->upload_date->translatedFormat('d M Y') }}</span></div>
    <h3 class="mb-3 mt-3 text-xl font-semibold leading-snug"><a href="{{ \App\Support\Site::path('/news/'.$post->slug) }}" class="hover:text-jts">{{ $post->title }}</a></h3>
    <p class="mb-4 line-clamp-2 text-sm leading-6 text-muted">{{ $post->translation()?->summary }}</p>
    <a class="text-link" href="{{ \App\Support\Site::path('/news/'.$post->slug) }}">{{ __('site.read') }}<x-site-icon name="arrow-up-right" class="size-4"/></a>
</article>
