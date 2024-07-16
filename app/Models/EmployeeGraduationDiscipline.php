<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class EmployeeGraduationDiscipline extends LegacyModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<EmployeeGraduation, $this>
     */
    public function employeeGraduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'discipline_id');
    }
}
