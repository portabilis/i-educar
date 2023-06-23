<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentInep
 *
 * @property LegacyStudent $student
 */
class StudentInep extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_cod_aluno';

    protected $fillable = [
        'cod_aluno',
        'cod_aluno_inep',
        'nome_inep',
        'fonte',
    ];

    public array $legacy = [
        'student_id' => 'cod_aluno',
        'number' => 'cod_aluno_inep',
        'name' => 'nome_inep',
        'font' => 'fonte',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'cod_aluno');
    }
}
