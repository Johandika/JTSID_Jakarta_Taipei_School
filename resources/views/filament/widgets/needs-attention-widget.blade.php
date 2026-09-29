<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ __('admin.widgets.needs_attention') }}</x-slot>
        <x-slot name="description">{{ __('admin.widgets.needs_attention_desc') }}</x-slot>

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
                <span>{{ __('admin.widgets.content_status') }}</span>
                <strong>{{ __('admin.widgets.content_status_value') }}</strong>
            </div>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
