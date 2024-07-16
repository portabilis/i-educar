<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducacensoDegree extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_curso_superior';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    public const int GRAU_TECNOLOGICO = 1;

    public const int GRAU_LICENCIATURA = 2;

    public const int GRAU_BACHARELADO = 3;

    public const int GRAU_SEQUENCIAL = 4;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'curso_id',
        'nome',
        'classe_id',
        'user_id',
        'created_at',
        'grau_academico',
    ];

    /**
     * @return HasMany<EmployeeGraduation, $this>
     */
    public function employeeGraduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'course_id');
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'codigo_curso_superior_2');
    }
}
