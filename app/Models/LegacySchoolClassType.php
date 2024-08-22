<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class LegacySchoolClassType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    protected $table = 'pmieducar.turma_tipo';

    protected $primaryKey = 'cod_turma_tipo';

    protected $fillable = [
        'ref_usuario_cad',
        'nm_tipo',
        'sgl_tipo',
        'ativo',
    ];

    /**
     * @return HasMany<LegacySchoolClass, $this>
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_cod_turma_tipo');
    }
}
