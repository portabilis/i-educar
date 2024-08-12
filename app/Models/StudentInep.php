<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentInep
 *
 * @property LegacyStudent $student
 * @property array<int, string> $fillable
 * @property string $number
 */
class StudentInep extends LegacyModel
{
    protected $table = 'modules.educacenso_cod_aluno';

    protected $fillable = [
        'cod_aluno',
        'cod_aluno_inep',
        'nome_inep',
        'fonte',
    ];

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'student_id' => 'cod_aluno',
        'number' => 'cod_aluno_inep',
        'name' => 'nome_inep',
        'font' => 'fonte',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'cod_aluno');
    }
}
