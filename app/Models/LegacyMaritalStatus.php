<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyMaritalStatus extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.estado_civil';

    /**
     * @var string
     */
    protected $primaryKey = 'ideciv';

    /**
     * @var array
     */
    protected $fillable = [
        'descricao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
