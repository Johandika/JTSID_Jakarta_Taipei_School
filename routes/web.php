<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$staticPages = [
    'academics',
    'admissions',
    'board-of-directors',
    'school-profile',
    'contact',
    'mandarin-program',
];

Route::get('/admin/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['en', 'id', 'zh-TW'], true), 404);

    $request->session()->put('admin_locale', $locale === 'zh-TW' ? 'zh_TW' : $locale);

    return redirect()->back(fallback: '/admin');
})->middleware('web')->name('admin.language');

foreach (['en' => '', 'zh' => 'zh', 'id' => 'id'] as $locale => $prefix) {
    Route::prefix($prefix)->name($locale.'.')->middleware(SetLocale::class)->group(function () use ($staticPages) {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/news', [ContentController::class, 'news'])->name('news');
        Route::get('/news/{slug}', [ContentController::class, 'post'])->name('post');
        Route::get('/resources/{type?}', [ContentController::class, 'resources'])->name('resources');
        Route::get('/staff', [ContentController::class, 'staff'])->name('staff');
        Route::get('/facilities', [ContentController::class, 'facilities'])->name('facilities');
        Route::get('/university-admission', [ContentController::class, 'universities'])->name('universities');
        Route::get('/download/admission-form', [DownloadController::class, 'admissionForm'])->name('download.admission-form');
        Route::get('/download/{kind}/{id}', DownloadController::class)->whereNumber('id')->name('download');
        foreach ($staticPages as $slug) {
            Route::view('/'.$slug, 'public.pages.'.$slug)->name('pages.'.$slug);
        }
    });
}
