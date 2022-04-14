<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyScoreExam extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_exame';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_componente_curricular',
        'nota_exame'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }
}
