@props(['popup'])
<dialog data-announcement-popup="{{ $popup->id }}" data-announcement-version="{{ $popup->updated_at?->timestamp }}" aria-labelledby="announcement-title" @if($popup->body) aria-describedby="announcement-message" @endif
        class="m-auto max-h-[90svh] w-[calc(100%-2rem)] max-w-xl overflow-y-auto rounded-xl bg-white p-0 text-ink shadow-2xl backdrop:bg-black/65">
    <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-white px-5 py-3">
        <p class="text-xs font-bold uppercase tracking-wide text-jts">{{ __('site.announcement') }}</p>
        <button type="button" data-close-popup class="icon-btn" aria-label="{{ __('site.close') }}" autofocus><x-site-icon name="x"/></button>
    </div>
    @if($popup->image)<img src="{{ \App\Support\Site::image($popup->image) }}" alt="{{ $popup->title }}" class="max-h-[55svh] w-full object-contain">@endif
    <div class="p-5 sm:p-6">
        <h2 id="announcement-title" class="text-xl font-semibold">{{ $popup->title }}</h2>
        @if($popup->body)<p id="announcement-message" class="mt-3 whitespace-pre-line text-sm leading-7 text-muted">{{ $popup->body }}</p>@endif
        <div class="mt-5 flex flex-wrap gap-3">
            @if($popup->link_url)<a href="{{ \App\Support\Site::path($popup->link_url) }}" class="btn btn-primary">{{ __('site.read_more') }}<x-site-icon name="arrow-up-right" class="size-4"/></a>@endif
            <button type="button" data-close-popup class="btn btn-outline">{{ __('site.continue_site') }}</button>
        </div>
    </div>
</dialog>
