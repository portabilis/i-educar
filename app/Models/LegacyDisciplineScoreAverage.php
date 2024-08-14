<?php

namespace App\Models;

use App\Services\Reports\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $media_arredondada
 */
class LegacyDisciplineScoreAverage extends Model
{
    protected $table = 'modules.nota_componente_curricular_media';

    protected $primaryKey = 'nota_aluno_id';

    protected $fillable = [
        'nota_aluno_id',
        'componente_curricular_id',
        'media',
        'media_arredondada',
        'etapa',
        'situacao',
        'bloqueada',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyRegistrationScore, $this>
     */
    public function registrationScore()
    {
        return $this->belongsTo(LegacyRegistrationScore::class, 'nota_aluno_id');
    }

    public function average(int $decimalPlaces): string|float|null
    {
        $score = $this->media_arredondada;
        if (!is_numeric($score) || empty($score)) {
            return $score;
        }

        return Util::format($score, $decimalPlaces);
    }
}
