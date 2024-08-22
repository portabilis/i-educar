<?php

namespace App\Models;

use App\Services\Reports\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $nota_arredondada
 * @property float $nota_recuperacao_especifica
 */
class LegacyDisciplineScore extends Model
{
    protected $table = 'modules.nota_componente_curricular';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nota_aluno_id',
        'componente_curricular_id',
        'nota',
        'nota_arredondada',
        'etapa',
        'nota_recuperacao',
        'nota_original',
        'nota_recuperacao_especifica',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyRegistrationScore, $this>
     */
    public function registrationScore(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistrationScore::class, 'nota_aluno_id');
    }

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'componente_curricular_id');
    }

    public function score(int $decimalPlaces = 1, bool $isGeneralAbsence = false): string|float|null
    {
        $score = $isGeneralAbsence ? 0 : $this->nota_arredondada;
        if (!is_numeric($score) || empty($score)) {
            return $score;
        }

        return Util::format($score, $decimalPlaces);
    }

    public function originalScore(int $decimalPlaces = 1, bool $isGeneralAbsence = false): ?string
    {
        $score = $isGeneralAbsence ? 0 : $this->nota_original;
        if (!is_numeric($score) || empty($score)) {
            return $score;
        }

        return Util::format($score, $decimalPlaces);
    }

    public function recoveryScore(int $decimalPlaces = 1, bool $isGeneralAbsence = false): ?string
    {
        $score = $isGeneralAbsence ? 0 : $this->nota_recuperacao ?? $this->nota_recuperacao_especifica;
        if (!is_numeric($score) || empty($score)) {
            return $score;
        }

        return Util::format($score, $decimalPlaces);
    }
}
