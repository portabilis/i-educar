<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyExternalAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.endereco_externo';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'tipo',
        'idtlog',
        'logradouro',
        'numero',
        'letra',
        'complemento',
        'bairro',
        'cep',
        'cidade',
        'sigla_uf',
        'reside_desde',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'bloco',
        'andar',
        'apartamento',
        'zona_localizacao',
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
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
        });
    }
}
