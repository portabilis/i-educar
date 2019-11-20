<?php

namespace App\Models;

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
        'idpes', 'data_cad', 'operacao', 'origem_gravacao', 'sexo', 'data_nascimento', 'idpes_mae', 'idpes_pai',
        'nacionalidade', 'idpais_estrangeiro', 'idmun_nascimento', 'cpf',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person()
    {
        return $this->hasOne(LegacyPerson::class, 'idpes', 'idpes');
    }
}
