<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacySchoolHistoryDiscipline extends LegacyModel
{
    public $timestamps = false;

    protected $table = 'pmieducar.historico_disciplinas';

    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_ref_cod_aluno');
    }
}
