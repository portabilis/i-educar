<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyPostalCodeAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'urbano.cep_logradouro';

    /**
     * @var string
     */
    protected $primaryKey = 'idlog';

    /**
     * @var array
     */
    protected $fillable = [
        'cep',
        'idlog',
        'nroini',
        'nrofin',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
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
