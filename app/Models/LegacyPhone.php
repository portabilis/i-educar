<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyPhone extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.fone_pessoa';

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
        'ddd',
        'fone',
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
