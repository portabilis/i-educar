<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.frequencia_aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'id';
    

    use HasFactory;
}
