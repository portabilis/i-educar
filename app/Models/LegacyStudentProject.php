<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'ref_cod_aluno',
        'data_inclusao',
        'data_desligamento',
        'ref_cod_projeto',
        'turno',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * @return BelongsTo<LegacyProject, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(LegacyProject::class, 'ref_cod_projeto');
    }
}
