<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeGraduationDiscipline extends LegacyModel
{
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
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
