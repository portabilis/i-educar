<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Phone
 *
 * @property int $person_id
 * @property int $type_id
 * @property int $area_code
 * @property int $number
 * @property int $created_by
 * @property int $updated_by
 */
class LegacyPhone extends Model
{
    /**
     * @var array
     */
    protected $table = 'cadastro.fone_pessoa';

    protected $primaryKey = 'idpes';

    protected $fillable = [
        'idpes',
        'tipo',
        'ddd',
        'fone',
        'idpes_cad',
        'origem_gravacao',
        'operacao',
        'data_cad',
    ];

    public $timestamps = false;
}
