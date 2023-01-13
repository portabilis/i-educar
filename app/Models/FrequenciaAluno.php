<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequenciaAluno extends Model
{

 /**
     * @var string
     */
    protected $table = 'modules.frequencia_aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'ref_frequencia',
        'ref_cod_matricula', 
        'aulas_faltou'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    use HasFactory;
}
  