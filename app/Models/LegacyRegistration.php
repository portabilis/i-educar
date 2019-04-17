<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyRegistration
 *
 * @property int $id
 *
 */
class LegacyRegistration extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_matricula';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_aluno', 'data_cadastro', 'ano', 'ref_usuario_cad',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_matricula'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_matricula;
    }

    /**
     * @return boolean
     */
    public function getIsDependencyAttribute()
    {
        return $this->dependencia;
    }

    /**
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->ano;
    }

    /**
     * Relação com o aluno.
     *
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }

    /**
     * @return HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(LegacyEnrollment::class, 'ref_cod_matricula');
    }

    /**
     * @return HasOne
     */
    public function lastEnrollment()
    {
        $hasOne = $this->hasOne(LegacyEnrollment::class, 'ref_cod_matricula');

        $hasOne->getQuery()->orderByDesc('sequencial');

        return $hasOne;
    }
}
