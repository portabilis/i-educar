<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class EducacensoDegree extends Model
{
    protected $table = 'modules.educacenso_curso_superior';

    public const GRAU_TECNOLOGICO = 1;

    public const GRAU_LICENCIATURA = 2;

    public const GRAU_BACHARELADO = 3;

    public const GRAU_SEQUENCIAL = 4;

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
