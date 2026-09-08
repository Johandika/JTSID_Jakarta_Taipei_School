<x-layouts.site>
    @php($site = \App\Support\Site::class)
    <section class="relative flex min-h-[420px] items-end overflow-hidden bg-[#dce9e1] sm:min-h-[490px]">
        <img src="{{ asset('images/campus.jpg') }}" alt="Jakarta Taipei School campus entrance" class="absolute inset-0 size-full object-cover object-[center_60%]" fetchpriority="high" width="1600" height="900">
        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/35 to-black/10"></div>
        <div class="shell relative py-10 text-white sm:py-12">
            <p class="mb-4 text-sm font-medium">{{ __('site.tagline') }}</p>
            <h1 class="max-w-2xl text-4xl font-semibold leading-[1.1] sm:text-6xl">{{ __('site.school') }}</h1>
            <p class="mb-6 mt-4 max-w-lg text-sm leading-6 text-white/90">{{ __('site.hero_copy') }}</p>
            <div class="flex flex-wrap gap-3"><a class="btn btn-light" href="{{ $site::path('/about') }}">{{ __('site.explore') }}<x-site-icon name="arrow-up-right" class="size-4"/></a><a class="btn border border-white/60 text-white hover:bg-white/10" href="{{ $site::path('/admissions') }}">{{ __('site.apply') }}<x-site-icon name="arrow-right" class="size-4"/></a></div>
        </div>
    </section>
    <div class="border-b border-line">
        <div class="shell grid grid-cols-2 sm:grid-cols-4">
            @foreach(['academics' => ['book-open','/academics'], 'admissions' => ['graduation-cap','/admissions'], 'calendar' => ['calendar-days','/resources/calendar'], 'resources' => ['file-text','/resources']] as $key => [$icon,$path])
                <a href="{{ $site::path($path) }}" class="flex min-h-22 items-center justify-between gap-3 border-line py-5 pr-5 text-sm font-semibold hover:text-jts sm:border-r sm:px-6 first:sm:pl-0 last:sm:border-0">
                    <span class="flex items-center gap-3"><x-site-icon :name="$icon" class="size-5 text-jts"/>{{ __('site.'.$key) }}</span><x-site-icon name="arrow-up-right" class="hidden size-4 sm:block"/>
                </a>
            @endforeach
        </div>
    </div>
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
            <div>@foreach($resources as $resource)<a class="flex items-center gap-4 border-b border-line py-5 first:pt-0" href="{{ $site::path('/download/resources/'.$resource->id) }}"><span class="flex size-11 shrink-0 items-center justify-center rounded bg-white text-jts"><x-site-icon name="file-text"/></span><div class="min-w-0 flex-1"><span class="mb-1 block text-xs text-muted">{{ __('site.'.$resource->type->code) }} · {{ $resource->gradeLevel->name }}</span><h3 class="text-sm font-semibold">{{ $resource->title }}</h3></div><x-site-icon name="download" class="size-4 text-jts"/></a>@endforeach</div>
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
