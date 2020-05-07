<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Fisica.
 *
 * @package namespace App\Entities;
 */
class LegacyIndividual extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'cadastro.fisica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'data_nascimento',
        'zona_localizacao_censo',
        'idpes',
        'data_nasc',
        'sexo',
        'idpes_mae',
        'idpes_pai',
        'idpes_responsavel',
        'idesco',
        'ideciv',
        'idpes_con',
        'data_uniao',
        'data_obito',
        'nacionalidade',
        'idpais_estrangeiro',
        'data_chegada_brasil',
        'idmun_nascimento',
        'ultima_empresa',
        'idocup',
        'nome_mae',
        'nome_pai',
        'nome_conjuge',
        'nome_responsavel',
        'justificativa_provisorio',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'ref_cod_sistema',
        'cpf',
        'ref_cod_religiao',
        'nis_pis_pasep',
        'sus',
        'ocupacao',
        'empresa',
        'pessoa_contato',
        'renda_mensal',
        'data_admissao',
        'ddd_telefone_empresa',
        'telefone_empresa',
        'falecido',
        'ativo',
        'ref_usuario_exc',
        'data_exclusao',
        'zona_localizacao_censo',
        'tipo_trabalho',
        'local_trabalho',
        'horario_inicial_trabalho',
        'horario_final_trabalho',
        'nome_social',
        'pais_residencia',
        'localizacao_diferenciada',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsToMany
     */
    public function race()
    {
        return $this->belongsToMany(
            LegacyRace::class,
            'cadastro.fisica_raca',
            'ref_idpes',
            'ref_cod_raca'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function deficiency()
    {
        return $this->belongsToMany(
            LegacyDeficiency::class,
            'cadastro.fisica_deficiencia',
            'ref_idpes',
            'ref_cod_deficiencia'
        );
    }

    /**
     * @return HasOne
     */
    public function person()
    {
        return $this->hasOne(LegacyPerson::class, 'idpes', 'idpes');
    }

    /**
     * @return HasOne
     */
    public function document()
    {
        return $this->hasOne(LegacyDocument::class, 'idpes');
    }

    /**
     * @return HasOne
     */
    public function picture()
    {
        return $this->hasOne(LegacyIndividualPicture::class, 'idpes');
    }

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->data_cad = now();
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
            $model->pais_residencia = $model->pais_residencia ?? 76;
        });
    }

    /**
     * @param string $cpf
     *
     * @return $this
     */
    public static function findByCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = intval($cpf);

        return static::query()->where('cpf', $cpf)->first();
    }
}
