<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSpace extends Model
{
    protected $fillable = [
        'name',
        'size',
        'school_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'school_id');
    }
}
