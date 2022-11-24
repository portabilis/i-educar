<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatriculaTurma extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_turma',
        'ativo',
        'data_enturmacao',
        'transferido',
        'remanejado',
        'abandono',
        'falecido',
        'id'
    
    ]; 

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
