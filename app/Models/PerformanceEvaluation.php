<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'created_by');
    }

    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'deleted_by');
    }

    public function institution()
    {
        return $this->belongsTo(LegacyInstitution::class, 'institution_id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->sequential = PerformanceEvaluation::where('employee_id', $model->employee_id)->max('sequential') + 1;
        });
    }
}
