<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltaGeral extends Model
{
    use HasFactory; /**
    * @var string
    */
    protected $table = 'modules.falta_geral';

    /**
     * @var string
     */
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'id',
        'falta_aluno_id',
        'quantidade',
        'etapa' 
    
    ];
 
    /**
     * @var bool
     */
    public $timestamps = false;
 
    
    use HasFactory;

}
