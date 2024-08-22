<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 */
class LegacyExemptionStage extends Model
{
    protected $table = 'pmieducar.dispensa_etapa';

    protected $primaryKey = 'ref_cod_dispensa';

    protected $fillable = [
        'ref_cod_dispensa',
        'etapa',
        'ref_cod_disciplina',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyDisciplineExemption, $this>
     */
    public function exemption(): BelongsTo
    {
        return $this->belongsTo(LegacyDisciplineExemption::class, 'ref_cod_dispensa', 'cod_dispensa');
    }
}
