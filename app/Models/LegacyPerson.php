<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property string $name
 */
class LegacyPerson extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.pessoa';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'nome', 'data_cad', 'tipo', 'situacao', 'origem_gravacao', 'operacao', 'email'
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
            $model->situacao = 'I';
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
            $model->slug = Str::lower(Str::slug($model->nome, ' '));

            if (config('legacy.app.uppercase_names')) {
                $model->nome = Str::upper($model->nome);
            }
        });
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nome;
    }

    /**
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne(LegacyPersonAddress::class, 'idpes', 'idpes');
    }

    /**
     * @return HasMany
     */
    public function phone()
    {
        return $this->hasMany(LegacyPhone::class, 'idpes', 'idpes');
    }

    /**
     * @return HasOne
     */
    public function individual()
    {
        return $this->hasOne(LegacyIndividual::class, 'idpes', 'idpes');
    }

    /**
     * @return BelongsToMany
     */
    public function deficiencies()
    {
        return $this->belongsToMany(
            LegacyDeficiency::class,
            'cadastro.fisica_deficiencia',
            'ref_idpes',
            'ref_cod_deficiencia',
            'idpes',
            'cod_deficiencia'
        );
    }

    /**
     * @return HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'idpes');
    }

    /**
     * @return BelongsToMany
     */
    public function considerableDeficiencies()
    {
        return $this->deficiencies()->where('desconsidera_regra_diferenciada', false);
    }
}
