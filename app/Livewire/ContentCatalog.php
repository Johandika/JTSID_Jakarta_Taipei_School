<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SubjectResource;
use App\Support\SchoolLevel;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ContentCatalog extends Component
{
    use WithPagination;

    #[Locked]
    public string $kind = 'news';

    #[Locked]
    public string $type = '';

    #[Locked]
    public string $locale = 'en';

    #[Url(as: 'school_level', except: 'all')]
    public string $schoolLevel = 'all';

    #[Url(as: 'class_level', except: '')]
    public string $classLevel = '';

    #[Url(except: '')]
    public string $semester = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $department = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $sort = 'newest';

    public function mount(string $kind, string $type = ''): void
    {
        $this->kind = $kind;
        $this->type = $type;
        $this->locale = app()->getLocale();
        $this->schoolLevel = $this->normalizedSchoolLevel();
    }

    public function hydrate(): void
    {
        app()->setLocale($this->locale);
    }

    public function updated($name): void
    {
        if ($name === 'schoolLevel') {
            $this->schoolLevel = $this->normalizedSchoolLevel();
        }
        if ($name === 'classLevel') {
            $this->classLevel = $this->normalizedClassLevel();
        }
        if ($name === 'semester') {
            $this->semester = $this->normalizedSemester();
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'department', 'category', 'schoolLevel', 'classLevel', 'semester', 'sort');
        $this->resetPage();
    }

    public function render(): View
    {
        app()->setLocale($this->locale);
        $usesDetailedAcademicPeriod = $this->kind !== 'news' && in_array($this->type, SubjectResource::DETAILED_TYPE_CODES, true);
        $query = ($this->kind === 'news' ? Post::query() : SubjectResource::query())->published()->with(['translations', 'department']);
        if (! $usesDetailedAcademicPeriod) {
            $query->forSchoolLevel($this->schoolLevel);
        }
        if ($this->search !== '') {
            $search = '%'.mb_substr($this->search, 0, 150).'%';
            $query->where(fn ($q) => $q->where('topic', 'ilike', $search)
                ->orWhereHas('translations', fn ($t) => $t->where('title', 'ilike', $search)));
        }
        if (ctype_digit($this->department)) {
            $query->where('department_id', $this->department);
        }
        if ($this->kind === 'news') {
            $query->with('category');
            if (ctype_digit($this->category)) {
                $query->where('category_id', $this->category);
            }
        } else {
            $query->with(['gradeLevel', 'type']);
            if ($this->type) {
                $query->whereHas('type', fn ($q) => $q->where('code', $this->type));
            }
            if ($usesDetailedAcademicPeriod) {
                $query->forClassLevel($this->classLevel)->forSemester($this->semester);
            }
        }

        return view('livewire.content-catalog', [
            'items' => $query->orderBy('upload_date', $this->sort === 'oldest' ? 'asc' : 'desc')->orderByDesc('id')->paginate(10),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => PostCategory::all(),
            'schoolLevels' => SchoolLevel::OPTIONS,
            'classLevels' => SubjectResource::CLASS_LEVEL_OPTIONS,
            'semesters' => SubjectResource::SEMESTER_OPTIONS,
            'usesDetailedAcademicPeriod' => $usesDetailedAcademicPeriod,
        ]);
    }

    private function normalizedSchoolLevel(): string
    {
        return array_key_exists($this->schoolLevel, SchoolLevel::OPTIONS) ? $this->schoolLevel : 'all';
    }

    private function normalizedClassLevel(): string
    {
        return array_key_exists($this->classLevel, SubjectResource::CLASS_LEVEL_OPTIONS) ? $this->classLevel : '';
    }

    private function normalizedSemester(): string
    {
        return array_key_exists($this->semester, SubjectResource::SEMESTER_OPTIONS) ? $this->semester : '';
    }
}
