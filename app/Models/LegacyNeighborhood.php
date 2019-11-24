<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyNeighborhood extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.bairro';


    /**
     * @var string
     */
    protected $primaryKey = 'idbai';

    /**
     * @var array
     */
    protected $fillable = [
        'idmun',
        'geom',
        'idbai',
        'nome',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'zona_localizacao',
        'iddis',
        'idsetorbai',
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
            $model->origem_gravacao = 'M';
            $model->data_cad = now();
            $model->operacao = 'I';
        });
    }
}
