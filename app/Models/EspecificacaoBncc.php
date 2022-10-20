<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspecificacaoBncc extends Model
{
    use HasFactory;

    /**
    * @var string
    */
   protected $table = 'modules.bncc_especificacao';

   /**
    * @var string
    */
   protected $primaryKey = 'id';

   protected $fillable = [
       'id',
       'especificacao',
       'bncc_id'
       
   
   ];

   /**
    * @var bool
    */
   public $timestamps = false;

}
