<x-filament-widgets::widget>
    <x-filament::section>
        <div class="jts-dashboard-header">
            <div>
                <div class="jts-dashboard-eyebrow">
                    <span>Ruang Kerja</span>
                    <span aria-hidden="true">/</span>
                    <span class="jts-dashboard-eyebrow-current">Ringkasan</span>
                    <span class="jts-dashboard-status">
                        <span class="jts-dashboard-status-dot" aria-hidden="true"></span>
                        Studio Aktif
                    </span>
                </div>

                <h1 class="jts-dashboard-title">Ringkasan JTS Content Studio</h1>
                <p class="jts-dashboard-description">
                    Kelola publikasi berita, materi pelajaran, dan pusat informasi Jakarta Taipei School.
                </p>
            </div>

            @if ($canCreatePosts || $canCreateResources)
                <div class="jts-dashboard-actions">
                    @if ($canCreatePosts)
                        <x-filament::button :href="$createPostUrl" tag="a" icon="heroicon-m-plus" color="primary">
                            Buat Berita
                        </x-filament::button>
                    @endif

                    @if ($canCreateResources)
                        <x-filament::button :href="$createResourceUrl" tag="a" icon="heroicon-m-arrow-up-tray" color="gray">
                            Upload Materi
                        </x-filament::button>
                    @endif
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
