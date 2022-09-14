<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;
     /**
     * @var string
     */
    protected $table = 'pmieducar.serie';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_serie';

    protected $fillable = [
        'cod_serie',
        'nm_serie'
        
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
