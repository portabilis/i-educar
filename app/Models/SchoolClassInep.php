<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $cod_turma_inep
 */
class SchoolClassInep extends Model
{
    protected $table = 'modules.educacenso_cod_turma';

    protected $fillable = [
        'cod_turma',
        'cod_turma_inep',
        'nome_inep',
        'turma_turno_id',
        'fonte',
        'created_at',
        'updated_at',
    ];

    protected function number(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_turma_inep,
        );
    }

    /**
     * @return BelongsTo<LegacySchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'cod_turma', 'cod_turma');
    }
}
