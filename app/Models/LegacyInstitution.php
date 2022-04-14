<?php

namespace App\Models;

use App\Services\RelocationDate\RelocationDateProvider;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyInstitution
 *
 * @property string   $name            Nome da instituição
 * @property string   $city            Noda da cidade da instituição
 * @property string   $state           Sigla do estado da instituição
 * @property DateTime $relocation_date Data base para remanejamento
 * @property DateTime $educacenso_date Data de corte do Educacenso
 */
class LegacyInstitution extends Model implements RelocationDateProvider
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.instituicao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_instituicao';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad', 'ref_idtlog', 'ref_sigla_uf', 'cep', 'cidade', 'bairro', 'logradouro', 'nm_responsavel',
        'data_cadastro', 'nm_instituicao',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'data_base_remanejamento', 'data_educacenso',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function generalConfiguration(): HasOne
    {
        return $this->hasOne(LegacyGeneralConfiguration::class, 'ref_cod_instituicao', 'cod_instituicao');
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_instituicao;
    }

    /**
     * @return string
     */
    public function getCityAttribute()
    {
        return $this->cidade;
    }

    /**
     * @return string
     */
    public function getStateAttribute()
    {
        return $this->ref_sigla_uf;
    }

    /**
     * @return DateTime
     */
    public function getRelocationDateAttribute()
    {
        return $this->data_base_remanejamento;
    }

    /**
     * @return DateTime
     */
    public function getEducacensoDateAttribute()
    {
        return $this->data_educacenso;
    }

    /**
     * Indica se os campos do Censo são obrigatórios.
     *
     * @return bool
     */
    public function isMandatoryCensoFields()
    {
        return boolval($this->obrigar_campos_censo);
    }

    public function getIdAttribute()
    {
        return $this->cod_instituicao;
    }

    /**
     * @return bool
     */
    public function getAllowRegistrationOutAcademicYearAttribute()
    {
        return boolval($this->permitir_matricula_fora_periodo_letivo);
    }

    /**
     * @return HasMany
     */
    public function schools()
    {
        return $this->hasMany(LegacySchool::class, 'ref_cod_instituicao', 'cod_instituicao');
    }

    public function getRelocationDate()
    {
        if ($this->getRelocationDateAttribute()) {
            return $this->getRelocationDateAttribute()->format('Y-m-d');
        }

        return null;
    }
}
