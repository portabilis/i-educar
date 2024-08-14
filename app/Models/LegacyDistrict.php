<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Usar novo módulo de endereço
 */
class LegacyDistrict extends Model
{
    protected $table = 'public.distrito';

    protected $primaryKey = 'iddis';

    protected $fillable = [
        'idmun',
        'geom',
        'iddis',
        'nome',
        'cod_ibge',
        'idpes_rev',
        'data_rev',
        'data_cad',
        'idpes_cad',
        'origem_gravacao',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
    ];

    public $timestamps = false;

    /**
     * {@inheritDoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            /** @var LegacyDistrict $district */
            $district = LegacyDistrict::query()->whereKey($model->idmun)->first();

            $model->origem_gravacao = 'M';
            $model->data_cad = now();
            $model->operacao = 'I';
            $model->iddis = $district->getKey();
        });
    }
}
