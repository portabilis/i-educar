<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use App\Models\Builders\DisciplineBuilder;
use App\Models\LegacyKnowledgeArea;
use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Discipline extends LegacyModel
{
    protected $table = 'relatorio.view_componente_curricular';

    public $timestamps = false;

    protected string $builder = DisciplineBuilder::class;

    protected $casts = [
        'tipos_base' => LegacyArray::class,
    ];

    public array $legacy = [
        'name' => 'nome',
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

    public function knowledgeArea(): BelongsTo
    {
        return $this->belongsTo(LegacyKnowledgeArea::class, 'area_conhecimento_id');
    }
}
