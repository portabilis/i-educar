<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\Ativo;
use App\Traits\HasLegacyUserAction;

class LegacyCalendarYear extends LegacyModel
{
    use Ativo;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public $table = 'pmieducar.calendario_ano_letivo';

    public $primaryKey = 'cod_calendario_ano_letivo';

    public const CREATED_AT = 'data_cadastra';

    public const UPDATED_AT = null;

    public const DELETE_AT = 'data_exclusao';

    public $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_escola',
        'ano',
    ];

    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }
}
