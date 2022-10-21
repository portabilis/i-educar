<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;

class LegacyBenefit extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasLegacyUserAction;

    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno_beneficio';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno_beneficio';

    /**
     * @var array
     */
    protected $fillable = [
        'nm_beneficio',
        'desc_beneficio',
        'data_exclusao',
        'ativo',
    ];

    public array $legacy = [
        'id' => 'cod_aluno_beneficio',
        'name' => 'nm_beneficio',
        'description' => 'desc_beneficio',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
    ];

    public function students()
    {
        return $this->belongsToMany(LegacyStudent::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_beneficio_id', 'aluno_id');
    }
}
