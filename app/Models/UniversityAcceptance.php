<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityAcceptance extends CmsModel
{
    public function publicImageUrl(): ?string
    {
        if (blank($this->image_url)) {
            return null;
        }

        $imageUrl = trim((string) $this->image_url);
        $driveFileId = $this->googleDriveFileId($imageUrl);

        if ($driveFileId !== null) {
            return 'https://drive.google.com/thumbnail?id='.$driveFileId.'&sz=w800&v='.substr(md5($imageUrl), 0, 10);
        }

        return $imageUrl;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    private function googleDriveFileId(string $imageUrl): ?string
    {
        if (preg_match('~drive\.google\.com/file/d/([^/]+)~', $imageUrl, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('~drive\.google\.com/(?:open|uc)\?[^#]*\bid=([^&]+)~', $imageUrl, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }
}
