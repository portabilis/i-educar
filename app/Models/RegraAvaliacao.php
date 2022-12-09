<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegraAvaliacao extends Model
{
    use HasFactory;
    /**

  * @var string
     */
    protected $table = 'modules.regra_avaliacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'serie_id',
        'ano_letivo',
        'regra_avaliacao_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
