<x-layouts.site :title="__('pages.contact.seo_title') ?: __('pages.contact.title')" :description="__('pages.contact.summary')">
    @php
        $site = \App\Support\Site::class;
        $page = __('pages.contact');
        $contact = config('site.contact');
    @endphp

    {{-- Hero Section (Dark Green Overlay consistent with other public pages) --}}
    <div class="relative overflow-hidden bg-[#0a2e1f] border-b border-line py-12 sm:py-16 text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/campus.jpg') }}" alt="{{ $page['title'] }}" class="size-full object-cover object-center opacity-45" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#0a2e1f]/95 via-[#0e3b2a]/75 to-[#176443]/40"></div>
        </div>
        <div class="shell relative z-10">
            <a href="{{ $site::path('/') }}" class="mb-5 inline-flex items-center gap-2 text-xs text-white/70 hover:text-white transition-colors">
                {{ __('site.home') }}<x-site-icon name="chevron-right" class="size-3"/>
            </a>
            <h1 class="text-4xl font-semibold leading-tight sm:text-5xl text-white">{{ $page['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/90">{{ $page['summary'] }}</p>
        </div>
    </div>

    {{-- Main Section: Contact Info + Livewire Form + Map --}}
    <div class="shell py-12 sm:py-16 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            {{-- Left Column: Contact Information --}}
            <div class="lg:col-span-5 bg-gradient-to-br from-[#f8faf9] via-[#f2f7f4] to-[#eaf2ee] rounded-3xl border border-slate-200/80 p-6 sm:p-8 lg:p-10 shadow-xs relative overflow-hidden flex flex-col justify-between h-full">
                {{-- Subtle Decorative Watermark Pattern --}}
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-[0.06] select-none">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="w-64 h-64 sm:w-80 sm:h-80 object-contain" />
                </div>

                <div class="relative z-10">
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#0e3b2a]">
                        {{ $page['contact_info_title'] ?? 'Contact Information' }}
                    </h2>
                    <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                        {{ $page['contact_info_subtitle'] ?? 'Hubungi kami melalui informasi berikut atau isi formulir di samping.' }}
                    </p>

                    <div class="mt-8 space-y-6 sm:space-y-7">
                        {{-- 1. Address --}}
                        <div class="flex items-start gap-4">
                            <div class="size-11 sm:size-12 rounded-2xl bg-white shadow-xs border border-emerald-900/10 flex items-center justify-center shrink-0 text-[#0d6b46]">
                                <x-site-icon name="map-pin" class="size-5 sm:size-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                                    {{ $page['address_label'] ?? 'Address' }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-600 leading-relaxed">
                                    {{ $contact['address']['full'] ?? 'Jl. Raya Kelapa Hybrida Blok QA3, Kelapa Gading, Jakarta Utara 14250, Indonesia' }}
                                </p>
                            </div>
                        </div>

                        {{-- 2. Telephone --}}
                        <div class="flex items-start gap-4">
                            <div class="size-11 sm:size-12 rounded-2xl bg-white shadow-xs border border-emerald-900/10 flex items-center justify-center shrink-0 text-[#0d6b46]">
                                <x-site-icon name="phone" class="size-5 sm:size-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                                    {{ $page['telephone_label'] ?? 'Telephone' }}
                                </h3>
                                <div class="mt-1 text-sm text-slate-600 space-y-1">
                                    <p>
                                        <a href="{{ $contact['phones']['primary']['url'] }}" class="hover:text-[#0d6b46] font-medium transition-colors">
                                            {{ $contact['phones']['primary']['display'] }}
                                        </a>
                                    </p>
                                    <p>
                                        <a href="{{ $contact['phones']['secondary']['url'] }}" class="hover:text-[#0d6b46] font-medium transition-colors">
                                            {{ $contact['phones']['secondary']['display'] }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Email --}}
                        <div class="flex items-start gap-4">
                            <div class="size-11 sm:size-12 rounded-2xl bg-white shadow-xs border border-emerald-900/10 flex items-center justify-center shrink-0 text-[#0d6b46]">
                                <x-site-icon name="mail" class="size-5 sm:size-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                                    {{ $page['email_label'] ?? 'Email' }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    <a href="{{ $contact['email']['url'] }}" class="hover:text-[#0d6b46] font-medium transition-colors">
                                        {{ $contact['email']['display'] }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        {{-- 4. Jam Operasional --}}
                        <div class="flex items-start gap-4">
                            <div class="size-11 sm:size-12 rounded-2xl bg-white shadow-xs border border-emerald-900/10 flex items-center justify-center shrink-0 text-[#0d6b46]">
                                <x-site-icon name="clock" class="size-5 sm:size-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                                    {{ $page['opening_hours_label'] ?? 'Jam Operasional' }}
                                </h3>
                                <div class="mt-1 text-sm text-slate-600">
                                    <p class="font-medium text-slate-700">{{ $page['opening_days'] ?? 'Senin – Jumat' }}</p>
                                    <p class="text-xs sm:text-sm text-slate-500">{{ $page['opening_time'] ?? '07:30 – 16:00 WIB' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp Prompt Pill --}}
                <div class="mt-8 pt-6 border-t border-emerald-900/10 flex items-center justify-between gap-3 text-xs text-slate-600">
                    <span class="flex items-center gap-1.5 font-medium">
                        <x-site-icon name="message-circle" class="size-4 text-[#0d6b46]" />
                        <span>{{ __('pages.contact.contact_link') }}</span>
                    </span>
                    <a
                        href="{{ $contact['whatsapp']['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 font-semibold text-[#0d6b46] hover:underline"
                    >
                        <span>{{ $contact['whatsapp']['display'] }}</span>
                        <x-site-icon name="external-link" class="size-3" />
                    </a>
                </div>
            </div>

            {{-- Right Column: Livewire Contact Form --}}
            <div class="lg:col-span-7">
                <livewire:contact-form />
            </div>
        </div>

        {{-- Google Maps Embed Section --}}
        <div class="mt-12 sm:mt-16">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold tracking-tight text-[#0e3b2a]">
                        {{ $page['map_heading'] ?? 'Lokasi Kampus Jakarta Taipei School' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        {{ $page['map_subheading'] ?? 'Temukan rute menuju sekolah kami melalui peta interaktif berikut.' }}
                    </p>
                </div>
                <a
                    href="https://maps.google.com/?cid=4028543789025066996"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-[#0d6b46] hover:underline"
                >
                    <span>{{ $page['open_in_google_maps'] ?? 'Buka di Google Maps' }}</span>
                    <x-site-icon name="external-link" class="size-3.5" />
                </a>
            </div>

            <div class="relative w-full overflow-hidden rounded-3xl border border-slate-200 shadow-sm bg-slate-100">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2472.012996499217!2d106.90572140965325!3d-6.146191130717376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f54e9c65d815%3A0x37e85c3839067f4!2sJakarta%20Taipei%20School!5e1!3m2!1sen!2sid!4v1790326143752!5m2!1sen!2sid"
                    class="w-full h-[400px] sm:h-[460px] border-0 block"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    title="Jakarta Taipei School Google Maps"
                ></iframe>
            </div>
        </div>
    </div>
</x-layouts.site>
