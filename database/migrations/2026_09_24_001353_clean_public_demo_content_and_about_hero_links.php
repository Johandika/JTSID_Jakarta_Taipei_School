<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('hero_slides')->where('cta_url', '/about')->update(['cta_url' => '/school-profile']);

        $notices = [
            'en' => '> Sample content for local testing. This is not an official school announcement.',
            'id' => '> Konten contoh untuk pengujian lokal. Informasi ini belum menjadi pengumuman resmi sekolah.',
            'zh-TW' => '> 此為本機測試用範例內容，並非學校正式公告。',
        ];

        foreach ($notices as $locale => $notice) {
            $prefix = $notice."\n\n";

            DB::table('post_translations')
                ->where('locale', $locale)
                ->where('body', 'like', $notice.'%')
                ->orderBy('id')
                ->chunkById(100, function ($translations) use ($prefix): void {
                    foreach ($translations as $translation) {
                        if (str_starts_with($translation->body, $prefix)) {
                            DB::table('post_translations')
                                ->where('id', $translation->id)
                                ->update(['body' => substr($translation->body, strlen($prefix))]);
                        }
                    }
                });

            DB::table('subject_resource_translations')
                ->where('locale', $locale)
                ->where('body', $notice)
                ->update(['body' => null]);
        }

        DB::table('staff_profiles')
            ->where('responsibilities', 'Sample profile for local testing. Replace with approved staff information.')
            ->update(['responsibilities' => null]);
    }

    /**
     * Removed demo copy cannot be safely reconstructed without replacing later edits.
     */
    public function down(): void
    {
        // This data cleanup is intentionally irreversible.
    }
};
