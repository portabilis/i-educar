<?php

namespace App\Models;

use App\Models\Builders\LegacyActiveLookingBuilder;
use Carbon\Carbon;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LegacyActiveLooking
 *
 * @property LegacyRegistration     $registration
 * @property Carbon                 $data_inicio
 * @property Carbon                 $data_fim
 * @property int                 $result
 */
class LegacyActiveLooking extends LegacyModel
{
    /** @use HasBuilder<LegacyActiveLookingBuilder> */
    use HasBuilder;

    use HasFiles;
    use SoftDeletes;

    protected $table = 'pmieducar.busca_ativa';

    protected static string $builder = LegacyActiveLookingBuilder::class;

    protected $fillable = [
        'ref_cod_matricula',
        'data_inicio',
        'data_fim',
        'observacoes',
        'resultado_busca_ativa',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public array $legacy = [
        'registration_id' => 'ref_cod_matricula',
        'start' => 'data_inicio',
        'end' => 'data_fim',
        'obs' => 'observacoes',
        'result' => 'resultado_busca_ativa',
    ];

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    public function getStartDate(): ?string
    {
        return $this->data_inicio ? $this->data_inicio->format('Y-m-d') : null;
    }

    public function getEndDate(): ?string
    {
        return $this->data_fim ? $this->data_fim->format('Y-m-d') : null;
    }

    protected function shortSituation(): Attribute
    {
        return Attribute::make(
            get: fn () => ActiveLooking::getShortDescriptiveValues()[$this->result] ?? null
        );
    }
}
