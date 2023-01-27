<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurmaModulo extends Model
{
   
  
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_turma';

    protected $fillable = [
        'sequencial',
        'ref_cod_modulo',
        'ref_cod_turma'
    ]; 

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
