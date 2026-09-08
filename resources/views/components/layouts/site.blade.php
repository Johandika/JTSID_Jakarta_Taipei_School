@props(['title' => null, 'description' => null, 'image' => null])
@php
    $site = \App\Support\Site::class;
    $telephone = $contacts->firstWhere('channel', 'admission_tel');
    $address = $contacts->firstWhere('channel', 'address');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | ' : '' }}Jakarta Taipei School</title>
    <meta name="description" content="{{ $description ?: __('site.hero_copy') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach(['en', 'zh-TW', 'id', 'x-default'] as $alternate)
        <link rel="alternate" hreflang="{{ $alternate }}" href="{{ url($site::switchLocale($alternate === 'x-default' ? 'en' : $alternate)) }}">
    @endforeach
    <meta property="og:title" content="{{ $title ?: 'Jakarta Taipei School' }}">
    <meta property="og:description" content="{{ $description ?: __('site.hero_copy') }}">
    <meta property="og:image" content="{{ $image ?: asset('images/campus.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:p-4">{{ __('site.skip') }}</a>
<header x-data="{ mobile: false }" class="relative z-30 bg-white">
    <div class="bg-jts text-white">
        <div class="shell flex min-h-9 items-center justify-between gap-3 text-xs">
            @if($telephone)<a class="flex items-center gap-2 py-2" href="{{ $site::contactUrl($telephone) }}"><x-site-icon name="phone" class="size-3.5"/><span class="hidden sm:inline">{{ __('site.admissions') }}</span>{{ $telephone->value }}</a>@else<span>Jakarta Taipei School</span>@endif
            <div class="flex items-center gap-4">
                @foreach($contacts->where('is_visible_header', true)->whereIn('channel', ['instagram','whatsapp','tiktok']) as $social)
                    <a class="hidden md:inline hover:underline" href="{{ $site::contactUrl($social) }}" target="_blank" rel="noopener">{{ $social->label }}</a>
                @endforeach
                <nav aria-label="Language" class="flex items-center gap-3">
                    @foreach(['en' => 'EN', 'zh-TW' => '繁中', 'id' => 'ID'] as $code => $label)
                        <a href="{{ $site::switchLocale($code) }}" lang="{{ $code }}" class="py-2 {{ app()->getLocale() === $code ? 'font-bold underline underline-offset-4' : 'opacity-80 hover:opacity-100' }}" @if(app()->getLocale() === $code) aria-current="true" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>
    <div class="shell flex min-h-22 items-center justify-between gap-5">
        <a href="{{ $site::path('/') }}" aria-label="Jakarta Taipei School"><img src="{{ asset('images/logo.jpg') }}" alt="Jakarta Taipei School" width="234" height="57" class="h-auto w-48 sm:w-56"></a>
        <nav class="hidden items-center gap-5 xl:flex" aria-label="Main navigation">
            @foreach($navigation->where('menu_area', 'header') as $item)
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <div class="flex items-center gap-1">
                        <a href="{{ $site::path($item->url) }}" class="py-8 text-[13px] font-semibold hover:text-jts {{ request()->getPathInfo() === $site::path($item->url) ? 'text-jts' : '' }}">{{ $item->title }}</a>
                        @if($item->children->isNotEmpty())<button type="button" @click="open = !open" :aria-expanded="open" aria-label="{{ $item->title }}" class="p-1"><x-site-icon name="chevron-down" class="size-3"/></button>@endif
                    </div>
                    @if($item->children->isNotEmpty())
                        <div x-show="open" x-cloak class="absolute left-0 top-full w-60 rounded border border-line bg-white p-2 shadow-lg">
                            @foreach($item->children as $child)<a href="{{ $site::path($child->url) }}" class="block rounded px-3 py-3 text-sm hover:bg-gray-50">{{ $child->title }}</a>@endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </nav>
        <button class="icon-btn xl:hidden" @click="mobile = !mobile" :aria-expanded="mobile" aria-controls="mobile-menu" aria-label="{{ __('site.menu') }}"><x-site-icon name="menu"/></button>
    </div>
    <nav id="mobile-menu" x-show="mobile" x-cloak @keydown.escape.window="mobile = false" class="max-h-[70svh] overflow-y-auto border-t border-line px-5 pb-5 xl:hidden" aria-label="Mobile navigation">
        @foreach($navigation->where('menu_area', 'header') as $item)
            <a href="{{ $site::path($item->url) }}" class="block border-b border-line py-4 font-semibold">{{ $item->title }}</a>
            @foreach($item->children as $child)<a href="{{ $site::path($child->url) }}" class="block py-3 pl-4 text-sm text-muted">{{ $child->title }}</a>@endforeach
        @endforeach
    </nav>
</header>
<main id="main">{{ $slot }}</main>
<footer class="mt-16 border-t border-line bg-[#f4f7f5]">
    <div class="shell grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <img src="{{ asset('images/logo.jpg') }}" alt="Jakarta Taipei School" width="234" height="57" class="mb-6 w-56 mix-blend-multiply">
            @if($address)<p class="mb-4 flex max-w-sm items-start gap-3 text-sm leading-6 text-muted"><x-site-icon name="map-pin" class="mt-1 size-4"/>{{ $address->value }}</p>@endif
            @foreach($contacts->whereIn('channel', ['phone','email'])->where('is_visible_footer', true) as $contact)
                <a href="{{ $site::contactUrl($contact) }}" class="mb-3 flex items-center gap-3 text-sm text-muted"><x-site-icon :name="$contact->channel === 'phone' ? 'phone' : 'mail'" class="size-4"/>{{ $contact->value }}</a>
            @endforeach
        </div>
        <div><h2 class="mb-5 text-sm font-bold">{{ __('site.quick_links') }}</h2>
            <div class="grid gap-3 text-sm text-muted">
                @foreach($navigation->where('menu_area', 'footer') as $item)<a class="hover:text-jts" href="{{ $site::path($item->url) }}">{{ $item->title }}</a>@endforeach
                <a href="/admin" class="hover:text-jts">{{ __('site.admin') }}</a>
            </div>
        </div>
        <div><h2 class="mb-5 text-sm font-bold">{{ __('site.connect') }}</h2>
            <div class="flex flex-wrap gap-5">
                @foreach($contacts->whereIn('channel', ['instagram','whatsapp','tiktok'])->where('is_visible_footer', true) as $social)
                    <a href="{{ $site::contactUrl($social) }}" target="_blank" rel="noopener" class="text-xs text-muted">
                        @if($social->qr_image)<img src="{{ $site::image($social->qr_image) }}" alt="{{ $social->label }} QR" class="mb-2 size-20 border border-line bg-white p-1" loading="lazy">@endif
                        <span class="flex items-center gap-1">{{ $social->label }}<x-site-icon name="arrow-up-right" class="size-3"/></span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="shell flex flex-wrap justify-between gap-3 border-t border-line py-5 text-xs text-muted"><p>&copy; {{ date('Y') }} Jakarta Taipei School. {{ __('site.rights') }}</p>@env('local')<p>{{ __('site.demo') }}</p>@endenv</div>
</footer>
@livewireScripts
</body>
</html>
