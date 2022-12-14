<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaExame extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_exame';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_matricula';

    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_componente_curricular',
        'nota_exame'
    ]; 

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;

  
}
