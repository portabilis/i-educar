<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string $name
 */
class LegacyPerson extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'cadastro.pessoa';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'nome', 'data_cad', 'tipo', 'situacao', 'origem_gravacao', 'operacao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->data_cad = now();
            $model->situacao = 'I';
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
        });
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nome;
    }

    /**
     * @return BelongsToMany
     */
    public function deficiencies()
    {
        return $this->belongsToMany(
            LegacyDeficiency::class,
            'cadastro.fisica_deficiencia',
            'ref_idpes',
            'ref_cod_deficiencia',
            'idpes',
            'cod_deficiencia'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function considerableDeficiencies()
    {
        return $this->deficiencies()->where('desconsidera_regra_diferenciada', false);
    }
}
