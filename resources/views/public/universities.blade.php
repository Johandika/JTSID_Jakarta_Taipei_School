<x-layouts.site :title="__('site.universities')">
    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/universities-hero.webp') }}" alt="{{ __('site.universities') }}" class="size-full object-cover object-center opacity-55" />
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
    <div
        class="shell py-10"
        x-data="{ previewImage: null }"
        x-on:keydown.escape.window="previewImage = null"
    >
        <form class="mb-8 grid items-end gap-4 sm:grid-cols-[2fr_1fr_auto]" method="get"><label><span class="field-label">{{ __('site.search') }}</span><input class="field" name="search" maxlength="150" value="{{ request('search') }}" placeholder="{{ __('site.university') }} / {{ __('site.student') }}"></label><label><span class="field-label">{{ __('site.year') }}</span><select class="field" name="year"><option value="">{{ __('site.all_years') }}</option>@foreach($years as $y)<option value="{{ $y->id }}" @selected(request('year') == $y->id)>{{ $y->name }}</option>@endforeach</select></label><button class="btn btn-primary"><x-site-icon name="search" class="size-4"/>{{ __('site.search') }}</button></form>
        <div class="overflow-x-auto rounded border border-line">
            <table class="data-table min-w-[760px]">
                <thead>
                    <tr>
                        <th>{{ __('site.year') }}</th>
                        <th>{{ __('site.image') }}</th>
                        <th>{{ __('site.student') }}</th>
                        <th>{{ __('site.university') }}</th>
                        <th>{{ __('site.program') }}</th>
                        <th>{{ __('site.country') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acceptances as $item)
                        @php
                            $imageUrl = $item->publicImageUrl();
                            $imageAlt = $item->student_name.' - '.$item->university_name;
                        @endphp
                        <tr>
                            <td>{{ $item->academicYear->name }}</td>
                            <td @if($imageUrl) x-data="{ imageFailed: false }" @endif>
                                @if($imageUrl)
                                    <button
                                        x-show="! imageFailed"
                                        type="button"
                                        class="group rounded focus-visible:outline-jts"
                                        aria-label="{{ __('site.image') }}: {{ $imageAlt }}"
                                        x-on:click="previewImage = { src: {{ \Illuminate\Support\Js::from($imageUrl) }}, alt: {{ \Illuminate\Support\Js::from($imageAlt) }} }"
                                    >
                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $imageAlt }}"
                                            class="h-14 w-14 object-cover transition-opacity group-hover:opacity-85"
                                            loading="lazy"
                                            x-on:error="imageFailed = true"
                                        />
                                    </button>
                                    <span x-cloak x-show="imageFailed" class="text-muted">-</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->student_name }}</td>
                            <td class="font-semibold text-jts">{{ $item->university_name }}</td>
                            <td>{{ $item->program_name }}</td>
                            <td>{{ $item->country }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('site.no_results') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-8">{{ $acceptances->links() }}</div>

        <div
            x-cloak
            x-show="previewImage"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            x-on:click.self="previewImage = null"
        >
            <div class="relative max-h-[90vh] max-w-5xl">
                <button
                    type="button"
                    class="absolute right-3 top-3 z-10 inline-flex size-10 items-center justify-center rounded bg-black/65 text-white transition-colors hover:bg-black"
                    aria-label="{{ __('site.close') }}"
                    x-on:click="previewImage = null"
                >
                    <x-site-icon name="x" class="size-5"/>
                </button>
                <img
                    x-bind:src="previewImage?.src"
                    x-bind:alt="previewImage?.alt"
                    class="max-h-[90vh] max-w-[92vw] bg-white object-contain shadow-2xl"
                />
            </div>
        </div>
    </div>
</x-layouts.site>
