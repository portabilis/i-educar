<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegraAvaliacaoRecuperacao extends Model
{
    use HasFactory;
      /**

  * @var string
     */
    protected $table = 'modules.regra_avaliacao_recuperacao';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'regra_avaliacao_id',
        'substitui_menor_nota'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
