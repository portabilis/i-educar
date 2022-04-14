<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolClassInep extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_cod_turma';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma';

    protected $fillable = [
        'cod_turma',
        'cod_turma_inep',
        'nome_inep',
        'fonte',
        'created_at',
        'updated_at'
    ];

    public function getNumberAttribute()
    {
        return $this->cod_turma_inep;
    }

    /**
     * @return BelongsTo
     */
    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'cod_turma', 'cod_turma');
    }
}
