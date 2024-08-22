<?php

namespace App\Models\View;

use App\Casts\LegacyArray;
use App\Models\Builders\DisciplineBuilder;
use App\Models\LegacyKnowledgeArea;
use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $carga_horaria
 * @property string $nome
 */
class Discipline extends LegacyModel
{
    /** @use HasBuilder<DisciplineBuilder> */
    use HasBuilder;

    protected $table = 'relatorio.view_componente_curricular';

    public $timestamps = false;

    protected static string $builder = DisciplineBuilder::class;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'tipos_base' => LegacyArray::class,
    ];

    /**
     * @var array<string, string>
     */
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

    /**
     * @return Collection<int, Model>
     */
    public static function getBySchoolClassAndGrade(int $schoolClass, int $grade): Collection
    {
        return self::query()
            ->where('cod_turma', $schoolClass)
            ->where('cod_serie', $grade)
            ->get();
    }

    /**
     * @return BelongsTo<LegacyKnowledgeArea, $this>
     */
    public function knowledgeArea(): BelongsTo
    {
        return $this->belongsTo(LegacyKnowledgeArea::class, 'area_conhecimento_id');
    }
}
