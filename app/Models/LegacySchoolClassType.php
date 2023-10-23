<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacySchoolClassType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_tipo',
        'sgl_tipo',
        'ativo',
    ];

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_cod_turma_tipo');
    }
}
