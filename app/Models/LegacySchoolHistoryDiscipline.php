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
        'ordenamento',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_ref_cod_aluno');
    }

    public function score(int $decimalPlaces = 2): ?string
    {
        if ($this->nota === null || $this->nota === '') {
            return null;
        }

        $score = str_replace(',', '.', $this->nota);

        if (!is_numeric($score)) {
            return $score;
        }

        return Util::format($score, $decimalPlaces);
    }

    public function scoreNotRounding(int $decimalPlaces = 2): ?string
    {
        if ($this->nota === null || $this->nota === '') {
            return null;
        }

        $score = str_replace(',', '.', $this->nota);

        if (!is_numeric($score)) {
            return $score;
        }

        return substr($score, 0, strpos($score, '.') + $decimalPlaces + 1);
    }

    public function isDiversified(): bool
    {
        return $this->tipo_base === ComponenteCurricular_Model_TipoBase::DIVERSIFICADA;
    }
}
