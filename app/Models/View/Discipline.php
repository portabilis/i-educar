<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use App\Models\Builders\DisciplineBuilder;
use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Discipline extends LegacyModel
{
    protected $table = 'relatorio.view_componente_curricular';

    public $timestamps = false;

    protected $casts = [
      'tipos_base' => LegacyArray::class
    ];

    public array $legacy = [
        'name' => 'nome'
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
}
