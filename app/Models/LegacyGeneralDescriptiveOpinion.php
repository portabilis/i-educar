<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGeneralDescriptiveOpinion extends Model
{
    protected $table = 'modules.parecer_geral';

    protected $fillable = [
        'parecer_aluno_id',
        'parecer',
        'etapa',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyStudentDescriptiveOpinion, $this>
     */
    public function studentDescriptiveOpinion(): BelongsTo
    {
        return $this->belongsTo(LegacyStudentDescriptiveOpinion::class, 'parecer_aluno_id');
    }
}
