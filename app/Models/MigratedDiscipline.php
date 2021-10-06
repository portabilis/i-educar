<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MigratedDiscipline extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'old_discipline_id', 'new_discipline_id', 'grade_id', 'year', 'created_by'
    ];
}
