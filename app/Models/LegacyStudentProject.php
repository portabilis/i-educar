<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudentProject extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'pmieducar.projeto_aluno';

    protected $fillable = [
        'ref_cod_aluno',
        'data_inclusao',
        'data_desligamento',
        'ref_cod_projeto',
        'turno',
    ];

    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    public function project()
    {
        return $this->belongsTo(LegacyProject::class, 'ref_cod_projeto');
    }
}
