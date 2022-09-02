<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponenteCurricular extends Model
{
    use HasFactory;

     /**
     * @var string
     */
    protected $table = 'modules.componente_curricular';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nome'
        
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
   
}
