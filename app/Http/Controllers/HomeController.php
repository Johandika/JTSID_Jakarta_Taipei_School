<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SlideshowItem;
use App\Models\SubjectResource;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('public.home', [
            'posts' => Post::published()->forSchoolLevel()->with(['translations', 'category', 'department'])->orderByDesc('upload_date')->limit(3)->get(),
            'resources' => SubjectResource::published()->forSchoolLevel()->with(['translations', 'type'])->orderByDesc('upload_date')->limit(4)->get(),
            'slides' => SlideshowItem::where('is_active', true)->forSchoolLevel()->orderBy('sort_order')->limit(10)->get(),
        ]);
    }
}
