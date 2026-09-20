<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Perlu perhatian</x-slot>
        <x-slot name="description">Langkah tindak lanjut penting hari ini</x-slot>

        <div class="jts-attention-list">
            @foreach ($items as $item)
                <a
                    href="{{ $item['url'] }}"
                    @class([
                        'jts-attention-item',
                        'jts-attention-item-warning' => $item['tone'] === 'warning',
                    ])
                >
                    <span class="jts-attention-content">
                        <span class="jts-attention-icon" aria-hidden="true">
                            <x-filament::icon :icon="$item['icon']" />
                        </span>
                        <span>
                            <span class="jts-attention-title">{{ $item['count'] }} {{ $item['title'] }}</span>
                            <span class="jts-attention-description">{{ $item['description'] }}</span>
                        </span>
                    </span>
                    <x-filament::icon icon="heroicon-m-arrow-up-right" class="jts-attention-arrow" aria-hidden="true" />
                </a>
            @endforeach
        </div>

        <x-slot name="footer">
            <div class="jts-attention-footer">
                <span>Status Konten</span>
                <strong>Terkontrol &amp; Terkoordinasi</strong>
            </div>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
