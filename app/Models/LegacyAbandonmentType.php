<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;

/**
 * @property array<int, string> $fillable
 */
class LegacyAbandonmentType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = null;

    protected $table = 'pmieducar.abandono_tipo';

    protected $primaryKey = 'cod_abandono_tipo';

    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nome',
        'data_exclusao',
        'ativo',
    ];
}
