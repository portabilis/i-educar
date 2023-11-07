<?php

namespace App\Models;

use App\Services\Reports\Util;
use ComponenteCurricular_Model_TipoBase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolHistoryDiscipline extends LegacyModel
{
    public $timestamps = false;

    protected $table = 'pmieducar.historico_disciplinas';

    public $fillable = [
        'ref_ref_cod_aluno',
        'sequencial',
        'ref_sequencial',
        'nm_disciplina',
        'nota',
        'faltas',
        'carga_horaria_disciplina',
        'dependencia',
        'tipo_base',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_ref_cod_aluno');
    }

    public function score(int $decimalPlaces = 2): ?string
    {
        if (!is_numeric($this->nota)) {
            return $this->nota !== '' ? $this->nota : null;
        }

        return Util::format($this->nota, $decimalPlaces);
    }

    public function isDiversified(): bool
    {
        return $this->tipo_base === ComponenteCurricular_Model_TipoBase::DIVERSIFICADA;
    }
}
