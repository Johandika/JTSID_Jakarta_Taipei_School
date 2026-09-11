<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Page;
use App\Models\Post;
use App\Models\StaffProfile;
use App\Models\SubjectResourceType;
use App\Models\UniversityAcceptance;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function page(string $slug)
    {
        $page = Page::published()->with('translations')->where('slug', $slug)->firstOrFail();

        return view('public.page', compact('page'));
    }

    public function post(string $slug)
    {
        $post = Post::published()->with(['translations', 'department', 'category'])->where('slug', $slug)->firstOrFail();

        return view('public.post', compact('post'));
    }

    public function news()
    {
        return view('public.catalog', ['kind' => 'news', 'type' => '']);
    }

    public function resources(?string $type = null)
    {
        abort_if($type && ! SubjectResourceType::where('code', $type)->exists(), 404);

        return view('public.catalog', ['kind' => 'resources', 'type' => $type ?? '']);
    }

    public function staff(Request $request)
    {
        $input = $request->validate(['department' => 'nullable|integer|exists:departments,id']);

        return view('public.staff', [
            'staff' => StaffProfile::where('is_active', true)->forSchoolLevel()->with('department')
                ->when($input['department'] ?? null, fn ($q, $id) => $q->where('department_id', $id))->orderBy('sort_order')->get(),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function universities(Request $request)
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
}
