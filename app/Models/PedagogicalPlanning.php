<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedagogicalPlanning extends Model
{
     /**
     * @var string
     */
    protected $table = 'modules.planejamento_pedagogico';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
