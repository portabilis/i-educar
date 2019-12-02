<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDisciplineDescriptiveOpinion extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.parecer_componente_curricular';

    /**
     * @var array
     */
    protected $fillable = [
        'parecer_aluno_id',
        'componente_curricular_id',
        'parecer',
        'etapa',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return BelongsTo
     */
    public function studentDescriptiveOpinion()
    {
        return $this->belongsTo(LegacyStudentDescriptiveOpinion::class, 'parecer_aluno_id');
    }
}
