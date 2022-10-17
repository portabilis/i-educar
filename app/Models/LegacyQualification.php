<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyQualification extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasInstitution;

    /**
     * @var string
     */
    protected $table = 'pmieducar.habilitacao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_habilitacao';

    /**
     * @var string[]
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'data_exclusao',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(LegacyCourse::class, 'pmieducar.habilitacao_curso', 'ref_cod_habilitacao', 'ref_cod_curso');
    }
}
