<x-layouts.site :title="__('pages.board_of_directors.seo_title') ?: __('pages.board_of_directors.title')" :description="__('pages.board_of_directors.summary')">
    @php
        $site = \App\Support\Site::class;
        $page = __('pages.board_of_directors');
        $directors = \App\Models\Director::where('is_active', true)->orderBy('sort_order')->get();
    @endphp

    {{-- Headline Section with Supporting Background Image (JTS Green Overlay) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/board-of-directors-hero.webp') }}" alt="{{ $page['heading'] ?? $page['title'] }}" class="size-full object-cover object-center opacity-55" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/90 via-[#0e3b2a]/65 to-[#176443]/30"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $page['heading'] ?? $page['title'] }}</h1>
            <p class="mt-4 max-w-4xl lg:max-w-5xl text-base leading-7 text-white/90">{{ $page['subheading'] ?? $page['summary'] }}</p>
        </div>
    </div>

    {{-- Board of Directors 3 Columns Interactive 3D Flip Card Section --}}
    <div class="shell pt-10 sm:pt-14 pb-16 sm:pb-24">
        {{-- 3 Columns Grid Container (1x3 per row) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($directors as $director)
                @php
                    $directorName = $director instanceof \App\Models\Director ? $director->trans('name') : ($director['name'] ?? '');
                    $directorRole = $director instanceof \App\Models\Director ? $director->trans('role') : ($director['role'] ?? '');
                    $directorPhoto = $director instanceof \App\Models\Director ? $director->photo_url : asset($director['image'] ?? 'images/logo.webp');
                    $directorEducation = $director instanceof \App\Models\Director ? $director->getEducationList() : ($director['education'] ?? []);
                    $directorBio = $director instanceof \App\Models\Director ? $director->trans('bio') : ($director['bio'] ?? '');
                @endphp
                <div x-data="{ flipped: false }" class="perspective-1000 group">
                    <div
                        class="relative w-full h-[510px] sm:h-[530px] rounded-3xl transition-transform duration-700 transform-style-3d cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-[#176443] focus:ring-offset-2"
                        :class="{ 'rotate-y-180': flipped }"
                        @click="flipped = !flipped"
                        @keydown.enter="flipped = !flipped"
                        @keydown.space.prevent="flipped = !flipped"
                        tabindex="0"
                        role="button"
                        :aria-expanded="flipped.toString()"
                        aria-label="{{ $directorName }}"
                    >
                        {{-- SISI DEPAN (FRONT FACE) --}}
                        <div class="absolute inset-0 w-full h-full backface-hidden rounded-3xl bg-white border border-line shadow-xs hover:shadow-xl transition-all duration-300 p-4 flex flex-col justify-between overflow-hidden group-hover:border-[#176443]/40">
                            {{-- Top Portrait Photo --}}
                            <div class="relative w-full aspect-square overflow-hidden rounded-2xl bg-neutral-100">
                                <img
                                    src="{{ $directorPhoto }}"
                                    alt="{{ $directorName }}"
                                    class="w-full h-full object-cover object-center grayscale contrast-105 group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-60"></div>

                                {{-- Floating Interactive Cue Badge --}}
                                <div class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-medium border border-white/20 transition-transform duration-300 group-hover:scale-105 shadow-xs">
                                    <svg class="size-3 text-[#efcf5d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                        <path d="M3 3v5h5"/>
                                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                        <path d="M16 16h5v5"/>
                                    </svg>
                                    <span>{{ $page['click_to_flip'] ?? 'Klik untuk profil' }}</span>
                                </div>
                            </div>

                            {{-- Bottom Details (Jabatan & Nama Elegan) --}}
                            <div class="pt-4 pb-2 px-1 flex flex-col justify-end">
                                <span class="text-xs font-semibold tracking-wider uppercase text-[#176443] line-clamp-1">
                                    {{ $directorRole }}
                                </span>
                                <h3 class="mt-1 text-xl sm:text-2xl font-serif font-bold text-ink tracking-tight line-clamp-1">
                                    {{ $directorName }}
                                </h3>

                                <div class="mt-3 pt-3 border-t border-line/60 flex items-center justify-between text-xs text-muted">
                                    <span class="inline-flex items-center gap-1.5 text-[#176443] font-semibold group-hover:underline">
                                        <span>{{ $page['click_to_flip'] ?? 'Klik untuk profil' }}</span>
                                        <x-site-icon name="arrow-right" class="size-3.5"/>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- SISI BELAKANG (BACK FACE) --}}
                        <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 rounded-3xl bg-gradient-to-b from-[#f7faf8] to-white border-2 border-[#176443]/30 shadow-xl p-5 sm:p-6 flex flex-col justify-between overflow-y-auto">
                            <div>
                                {{-- Header Sisi Belakang --}}
                                <div class="flex items-start justify-between gap-2 border-b border-line pb-3">
                                    <div>
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-[#176443]/10 text-[#176443]">
                                            {{ $directorRole }}
                                        </span>
                                        <h4 class="mt-1.5 text-base sm:text-lg font-serif font-bold text-ink leading-snug">
                                            {{ $directorName }}
                                        </h4>
                                    </div>
                                    <button
                                        type="button"
                                        class="size-8 rounded-full bg-[#176443]/10 hover:bg-[#176443] text-[#176443] hover:text-white flex items-center justify-center transition-colors shrink-0"
                                        title="{{ $page['flip_back'] ?? 'Kembali' }}"
                                    >
                                        <x-site-icon name="x" class="size-4"/>
                                    </button>
                                </div>

                                {{-- Riwayat Pendidikan --}}
                                <div class="mt-4">
                                    <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#176443]">
                                        <x-site-icon name="graduation-cap" class="size-3.5 text-[#176443]"/>
                                        <span>{{ $page['education_title'] ?? 'Riwayat Pendidikan' }}</span>
                                    </div>
                                    <ul class="mt-2 space-y-1.5 text-xs text-ink/85 leading-relaxed">
                                        @foreach($directorEducation as $edu)
                                            <li class="flex items-start gap-1.5">
                                                <span class="size-1.5 rounded-full bg-[#176443] mt-1.5 shrink-0"></span>
                                                <span>{{ $edu }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Biografi & Pengalaman --}}
                                <div class="mt-4">
                                    <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-[#176443]">
                                        <x-site-icon name="file-text" class="size-3.5 text-[#176443]"/>
                                        <span>{{ $page['experience_title'] ?? 'Profil & Pengalaman' }}</span>
                                    </div>
                                    <p class="mt-2 text-xs leading-relaxed text-muted">
                                        {{ $directorBio }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tombol Kembali di Bawah --}}
                            <div class="pt-4 border-t border-line mt-3 flex items-center justify-center">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#176443] hover:underline">
                                    <x-site-icon name="arrow-left" class="size-3.5"/>
                                    <span>{{ $page['flip_back'] ?? 'Kembali' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.site>
