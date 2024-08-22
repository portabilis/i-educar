<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyRegimeType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    protected $table = 'pmieducar.tipo_regime';

    protected $primaryKey = 'cod_tipo_regime';

    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'data_exclusao',
        'ativo',
    ];

    /**
     * @return HasMany<LegacyCourse, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(LegacyCourse::class, 'ref_cod_tipo_regime');
    }
}
