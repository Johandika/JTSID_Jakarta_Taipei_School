<x-layouts.site :title="__('site.staff')">
    <x-page-heading :title="__('site.staff')" :description="__('site.staff_copy')"/>
    <div class="shell py-10">
        <form class="mb-9 flex flex-wrap items-end gap-3" method="get"><label class="w-72"><span class="field-label">{{ __('site.department') }}</span><select name="department" class="field"><option value="">{{ __('site.all_departments') }}</option>@foreach($departments as $d)<option value="{{ $d->id }}" @selected(request('department') == $d->id)>{{ __('site.'.$d->code) }}</option>@endforeach</select></label><button class="btn btn-primary">{{ __('site.filter') }}</button></form>
        <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">@forelse($staff as $person)
            <article class="flex gap-4 border-b border-line pb-7">
                @if($person->photo)<img src="{{ \App\Support\Site::image($person->photo) }}" alt="{{ $person->name }}" class="size-16 rounded object-cover" loading="lazy">@else<span class="flex size-14 shrink-0 items-center justify-center rounded bg-[#eef4f0] text-jts"><x-site-icon name="users" class="size-6"/></span>@endif
                <div class="min-w-0"><p class="mb-2 text-xs text-jts">{{ __('site.'.$person->department->code) }}</p><h2 class="text-lg font-semibold">{{ $person->name }}</h2><p class="mt-1 text-sm text-muted">{{ $person->title }}</p>@if($person->extension)<p class="mt-3 text-xs text-muted">{{ __('site.extension') }} {{ $person->extension }}</p>@endif@if($person->responsibilities)<p class="mt-3 text-sm leading-6 text-muted">{{ $person->responsibilities }}</p>@endif</div>
            </article>@empty<p>{{ __('site.no_results') }}</p>@endforelse</div>
    </div>
</x-layouts.site>
