<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGeneralAverage extends Model
{
    protected $table = 'modules.media_geral';

    protected $fillable = [
        'nota_aluno_id',
        'media',
        'media_arredondada',
        'etapa',
    ];

    public $timestamps = false;

    public $primaryKey = 'nota_aluno_id';

    /**
     * @return BelongsTo<LegacyStudentScore, $this>
     */
    public function studentScore(): BelongsTo
    {
        return $this->belongsTo(LegacyStudentScore::class, 'nota_aluno_id');
    }
}
