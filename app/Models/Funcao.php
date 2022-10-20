<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    use HasFactory;
    
    /**
     * @var string
     */
    protected $table = 'pmieducar.funcao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_funcao';

    protected $fillable = [
        'cod_funcao',
        'nm_funcao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
  

    //relação de n para n
    public function series() 
    { 
        return $this->belongsToMany('App\Models\Serie', 'bncc_series', 'id_bncc', 'id_serie'); 
    }
}
