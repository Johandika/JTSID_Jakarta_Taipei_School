<?php

namespace Tests\Feature;

use App\Filament\Imports\UniversityAcceptanceImporter;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\UniversityAcceptance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversityAcceptanceImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_university_admission_page_renders_acceptance_image_url(): void
    {
        $department = Department::create([
            'code' => 'senior-high',
            'name' => 'Senior High',
        ]);
        $academicYear = AcademicYear::create([
            'code' => '2026-2027',
            'name' => '2026 / 2027',
        ]);

        UniversityAcceptance::create([
            'department_id' => $department->id,
            'academic_year_id' => $academicYear->id,
            'student_name' => 'Alicia Tan',
            'university_name' => 'National Taiwan University',
            'program_name' => 'Computer Science',
            'country' => 'Taiwan',
            'image_url' => 'https://drive.google.com/file/d/drive-file-id/view?usp=sharing',
            'is_active' => true,
        ]);

        $this->get('/university-admission')
            ->assertOk()
            ->assertSee('Alicia Tan')
            ->assertSee('https://drive.google.com/thumbnail?id=drive-file-id&amp;sz=w800&amp;v=', false);
    }

    public function test_university_admission_page_shows_fifty_acceptances_per_page(): void
    {
        $department = Department::create([
            'code' => 'senior-high',
            'name' => 'Senior High',
        ]);
        $academicYear = AcademicYear::create([
            'code' => '2026-2027',
            'name' => '2026 / 2027',
        ]);

        foreach (range(1, 51) as $number) {
            UniversityAcceptance::create([
                'department_id' => $department->id,
                'academic_year_id' => $academicYear->id,
                'student_name' => 'Alumni '.$number,
                'university_name' => 'University '.$number,
                'program_name' => 'Sample program',
                'country' => 'Taiwan',
                'sort_order' => $number,
                'is_active' => true,
            ]);
        }

        $this->get('/university-admission')
            ->assertOk()
            ->assertSee('Showing')
            ->assertSee('1')
            ->assertSee('50')
            ->assertSee('51')
            ->assertSee('Alumni 50')
            ->assertDontSee('Alumni 51');

        $this->get('/university-admission?page=2')
            ->assertOk()
            ->assertSee('Alumni 51')
            ->assertDontSee('Alumni 50');
    }

    public function test_university_acceptance_importer_creates_and_updates_rows_from_csv_data(): void
    {
        Department::create([
            'code' => 'academic',
            'name' => 'Academic Office',
        ]);

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, UniversityAcceptanceImporter::TEMPLATE_HEADERS, separator: ';');
        fputcsv($stream, [
            'Alicia Tan',
            'National Taiwan University',
            'Computer Science',
            'International Admission',
            'Taiwan',
            '2026 / 2027',
            'Academic Office',
            'https://drive.google.com/file/d/drive-file-id/view?usp=sharing',
            '1',
            '1',
        ], separator: ';');
        rewind($stream);

        $result = UniversityAcceptanceImporter::importFromStream($stream);

        $acceptance = UniversityAcceptance::firstOrFail();

        $this->assertSame(['created' => 1, 'updated' => 0], $result);
        $this->assertSame('Computer Science', $acceptance->program_name);
        $this->assertSame('2026 / 2027', $acceptance->academicYear->name);
        $this->assertSame('Academic Office', $acceptance->department->name);

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, UniversityAcceptanceImporter::TEMPLATE_HEADERS, separator: ';');
        fputcsv($stream, [
            'Alicia Tan',
            'National Taiwan University',
            'Updated Program',
            'Scholarship',
            'Taiwan',
            '2026 / 2027',
            'academic',
            'https://drive.google.com/file/d/drive-file-id/view?usp=sharing',
            '2',
            '1',
        ], separator: ';');
        rewind($stream);

        $result = UniversityAcceptanceImporter::importFromStream($stream);

        $this->assertSame(['created' => 0, 'updated' => 1], $result);
        $this->assertSame(1, UniversityAcceptance::count());
        $this->assertSame('Updated Program', $acceptance->fresh()->program_name);
        $this->assertSame(2, $acceptance->fresh()->sort_order);
    }
}
