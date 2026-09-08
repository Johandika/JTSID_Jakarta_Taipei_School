<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityAcceptance extends CmsModel
{
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
