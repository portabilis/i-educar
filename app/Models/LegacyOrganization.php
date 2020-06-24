<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LegacyOrganization extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.juridica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'cnpj',
        'insc_estadual',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'idsis_rev',
        'idsis_cad',
        'fantasia',
        'capital_social',
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
            if (config('legacy.app.uppercase_names')) {
                $model->fantasia =  Str::upper($model->fantasia);
            }
        });
    }
}
