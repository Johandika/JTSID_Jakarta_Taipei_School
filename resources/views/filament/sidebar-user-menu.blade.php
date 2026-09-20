@php
    $user = filament()->auth()->user();
    $userName = filament()->getUserName($user);
@endphp

@if ($user)
    <div class="fi-sidebar-footer jts-sidebar-user-menu">
        <x-filament::dropdown placement="right-end" :teleport="true" width="xs" class="fi-user-menu">
            <x-slot name="trigger">
                <button type="button" class="fi-user-menu-trigger" aria-label="{{ $userName }}">
                    <x-filament-panels::avatar.user :user="$user" loading="lazy" />
                    <span class="fi-user-menu-trigger-text">{{ $userName }}</span>
                    <x-filament::icon icon="heroicon-m-chevron-right" class="jts-user-menu-chevron" aria-hidden="true" />
                </button>
            </x-slot>

            <x-filament::dropdown.header icon="heroicon-m-user-circle">
                {{ $userName }}
            </x-filament::dropdown.header>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item
                    tag="a"
                    href="/"
                    target="_blank"
                    icon="heroicon-o-arrow-top-right-on-square"
                >
                    Lihat Website
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item
                    tag="form"
                    :action="filament()->getLogoutUrl()"
                    method="post"
                    icon="heroicon-o-arrow-left-start-on-rectangle"
                >
                    Keluar
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>
@endif
