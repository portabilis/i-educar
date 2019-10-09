<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LegacyDisciplineExemption
 * @property LegacyRegistration $registration
 */
class LegacyExemptionStage extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.dispensa_etapa';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_dispensa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_dispensa',
        'etapa',
        'ref_cod_disciplina',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function exemption()
    {
        return $this->belongsTo(LegacyDisciplineExemption::class, 'ref_cod_dispensa', 'cod_dispensa');
    }
}
