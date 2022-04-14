<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPlanningBNCC extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.planejamento_aula_bncc';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    use HasFactory;
}
