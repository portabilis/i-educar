<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
     /**
     * @var string
     */
    protected $table = 'cadastro.pessoa';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    protected $fillable = [
        'idpes',
        'nome'
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
