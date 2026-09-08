<x-layouts.site :title="$post->translation()?->seo_title ?: $post->title" :description="$post->translation()?->seo_description ?: $post->translation()?->summary" :image="\App\Support\Site::image($post->image)">
    <article class="shell py-10 sm:py-14">
        <div class="mx-auto max-w-3xl">
            <a class="text-link mb-8" href="{{ \App\Support\Site::path('/news') }}"><x-site-icon name="arrow-left" class="size-4"/>{{ __('site.back_news') }}</a>
            <div class="mb-4 flex flex-wrap gap-4 text-xs text-muted"><span class="font-bold text-jts">{{ __('site.'.$post->category->code) }}</span><span>{{ $post->upload_date->translatedFormat('d M Y') }}</span><span>{{ __('site.'.$post->department->code) }}</span></div>
            <h1 class="text-3xl font-semibold leading-tight sm:text-5xl">{{ $post->title }}</h1>
            <p class="mt-6 text-lg leading-8 text-muted">{{ $post->translation()?->summary }}</p>
            @if($post->image)<img src="{{ \App\Support\Site::image($post->image) }}" alt="{{ $post->title }}" class="my-9 aspect-[16/10] w-full rounded object-cover" width="900" height="562">@endif
            <div class="prose mt-8" lang="{{ $post->translation()?->locale }}">{!! $post->html_body !!}</div>
            @if($post->file_path)<a class="btn btn-primary mt-8" href="{{ \App\Support\Site::path('/download/news/'.$post->id) }}"><x-site-icon name="download" class="size-4"/>{{ __('site.download') }} · {{ strtoupper(pathinfo($post->file_path, PATHINFO_EXTENSION)) }}</a>@endif
        </div>
    </article>
</x-layouts.site>
