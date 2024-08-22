<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class MigratedDiscipline extends Model
{
    protected $fillable = [
        'old_discipline_id',
        'new_discipline_id',
        'grade_id',
        'year',
        'created_by',
    ];

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function oldDiscipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'old_discipline_id');
    }

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function newDiscipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'new_discipline_id');
    }

    /**
     * @return BelongsTo<LegacyGrade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(LegacyGrade::class, 'grade_id');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'created_by');
    }
}
