<?php

namespace App\Http\Controllers;

use App\Models\AdmissionFee;
use App\Models\AdmissionForm;
use App\Models\Post;
use App\Models\SubjectResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function admissionForm()
    {
        $form = AdmissionForm::current();
        abort_unless($form, 404);

        return Storage::disk('public')->download($form->file_path, $form->download_name, [
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }

    public function admissionFee(AdmissionFee $admissionFee): StreamedResponse
    {
        abort_unless($admissionFee->hasDownloadableFile(), 404);

        return Storage::disk('local')->download($admissionFee->file_path, $admissionFee->downloadName(), [
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }

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
