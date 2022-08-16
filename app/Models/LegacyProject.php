<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyProject extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'pmieducar.projeto';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_projeto';

    protected $fillable = [
        'nome',
        'observacao'
    ];
}
