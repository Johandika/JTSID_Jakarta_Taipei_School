@props([
    'title' => null,
    'description' => null,
    'primaryUrl' => null,
    'primaryLabel' => null,
    'secondaryUrl' => null,
    'secondaryLabel' => null,
    'image' => null,
])

@php
    $site = \App\Support\Site::class;
    $title = $title ?? __('pages.school_profile.cta_title');
    $description = $description ?? __('pages.school_profile.cta_desc');
    $primaryUrl = $primaryUrl ?? $site::path('/admissions');
    $primaryLabel = $primaryLabel ?? __('pages.school_profile.cta_admissions');
    $secondaryUrl = $secondaryUrl ?? $site::path('/contact');
    $secondaryLabel = $secondaryLabel ?? __('pages.school_profile.cta_contact');
    $image = $image ?? asset('images/cta-students.webp');
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl sm:rounded-3xl bg-[#0a4a30] text-white shadow-xl']) }}>
    {{-- Right Landmark / Students Image with Seamless Gradient Overlay --}}
    <div class="absolute inset-y-0 right-0 w-full sm:w-[60%] lg:w-[55%] pointer-events-none select-none overflow-hidden">
        <img src="{{ $image }}" alt="{{ $title }}" class="size-full object-cover object-center sm:object-right" />
        <div class="absolute inset-0 bg-gradient-to-r from-[#0a4a30] via-[#0a4a30]/80 to-transparent sm:via-[#0a4a30]/50"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a4a30]/85 via-[#0a4a30]/30 to-transparent sm:hidden"></div>
    </div>

    {{-- Content Area --}}
    <div class="relative z-10 px-6 py-8 sm:px-10 sm:py-12 lg:px-14 lg:py-14 max-w-2xl">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white leading-tight">
            {{ $title }}
        </h2>

        <p class="mt-3 sm:mt-4 text-xs sm:text-sm lg:text-base leading-relaxed text-emerald-50/90 max-w-xl">
            {{ $description }}
        </p>

        <div class="mt-6 sm:mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
            @if ($primaryUrl && $primaryLabel)
                <a href="{{ $primaryUrl }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-xs sm:text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 active:scale-[0.98] transition-all">
                    <span>{{ $primaryLabel }}</span>
                    <x-site-icon name="arrow-right" class="size-4 text-gray-700" />
                </a>
            @endif

            @if ($secondaryUrl && $secondaryLabel)
                <a href="{{ $secondaryUrl }}" class="inline-flex items-center justify-center rounded-xl border border-white/40 bg-transparent px-5 py-3 text-xs sm:text-sm font-semibold text-white hover:bg-white/10 active:scale-[0.98] transition-all">
                    <span>{{ $secondaryLabel }}</span>
                </a>
            @endif
        </div>
    </div>
</div>
