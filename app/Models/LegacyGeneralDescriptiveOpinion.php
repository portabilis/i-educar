<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGeneralDescriptiveOpinion extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.parecer_geral';

    /**
     * @var array
     */
    protected $fillable = [
        'parecer_aluno_id',
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
