<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * LegacyCourse
 *
 * @property string $name Nome do curso
 */
class LegacyDocument extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.documento';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes', 'rg', 'certidao_nascimento', 'data_cad', 'operacao', 'origem_gravacao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
            $model->data_cad = now()->format('Y-m-d');
        });
    }
}
