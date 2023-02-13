<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Discipline extends Model
{
    protected $table = 'relatorio.view_componente_curricular';

    public $timestamps = false;

    protected $casts = [
      'tipos_base' => LegacyArray::class
    ];

    protected function workload(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->carga_horaria
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome
        );
    }

    public static function getBySchoolClassAndGrade(int $schoolClass, int $grade): Collection
    {
        return self::query()
            ->where('cod_turma', $schoolClass)
            ->where('cod_serie', $grade)
            ->get();
    }
}
