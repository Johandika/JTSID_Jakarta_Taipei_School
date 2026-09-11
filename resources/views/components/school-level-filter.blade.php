@php($levels = \App\Support\SchoolLevel::class)
<div class="border-y border-line bg-[#f4f7f5]">
    <div class="shell flex flex-wrap items-center justify-between gap-3 py-3">
        <div><p class="text-sm font-semibold">{{ __('site.browse_level') }}</p><p class="mt-1 text-xs text-muted">{{ __('site.level_hint') }}</p></div>
        <nav aria-label="{{ __('site.grade') }}" class="flex flex-wrap gap-1 rounded-lg border border-line bg-white p-1">
            @foreach(['all' => __('site.all_levels'), ...$levels::OPTIONS] as $code => $label)
                <a href="{{ $levels::url($code) }}" @if($levels::selected() === $code) aria-current="true" @endif
                   class="inline-flex min-h-10 items-center justify-center rounded-md px-3 text-sm font-semibold sm:px-4 {{ $levels::selected() === $code ? 'bg-jts text-white' : 'text-muted hover:bg-gray-100 hover:text-jts' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
</div>
