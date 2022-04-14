<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacySchoolingDegree extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.escolaridade';

    /**
     * @var string
     */
    protected $primaryKey = 'idesco';

    /**
     * @var array
     */
    protected $fillable = [
        'idesco', 'descricao', 'escolaridade',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
