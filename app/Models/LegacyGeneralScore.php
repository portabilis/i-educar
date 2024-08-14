<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGeneralScore extends Model
{
    protected $table = 'modules.nota_geral';

    protected $fillable = [
        'nota_aluno_id',
        'nota',
        'nota_arredondada',
        'etapa',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyStudentScore, $this>
     */
    public function studentScore(): BelongsTo
    {
        return $this->belongsTo(LegacyStudentScore::class, 'nota_aluno_id');
    }
}
