<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Fisica.
 *
 * @package namespace App\Entities;
 */
class LegacyIndividual extends EloquentBaseModel implements Transformable
{
    use TransformableTrait;

    /**
     * @var string
     */
    protected $table = 'cadastro.fisica';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes', 'data_cad', 'operacao', 'origem_gravacao',
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

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'idpes', 'idpes');
    }

    /**
     * @param string $cpf
     *
     * @return $this
     */
    public static function findByCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = intval($cpf);

        return static::query()->where('cpf', $cpf)->first();
    }
}
