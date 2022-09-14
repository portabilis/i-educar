<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BNCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.bncc';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'codigo',
        'habilidade',
        'codigo_habilidade',
        'componente_curricular_id',
        'inativo',
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;

    //relação de n para n
    public function series() 
    { 
        return $this->belongsToMany('App\Models\Serie', 'bncc_series', 'id_bncc', 'id_serie'); 
    }
}