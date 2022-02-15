<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedagogicalPlanningBNCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.planejamento_pedagogico_bncc';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    use HasFactory;
}
