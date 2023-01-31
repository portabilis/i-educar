<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

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
}
