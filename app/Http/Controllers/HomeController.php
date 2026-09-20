<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SlideshowItem;
use App\Models\SubjectResource;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'posts' => Post::published()->with(['translations', 'category', 'department'])->orderByDesc('upload_date')->limit(3)->get(),
            'resources' => SubjectResource::published()->with(['translations', 'type'])->orderByDesc('upload_date')->limit(4)->get(),
            'slides' => SlideshowItem::where('is_active', true)->orderBy('sort_order')->limit(10)->get(),
        ]);
    }
}
