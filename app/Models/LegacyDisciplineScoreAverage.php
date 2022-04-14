<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDisciplineScoreAverage extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_componente_curricular_media';

    /**
     * @var string
     */
    protected $primaryKey = 'nota_aluno_id';

    /**
     * @var array
     */
    protected $fillable = [
        'nota_aluno_id',
        'componente_curricular_id',
        'media',
        'media_arredondada',
        'etapa',
        'situacao',
        'bloqueada',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function registrationScore()
    {
        return $this->belongsTo(LegacyRegistrationScore::class, 'nota_aluno_id');
    }
}
