<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * LegacyCourse
 *
 * @property string $name Nome do curso
 */
class LegacyDocument extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.documento';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes', 'rg', 'certidao_nascimento', 'data_cad', 'operacao', 'origem_gravacao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
