<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyCity extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.municipio';

    /**
     * @var string
     */
    protected $primaryKey = 'idmun';

    /**
     * @var array
     */
    protected $fillable = [
        'idmun',
        'nome',
        'sigla_uf',
        'area_km2',
        'idmreg',
        'idasmun',
        'cod_ibge',
        'geom',
        'tipo',
        'idmun_pai',
        'idpes_rev',
        'idpes_cad',
        'data_rev',
        'data_cad',
        'origem_gravacao',
        'operacao',
        'nome_limpo',
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

    /**
     * @return HasMany
     */
    public function districts()
    {
        return $this->hasMany(LegacyDistrict::class, 'idmun', 'idmun');
    }
}
