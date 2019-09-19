<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
