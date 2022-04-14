<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudentBenefit extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno_aluno_beneficio';

    /**
     * @var array
     */
    protected $fillable = [
        'aluno_id',
        'aluno_beneficio_id',
    ];

    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'aluno_id');
    }

    public function benefit()
    {
        return $this->belongsTo(LegacyBenefit::class, 'aluno_beneficio_id');
    }
}
