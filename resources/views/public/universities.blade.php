<x-layouts.site :title="__('site.universities')">
    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/universities-hero.jpg') }}" alt="{{ __('site.universities') }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ \App\Support\Site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ __('site.universities') }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ __('site.universities_copy') }}</p>
        </div>
    </div>
    <div class="shell py-10">
        <form class="mb-8 grid items-end gap-4 sm:grid-cols-[2fr_1fr_auto]" method="get"><label><span class="field-label">{{ __('site.search') }}</span><input class="field" name="search" maxlength="150" value="{{ request('search') }}" placeholder="{{ __('site.university') }} / {{ __('site.student') }}"></label><label><span class="field-label">{{ __('site.year') }}</span><select class="field" name="year"><option value="">{{ __('site.all_years') }}</option>@foreach($years as $y)<option value="{{ $y->id }}" @selected(request('year') == $y->id)>{{ $y->name }}</option>@endforeach</select></label><button class="btn btn-primary"><x-site-icon name="search" class="size-4"/>{{ __('site.search') }}</button></form>
        <div class="overflow-x-auto rounded border border-line"><table class="data-table min-w-[640px]"><thead><tr><th>{{ __('site.year') }}</th><th>{{ __('site.student') }}</th><th>{{ __('site.university') }}</th><th>{{ __('site.program') }}</th><th>{{ __('site.country') }}</th></tr></thead><tbody>@forelse($acceptances as $item)<tr><td>{{ $item->academicYear->name }}</td><td>{{ $item->student_name }}</td><td class="font-semibold text-jts">{{ $item->university_name }}</td><td>{{ $item->program_name }}</td><td>{{ $item->country }}</td></tr>@empty<tr><td colspan="5">{{ __('site.no_results') }}</td></tr>@endforelse</tbody></table></div>
        <div class="mt-8">{{ $acceptances->links() }}</div>
    </div>
</x-layouts.site>
