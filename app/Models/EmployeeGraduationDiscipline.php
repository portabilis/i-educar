<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeGraduationDiscipline extends LegacyModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function employeeGraduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'discipline_id');
    }
}
