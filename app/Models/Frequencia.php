<?php

<<<<<<< HEAD
namespace App\Models; 
=======
namespace App\Models;
>>>>>>> 2.6-tecsis-homologacao

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
 /**
     * @var string
     */
    protected $table = 'modules.frequencia';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'data',
        'ref_cod_turma'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;}
