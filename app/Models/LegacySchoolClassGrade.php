<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolClassGrade extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_serie';

    /**
     * @var array
     */
    protected $fillable = [
        'escola_id',
        'serie_id',
        'turma_id',
        'boletim_id',
        'boletim_diferenciado_id',
    ];
}
