<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaltaAluno extends Model
{  /**
    * @var string
    */
   protected $table = 'modules.falta_aluno';

   /**
    * @var string
    */
   protected $primaryKey = 'id';

   protected $fillable = [ 
       'id',
       'matricula_id',
       'tipo_falta'
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;

   
   use HasFactory;
}
