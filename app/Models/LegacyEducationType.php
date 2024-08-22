<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;

class LegacyEducationType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    protected $table = 'pmieducar.tipo_ensino';

    protected $primaryKey = 'cod_tipo_ensino';

    public array $legacy = [
        'id' => 'cod_tipo_ensino',
        'name' => 'nm_tipo',
        'complementary_activity' => 'atividade_complementar',
    ];

    protected $fillable = [
        'ref_usuario_cad',
        'ref_usuario_exc',
        'nm_tipo',
        'ativo',
        'atividade_complementar',
        'data_exclusao',
    ];
}
