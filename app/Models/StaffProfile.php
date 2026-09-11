<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolLevels;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends CmsModel
{
    use HasSchoolLevels;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
