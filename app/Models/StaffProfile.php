<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends CmsModel
{
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
