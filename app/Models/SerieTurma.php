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
   protected $table = 'pmieducar.turma_serie';

   /**
    * @var string
    */
   protected $primaryKey = 'cod_serie';

   protected $fillable = [
       'turma_id',
       'serie_id'
       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;
}
