<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SubjectResource;
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

    #[Url]
    public string $search = '';

    #[Url]
    public string $department = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $grade = '';

    #[Url]
    public string $sort = 'newest';

    public function mount(string $kind, string $type = ''): void
    {
        $this->kind = $kind;
        $this->type = $type;
        $this->locale = app()->getLocale();
    }

    public function hydrate(): void
    {
        app()->setLocale($this->locale);
    }

    public function updated($name): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'department', 'category', 'grade', 'sort');
        $this->resetPage();
    }

    public function render()
    {
        app()->setLocale($this->locale);
        $query = ($this->kind === 'news' ? Post::query() : SubjectResource::query())->published()->with(['translations', 'department']);
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
            if (ctype_digit($this->grade)) {
                $query->where('grade_level_id', $this->grade);
            }
        }

        return view('livewire.content-catalog', [
            'items' => $query->orderBy('upload_date', $this->sort === 'oldest' ? 'asc' : 'desc')->orderByDesc('id')->paginate(10),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => PostCategory::all(), 'grades' => GradeLevel::all(),
        ]);
    }
}
