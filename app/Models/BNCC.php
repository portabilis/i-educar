<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BNCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.bncc';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'BNCC_id',
        'codigo',
        'codigo_habilidade',
        'descricao_habilidade',
        'id_componente',
        'status',
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
