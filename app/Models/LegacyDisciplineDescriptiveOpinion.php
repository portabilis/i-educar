<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDisciplineDescriptiveOpinion extends Model
{
    protected $table = 'modules.parecer_componente_curricular';

    protected $fillable = [
        'parecer_aluno_id',
        'componente_curricular_id',
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

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'componente_curricular_id');
    }
}
