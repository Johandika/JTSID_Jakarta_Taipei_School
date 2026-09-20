<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Post;
use App\Models\StaffProfile;
use App\Models\SubjectResourceType;
use App\Models\UniversityAcceptance;
use App\Support\SchoolLevel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function post(string $slug): View
    {
        $post = Post::published()->with(['translations', 'department', 'category'])->where('slug', $slug)->firstOrFail();

        return view('public.post', compact('post'));
    }

    public function news(Request $request): View
    {
        $this->normalizeSchoolLevel($request);

        return view('public.catalog', ['kind' => 'news', 'type' => '']);
    }

    public function resources(Request $request, ?string $type = null): View
    {
        abort_if($type && ! SubjectResourceType::where('code', $type)->exists(), 404);
        $this->normalizeSchoolLevel($request);

        return view('public.catalog', ['kind' => 'resources', 'type' => $type ?? '']);
    }

    public function staff(Request $request): View
    {
        $input = $request->validate(['department' => 'nullable|integer|exists:departments,id']);

        return view('public.staff', [
            'staff' => StaffProfile::where('is_active', true)->with('department')
                ->when($input['department'] ?? null, fn ($q, $id) => $q->where('department_id', $id))->orderBy('sort_order')->get(),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function universities(Request $request): View
    {
        $input = $request->validate(['search' => 'nullable|string|max:150', 'year' => 'nullable|integer|exists:academic_years,id']);

        return view('public.universities', [
            'acceptances' => UniversityAcceptance::where('is_active', true)->with('academicYear')
                ->when($input['year'] ?? null, fn ($q, $id) => $q->where('academic_year_id', $id))
                ->when($input['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('university_name', 'ilike', '%'.$s.'%')->orWhere('student_name', 'ilike', '%'.$s.'%')))
                ->orderByDesc('academic_year_id')->orderBy('sort_order')->paginate(10)->withQueryString(),
            'years' => AcademicYear::orderByDesc('id')->get(),
        ]);
    }

    private function normalizeSchoolLevel(Request $request): void
    {
        if (! $request->query->has('school_level')) {
            return;
        }

        $schoolLevel = $request->query('school_level');
        if (! is_string($schoolLevel) || ($schoolLevel !== 'all' && ! array_key_exists($schoolLevel, SchoolLevel::OPTIONS))) {
            $request->query->remove('school_level');
        }
    }
}
