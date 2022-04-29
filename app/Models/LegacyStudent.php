<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * @return BelongsToMany
     */
    public function guardians()
    {
        return $this->belongsToMany(
            LegacyPerson::class,
            'pmieducar.responsaveis_aluno',
            'ref_cod_aluno',
            'ref_idpes',
            'cod_aluno',
            'idpes'
        );
    }

    public function getInepNumberAttribute()
    {
        return $this->inep ? $this->inep->number : null;
    }

    public function getStateRegistrationIdAttribute()
    {
        return $this->aluno_estado_id;
    }

    public function setStateRegistrationIdAttribute($value)
    {
        $this->aluno_estado_id = $value;
    }

    public function inep()
    {
        return $this->hasOne(StudentInep::class, 'cod_aluno', 'cod_aluno');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('aluno.ativo', 1);
    }

    public function scopeMale(Builder $query)
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query)
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'F');
    }
}
