<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyTransferType extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasInstitution;
    use HasLegacyUserAction;

    /**
     * @var string
     */
    protected $table = 'pmieducar.transferencia_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_transferencia_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'desc_tipo',
        'data_exclusao',
        'ativo',
    ];

    /**
     * @return HasMany
     */
    public function transferRequests(): HasMany
    {
        return $this->hasMany(LegacyTransferRequest::class, 'ref_cod_transferencia_tipo');
    }
}
