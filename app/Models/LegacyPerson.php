<?php

namespace App\Models;

use App\Models\Builders\LegacyPersonBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

/**
 * @property string $name
 */
class LegacyPerson extends LegacyModel
{
    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = 'data_rev';

    /**
     * @var string
     */
    protected $table = 'cadastro.pessoa';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    protected string $builder = LegacyPersonBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'nome',
        'data_cad',
        'tipo',
        'situacao',
        'origem_gravacao',
        'operacao',
        'email',
    ];

    public array $legacy = [
        'id' => 'idpes',
        'name' => 'nome',
    ];

    /**
     * {@inheritDoc}
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

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->idpes
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome
        );
    }

    protected function socialName(): Attribute
    {
        return Attribute::make(
            get: fn () => empty($this->individual->social_name) ? null : $this->individual->social_name
        );
    }

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->individual->social_name)) {
                    return $this->nome;
                }

                return $this->individual->social_name;
            }
        );
    }

    public function phones(): HasMany
    {
        return $this->hasMany(LegacyPhone::class, 'idpes', 'idpes');
    }

    public function phone(): HasOne
    {
        return $this->hasOne(LegacyPhone::class, 'idpes', 'idpes');
    }

    public function individual(): HasOne
    {
        return $this->hasOne(LegacyIndividual::class, 'idpes', 'idpes');
    }

    public function deficiencies(): BelongsToMany
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

    public function place(): HasOneThrough
    {
        return $this->hasOneThrough(
            Place::class,
            PersonHasPlace::class,
            'person_id',
            'id',
            'idpes',
            'place_id'
        )->orderBy('type');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'idpes');
    }

    public function considerableDeficiencies(): BelongsToMany
    {
        return $this->deficiencies()->where('desconsidera_regra_diferenciada', false);
    }
}
