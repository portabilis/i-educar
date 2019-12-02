<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudent extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_idpes', 'data_cadastro', 'tipo_responsavel',
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
            $model->data_cadastro = now();
        });
    }

    /**
     * @return BelongsTo
     */
    public function individual()
    {
        return $this->belongsTo(LegacyIndividual::class, 'ref_idpes');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    public function registrations()
    {
        return $this->hasMany(LegacyRegistration::class, 'ref_cod_aluno');
    }
}
