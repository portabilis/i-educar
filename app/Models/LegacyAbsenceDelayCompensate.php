<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacyAbsenceDelayCompensate extends LegacyModel
{
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = null;

    public const DELETED_AT = 'data_exclusao';

    protected $table = 'pmieducar.falta_atraso_compensado';

    protected $primaryKey = 'cod_compensado';

    protected $fillable = [
        'ref_cod_escola',
        'ref_ref_cod_instituicao',
        'ref_cod_servidor',
        'data_inicio',
        'data_fim',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    /**
     * @return BelongsTo<LegacyInstitution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_ref_cod_instituicao');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ref_cod_servidor');
    }
}
