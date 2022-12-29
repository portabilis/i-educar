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
   protected $primaryKey = 'id';

   protected $fillable = [
        'id',
       'serie_id',
       'turma_id'

       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;
}
