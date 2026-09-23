<?php

namespace App\Models;

class UniversityLogo extends CmsModel
{
    protected $table = 'university_logos';

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'sort_order' => 'integer',
        ]);
    }
}
