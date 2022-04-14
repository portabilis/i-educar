<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 * @property integer            cod_dispensa
 */
class LegacyDisciplineExemption extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.dispensa_disciplina';

    protected $primaryKey = 'cod_dispensa';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_disciplina',
        'ref_cod_escola',
        'ref_cod_serie',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_cod_tipo_dispensa',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'observacao',
        'cod_dispensa',
        'updated_at',
    ];

    protected $dates = [
        'data_cadastro',
        'data_exclusao',
        'updated_at'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    /**
     * @return BelongsTo
     */
    public function discipline()
    {
        return $this->belongsTo(LegacyDiscipline::class, 'ref_cod_disciplina');
    }

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LegacyExemptionType::class, 'ref_cod_tipo_dispensa');
    }

    /**
     * @return HasMany
     */
    public function stages()
    {
        return $this->hasMany(LegacyExemptionStage::class, 'ref_cod_dispensa', 'cod_dispensa');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'ref_usuario_cad');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }
}
