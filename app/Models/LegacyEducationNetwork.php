<?php

namespace App\Models;

use App\Models\Builders\LegacyEducationNetworkBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegacyEducationNetwork extends Model
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_rede_ensino';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola_rede_ensino';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacyEducationNetworkBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'cod_escola_rede_ensino',
        'name' => 'nm_rede'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola_rede_ensino',
        'ref_usuario_cad',
        'nm_rede',
        'data_cadastro',
        'ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->cod_escola_rede_ensino;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nm_rede;
    }

    /**
     * Filtra por Instituição
     *
     * @param Builder $query
     * @param int $institution
     * @return void
     */
    public function scopeWhereInstitution(Builder $query, int $institution): void
    {
        $query->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por ativo
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('ativo', 1);
    }

    /**
     * Ordena por nome
     *
     * @param Builder $query
     * @param string $direction
     * @return void
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('nm_rede',$direction);
    }

}
