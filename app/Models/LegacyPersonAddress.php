<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyPersonAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.endereco_pessoa';

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
        'cep',
        'idlog',
        'numero',
        'letra',
        'complemento',
        'reside_desde',
        'idbai',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'bloco',
        'andar',
        'apartamento',
        'observacoes',
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
