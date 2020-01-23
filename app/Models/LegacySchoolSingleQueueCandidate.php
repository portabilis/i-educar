<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolSingleQueueCandidate extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_candidato_fila_unica';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_candidato_fila_unica';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_candidato_fila_unica',
        'sequencial',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function candidate()
    {
        return $this->belongsTo(LegacySingleQueueCandidate::class, 'ref_cod_candidato_fila_unica');
    }

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }
}
