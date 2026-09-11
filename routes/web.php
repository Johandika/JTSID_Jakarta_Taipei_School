<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetSchoolLevel;
use Illuminate\Support\Facades\Route;

foreach (['en' => '', 'zh' => 'zh', 'id' => 'id'] as $locale => $prefix) {
    Route::prefix($prefix)->name($locale.'.')->middleware([SetLocale::class, SetSchoolLevel::class])->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/news', [ContentController::class, 'news'])->name('news');
        Route::get('/news/{slug}', [ContentController::class, 'post'])->name('post');
        Route::get('/resources/{type?}', [ContentController::class, 'resources'])->name('resources');
        Route::get('/staff', [ContentController::class, 'staff'])->name('staff');
        Route::get('/university-admission', [ContentController::class, 'universities'])->name('universities');
        Route::get('/download/{kind}/{id}', DownloadController::class)->whereNumber('id')->name('download');
        Route::get('/{slug}', [ContentController::class, 'page'])->where('slug', '(?!admin$|livewire$|id$|zh$)[a-z0-9-]+')->name('page');
    });
}
