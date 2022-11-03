<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducacensoInstitution extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_ies';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'ies_id',
        'nome',
        'dependencia_administrativa_id',
        'tipo_instituicao_id',
        'uf',
        'user_id',
        'created_at',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(LegacySchool::class, 'codigo_ies');
    }

    public function employeeGraduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'college_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'instituicao_curso_superior_3');
    }
}
