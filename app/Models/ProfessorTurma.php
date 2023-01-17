<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorTurma extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.professor_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'servidor_id',
        'turma_id',
        'ano'
    
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    
    use HasFactory;
}
