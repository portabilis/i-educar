<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponenteCurricularAno extends Model
{  
      use HasFactory;

    /**
    * @var string
    */
   protected $table = 'modules.componente_curricular_ano_escolar';

   /**
    * @var string
    */
   protected $primaryKey = 'id';

   protected $fillable = [
       'componente_curricular_id',
       'carga_horaria'
       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;


   
}
