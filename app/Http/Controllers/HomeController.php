<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\SubjectResource;
use App\Models\UniversityLogo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'heroSlides' => HeroSlide::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => Post::published()->with(['translations', 'category', 'department'])->orderByDesc('upload_date')->limit(4)->get(),
            'resources' => SubjectResource::published()->with(['translations', 'type'])->orderByDesc('upload_date')->limit(4)->get(),
            'universityLogos' => UniversityLogo::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
