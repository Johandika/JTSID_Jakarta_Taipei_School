<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolLevels;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends CmsModel
{
    use HasSchoolLevels;
    use HasTranslations;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class);
    }
}
