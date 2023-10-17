<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 */
class LegacyExemptionType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_dispensa';

    protected $primaryKey = 'cod_tipo_dispensa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'data_exclusao',
        'ativo',
    ];

    public array $legacy = [
        'id' => 'cod_tipo_dispensa',
        'name' => 'nm_tipo',
        'description' => 'descricao',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
    ];

    public function disciplineExemptions(): HasMany
    {
        return $this->hasMany(LegacyDisciplineExemption::class, 'ref_cod_tipo_dispensa');
    }
}
