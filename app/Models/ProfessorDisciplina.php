<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorDisciplina extends Model
{
 /**
     * @var string
     */
    protected $table = 'pmieducar.servidor_disciplina';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_disciplina';

    protected $fillable = [
        'ref_cod_disciplina',
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'ref_cod_curso'
    
    ];
    

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;}
