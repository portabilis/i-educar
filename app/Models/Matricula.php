<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
/**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    protected $fillable = [
        'cod_matricula',
        'ref_cod_aluno',
        'ativo',
        'aprovado',
        'ref_cod_abandono',
        'saida_escola',
        'ref_ref_cod_serie',
        'ref_ref_cod_escola',
        'ano',
        'ref_ref_cod_escola',
        'ref_cod_curso',
        'ref_ref_cod_serie'
    
    ]; 

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;

  

}
