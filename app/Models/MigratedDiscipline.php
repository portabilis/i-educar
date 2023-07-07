<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MigratedDiscipline extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'old_discipline_id', 'new_discipline_id', 'grade_id', 'year', 'created_by',
    ];

    public function oldDiscipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'old_discipline_id');
    }

    public function newDiscipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'new_discipline_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'grade_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'created_by');
    }
}
