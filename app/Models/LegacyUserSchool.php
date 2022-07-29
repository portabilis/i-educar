<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyUserSchool extends Model
{
    protected $table = 'pmieducar.escola_usuario';

    public $timestamps = false;

    protected $fillable = [
        'ref_cod_usuario',
        'ref_cod_escola',
        'escola_atual',
    ];
}
