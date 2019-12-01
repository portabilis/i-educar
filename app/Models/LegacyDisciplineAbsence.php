<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyDisciplineAbsence extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.falta_componente_curricular';

    /**
     * @var array
     */
    protected $fillable = [
        'falta_aluno_id',
        'componente_curricular_id',
        'quantidade',
        'etapa',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function studentAbsence()
    {
        return $this->belongsTo(LegacyStudentAbsence::class, 'falta_aluno_id');
    }
}
