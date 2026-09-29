@props([
    'livewire' => null,
])

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="jts-auth-viewport">
        {{ $slot }}
    </div>
</x-filament-panels::layout.base>
