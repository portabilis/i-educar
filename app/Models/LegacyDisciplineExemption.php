<?php

namespace App\Models;

use App\Models\Builders\LegacyDisciplineExemptionBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 * @property int            $cod_dispensa
 */
class LegacyDisciplineExemption extends LegacyModel
{
    /** @use HasBuilder<LegacyDisciplineExemptionBuilder> */
    use HasBuilder;

    use HasLegacyUserAction;
    use LegacySoftDeletes;

    public const CREATED_AT = 'data_cadastro';

    protected $table = 'pmieducar.dispensa_disciplina';

    protected $primaryKey = 'cod_dispensa';

    protected static string $builder = LegacyDisciplineExemptionBuilder::class;

    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_disciplina',
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_cod_tipo_dispensa',
        'data_exclusao',
        'observacao',
        'cod_dispensa',
        'batch',
    ];

    public array $legacy = [
        'id' => 'cod_dispensa',
        'registration_id' => 'ref_cod_matricula',
        'discipline_id' => 'ref_cod_disciplina',
        'school_id' => 'ref_cod_escola',
        'grade_id' => 'ref_cod_serie',
        'exemption_type_id' => 'ref_cod_tipo_dispensa',
        'observation' => 'observacao',
        'created_at' => 'data_cadastro',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
    ];

    protected $casts = [
        'updated_at' => 'date:d/m/Y H:i:s',
        'data_exclusao' => 'datetime',
    ];

    /**
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    /**
     * @return BelongsTo<LegacyDiscipline, $this>
     */
    public function discipline(): BelongsTo
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }

    /**
     * @return BelongsTo<LegacyExemptionType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LegacyExemptionType::class, 'ref_cod_tipo_dispensa');
    }

    /**
     * @return HasMany<LegacyExemptionStage, $this>
     */
    public function stages(): HasMany
    {
        return $this->hasMany(LegacyExemptionStage::class, 'ref_cod_dispensa', 'cod_dispensa');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_cad');
    }

    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }
}
