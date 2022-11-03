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
    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = null;

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
        'idpes',
        'rg',
        'certidao_nascimento',
        'operacao',
        'origem_gravacao'
    ];

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
        });
    }
}
