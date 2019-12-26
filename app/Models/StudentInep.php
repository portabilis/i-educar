<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentInep
 *
 * @property LegacyStudent $student
 *
 */
class StudentInep extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_cod_aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno';

    protected $fillable = ['cod_aluno', 'cod_aluno_inep'];

    public function getNumberAttribute()
    {
        return $this->cod_aluno_inep;
    }

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'cod_aluno', 'cod_aluno');
    }
}
