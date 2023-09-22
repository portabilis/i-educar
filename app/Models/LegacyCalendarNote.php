<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;

class LegacyCalendarNote extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public $table = 'pmieducar.calendario_anotacao';

    public $primaryKey = 'cod_calendario_anotacao';

    public $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_anotacao',
        'descricao',
        'data_cadastro',
        'data_exclusao',
        'ativo',
    ];
}
