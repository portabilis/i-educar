<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
  
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    protected $fillable = [
        'ref_cod_matricula',
        'nm_turma',
        'cod_turma'
        
    
    ]; 

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
