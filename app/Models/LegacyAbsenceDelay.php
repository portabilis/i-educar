<?php

namespace App\Models;

use App\Models\Builders\LegacyAbsenceDelayBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Models\Enums\AbsenceDelayType;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAbsenceDelay extends LegacyModel
{
    use HasFiles;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = null;

    public const DELETED_AT = 'data_exclusao';

    protected $table = 'pmieducar.falta_atraso';

    protected $primaryKey = 'cod_falta_atraso';

    protected string $builder = LegacyAbsenceDelayBuilder::class;

    protected $fillable = [
        'ref_cod_escola',
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'tipo',
        'data_falta_atraso',
        'qtd_horas',
        'qtd_min',
        'justificada',
        'ref_cod_servidor_funcao',
    ];

    protected $casts = [
        'data_falta_atraso' => 'date',
    ];

    protected function typeName(): Attribute
    {
        return Attribute::make(
            get: fn () => AbsenceDelayType::tryFrom($this->tipo)?->name(),
        );
    }

    protected function justifyName(): Attribute
    {
        return Attribute::make(
            //no banco é salvo 0 como "Sim"
            get: fn () => $this->justificada === 0 ? 'Sim' : 'Não',
        );
    }

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
