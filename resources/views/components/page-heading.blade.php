@props(['title', 'description' => null])
<div class="page-heading"><div class="shell">
    <a href="{{ \App\Support\Site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-muted">{{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/></a>
    <h1 class="text-4xl font-semibold leading-tight sm:text-5xl">{{ $title }}</h1>
    @if($description)<p class="mt-4 max-w-2xl text-base leading-7 text-muted">{{ $description }}</p>@endif
</div></div>
