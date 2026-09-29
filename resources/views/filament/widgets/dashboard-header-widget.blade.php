<x-filament-widgets::widget>
    <x-filament::section>
        <div class="jts-dashboard-header">
            <div>
                <div class="jts-dashboard-eyebrow">
                    <span>{{ __('admin.dashboard.workspace') }}</span>
                    <span aria-hidden="true">/</span>
                    <span class="jts-dashboard-eyebrow-current">{{ __('admin.dashboard.summary') }}</span>
                    <span class="jts-dashboard-status">
                        <span class="jts-dashboard-status-dot" aria-hidden="true"></span>
                        {{ __('admin.dashboard.studio_active') }}
                    </span>
                </div>

                <h1 class="jts-dashboard-title">{{ __('admin.dashboard.title') }}</h1>
                <p class="jts-dashboard-description">
                    {{ __('admin.dashboard.description') }}
                </p>
            </div>

            @if ($canCreatePosts || $canCreateResources)
                <div class="jts-dashboard-actions">
                    @if ($canCreatePosts)
                        <x-filament::button :href="$createPostUrl" tag="a" icon="heroicon-m-plus" color="primary">
                            {{ __('admin.dashboard.create_news') }}
                        </x-filament::button>
                    @endif

                    @if ($canCreateResources)
                        <x-filament::button :href="$createResourceUrl" tag="a" icon="heroicon-m-arrow-up-tray" color="gray">
                            {{ __('admin.dashboard.upload_resource') }}
                        </x-filament::button>
                    @endif
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
