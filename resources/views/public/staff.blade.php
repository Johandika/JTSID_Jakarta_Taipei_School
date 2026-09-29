<x-layouts.site :title="__('site.staff')" :description="__('site.staff_copy')">
    @php
        $site = \App\Support\Site::class;
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/staff-hero.webp') }}" alt="{{ __('site.staff') }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ __('site.staff') }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ __('site.staff_copy') }}</p>
        </div>
    </div>

    <div class="shell py-10 sm:py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
            @forelse($staff as $person)
                <article class="group flex flex-col">
                    {{-- Photo Container --}}
                    <div class="relative w-full aspect-square overflow-hidden rounded-2xl bg-neutral-100 border border-line/60">
                        {{-- Department Green Badge in Top-Left Corner --}}
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold tracking-wide bg-[#176443] text-white shadow-xs">
                                {{ __('site.'.$person->department->code) }}
                            </span>
                        </div>

                        @if($person->photo)
                            <img
                                src="{{ \App\Support\Site::image($person->photo) }}"
                                alt="{{ $person->name }}"
                                class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            />
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-[#f4f7f5] transition-transform duration-500 group-hover:scale-105">
                                <img
                                    src="{{ asset('images/jtsidlogo.svg') }}"
                                    alt=""
                                    class="h-32 w-auto max-w-[78%] object-contain opacity-80"
                                    loading="lazy"
                                />
                            </div>
                        @endif
                    </div>

                    {{-- Staff Info (Name & Role below image) --}}
                    <div class="pt-3.5 pb-1">
                        <h3 class="text-lg font-serif font-bold text-ink tracking-tight group-hover:text-jts transition-colors line-clamp-1">
                            {{ $person->name }}
                        </h3>
                        <p class="mt-1 text-sm text-muted line-clamp-2">
                            {{ $person->title }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-12 text-center text-muted">
                    <p class="text-base">{{ __('site.no_results') }}</p>
                    @if(request('department'))
                        <a href="{{ $site::path('/staff') }}" class="mt-3 inline-block text-sm font-semibold text-jts hover:underline">
                            {{ __('site.clear') }}
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.site>
