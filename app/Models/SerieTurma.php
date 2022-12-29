<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerieTurma extends Model
{
    use HasFactory;
    /**
    * @var string
    */
   protected $table = 'pmieducar.turma';

   /**
    * @var string
    */
   protected $primaryKey = 'cod_serie';

   protected $fillable = [
       'cod_turma',
       'ref_ref_cod_serie'
       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;
}
