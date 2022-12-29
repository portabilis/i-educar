<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponenteCurricularTurma extends Model
{
    use HasFactory;

    /**
    * @var string
    */
   protected $table = 'modules.componente_curricular_turma';

   /**
    * @var string
    */
   protected $primaryKey = 'id';

   protected $fillable = [
       'componente_curricular_id',
       'turma_id',
       'carga_horaria'
       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;

   
}
