<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 */
class LegacyComplementSchool extends LegacyModel
{
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_complemento';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'cep',
        'numero',
        'complemento',
        'email',
        'nm_escola',
        'municipio',
        'bairro',
        'logradouro',
        'ddd_telefone',
        'telefone',
        'ddd_fax',
        'fax',
    ];

    public array $legacy = [
        'id' => 'ref_cod_escola',
        'name' => 'nm_escola',
        'active' => 'ativo',
        'city' => 'municipio',
        'number' => 'numero',
        'complement' => 'complemento',
        'district' => 'bairro',
        'address' => 'logradouro',
        'ddd_phone' => 'ddd_telefone',
        'phone' => 'telefone',
        'ddd_fax' => 'ddd_fax',
        'fax' => 'fax',
    ];

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola', 'cod_escola');
    }
}
