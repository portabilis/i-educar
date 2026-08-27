<?php

namespace App\Models;

use App\Services\Reports\Util;
use ComponenteCurricular_Model_TipoBase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property string $nota
 * @property int $tipo_base
 */
class LegacySchoolHistoryDiscipline extends LegacyModel
{
    private const DECIMAL_PATTERN = '/^[+-]?(\d+(\.\d*)?|\.\d+)$/';

    public $timestamps = false;

    protected $table = 'pmieducar.historico_disciplinas';

    public $fillable = [
        'historico_escolar_id',
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

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_ref_cod_aluno');
    }

    /**
     * @return BelongsTo<LegacySchoolHistory, $this>
     */
    public function schoolHistory(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolHistory::class, 'historico_escolar_id');
    }

    public function score(int $decimalPlaces = 2): ?string
    {
        $score = trim((string) $this->nota);

        if ($score === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $score);

        if (!preg_match(self::DECIMAL_PATTERN, $normalized)) {
            return $score;
        }

        return Util::format($normalized, $decimalPlaces);
    }

    public function scoreNotRounding(int $decimalPlaces = 2): ?string
    {
        $score = trim((string) $this->nota);

        if ($score === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $score);

        if (!preg_match(self::DECIMAL_PATTERN, $normalized)) {
            return $score;
        }

        $normalized = bcdiv($normalized, '1', max($decimalPlaces, 0));

        if (!str_contains($normalized, '.')) {
            return $normalized;
        }

        return str_replace('.', ',', rtrim(rtrim($normalized, '0'), '.'));
    }

    public function isDiversified(): bool
    {
        return $this->tipo_base === ComponenteCurricular_Model_TipoBase::DIVERSIFICADA;
    }
}
