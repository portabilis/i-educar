<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @deprecated
 *
 * @property string $name
 */
class LegacyPersonAddress extends Model
{
    protected $table = 'cadastro.endereco_pessoa';

    protected $primaryKey = 'idpes';

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

    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyPersonAddress, $this>
     */
    public function person()
    {
        return $this->belongsTo(LegacyPersonAddress::class, 'idpes', 'idpes');
    }

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
