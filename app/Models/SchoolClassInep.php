<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolClassInep extends Model
{
    /**
     * @var string
     */
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

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolClass::class, 'cod_turma', 'cod_turma');
    }
}
