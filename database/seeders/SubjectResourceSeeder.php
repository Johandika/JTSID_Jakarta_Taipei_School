<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SubjectResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = Department::where('code', 'academic')->firstOrFail()->id;
        $file = app(DemoContentSeeder::class)->sampleDocument();
        $image = 'images/classroom.webp';
        if (! Storage::disk('public')->exists($image)) {
            Storage::disk('public')->put($image, file_get_contents(public_path($image)));
        }

        foreach (SubjectResourceType::all() as $type) {
            $detailed = in_array($type->code, SubjectResource::DETAILED_TYPE_CODES, true);
            $levels = $detailed ? SubjectResource::CLASS_LEVEL_OPTIONS : GradeLevel::pluck('name', 'code')->all();
            $semesters = $detailed ? SubjectResource::SEMESTER_OPTIONS : ['' => ''];

            foreach ($levels as $level => $label) {
                foreach ($semesters as $semester => $semesterLabel) {
                    $period = $label.($semesterLabel ? ' - '.$semesterLabel : '');
                    $resource = SubjectResource::firstOrCreate([
                        'topic' => $period.' '.$type->name.' 2026 / 2027',
                        'type_id' => $type->id,
                    ], [
                        'department_id' => $departmentId,
                        'school_levels' => $detailed ? [] : [$level],
                        'grade_level_id' => $detailed ? null : GradeLevel::where('code', $level)->value('id'),
                        'class_level' => $detailed ? $level : null,
                        'semester' => $detailed ? $semester : null,
                        'image' => $type->code === 'weekly_news' ? $image : null,
                        'file_path' => $file,
                        'upload_date' => today(),
                        'published_at' => now()->subDay(),
                        'status' => 'published',
                    ]);

                    foreach (['en', 'id', 'zh-TW'] as $locale) {
                        $resource->translations()->firstOrCreate(['locale' => $locale], [
                            'title' => $period.' '.__('site.'.$type->code, [], $locale).' 2026 / 2027',
                            'summary' => $type->code === 'weekly_news' ? match ($locale) {
                                'id' => 'Contoh berita mingguan: kegiatan kelas, pembelajaran, dan pengumuman sekolah.',
                                'zh-TW' => '每週校園消息範例：課堂活動、學習與學校公告。',
                                default => 'Sample weekly news: classroom activities, learning, and school announcements.',
                            } : null,
                        ]);
                    }
                }
            }
        }
    }
}
