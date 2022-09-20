<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int idpes_exc
 * @property int idpes_cad
 * @property string nm_raca
 * @property int raca_educacenso,
 */
class LegacyRace extends Model
{
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const DELETED_AT = 'data_exclusao';
    public const UPDATED_AT =  null;

    /**
     * @var string
     */
    protected $table = 'cadastro.raca';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_raca';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes_exc',
        'idpes_cad',
        'nm_raca',
        'raca_educacenso',
    ];
}
