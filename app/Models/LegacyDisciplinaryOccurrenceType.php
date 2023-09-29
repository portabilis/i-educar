<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;

class LegacyDisciplinaryOccurrenceType extends LegacyModel
{
    use Ativo;
    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_ocorrencia_disciplinar';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_ocorrencia_disciplinar';

    /**
     * @var array
     */
    protected $fillable = [
        'nm_tipo',
        'descricao',
        'max_ocorrencias',
        'data_exclusao',
        'ativo',
    ];

    public array $legacy = [
        'name' => 'nm_tipo',
        'description' => 'descricao',
        'max' => 'max_ocorrencias',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
    ];
}
