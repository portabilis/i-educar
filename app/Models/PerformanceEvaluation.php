<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<int, string> $fillable
 */
class PerformanceEvaluation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sequential',
        'employee_id',
        'institution_id',
        'description',
        'title',
        'deleted_by',
        'created_by',
    ];

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'created_by');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'deleted_by');
    }

    /**
     * @return BelongsTo<LegacyInstitution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'institution_id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->sequential = PerformanceEvaluation::query()->where('employee_id', $model->employee_id)->max('sequential') + 1;
        });
    }
}
