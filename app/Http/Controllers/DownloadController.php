<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SubjectResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function __invoke(string $kind, int $id)
    {
        $class = match ($kind) {
            'news' => Post::class, 'resources' => SubjectResource::class, default => abort(404)
        };
        $item = $class::published()->with('translations')->findOrFail($id);
        $path = $item->file_path;
        abort_unless($path && str_starts_with($path, 'documents/') && ! str_contains($path, '..') && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, (Str::slug($item->title) ?: 'document').'.'.pathinfo($path, PATHINFO_EXTENSION), [
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }
}
