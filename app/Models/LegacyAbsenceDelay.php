<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAbsenceDelay extends LegacyModel
{
    use LegacySoftDeletes;
    use HasLegacyUserAction;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;
    public const DELETED_AT = 'data_exclusao';

    protected $table = 'pmieducar.falta_atraso';

    protected $primaryKey = 'cod_falta_atraso';

    protected $fillable = [
        'ref_cod_escola',
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'tipo',
        'data_falta_atraso',
        'qtd_horas',
        'qtd_min',
        'justificada',
        'ref_cod_servidor_funcao'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_ref_cod_instituicao');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor');
    }

    public function employeeRole(): BelongsTo
    {
        return $this->belongsTo(LegacyEmployeeRole::class, 'ref_cod_servidor_funcao');
    }
}
