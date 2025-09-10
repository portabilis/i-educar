<?php

namespace App\Models;

use App\Models\Builders\LegacyPersonBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

/**
 * @property int $idpes
 * @property string $name
 * @property string $nome
 * @property string $real_name
 */
class LegacyPerson extends LegacyModel
{
    /** @use HasBuilder<LegacyPersonBuilder> */
    use HasBuilder;

    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = 'data_rev';

    protected $table = 'cadastro.pessoa';

    protected $primaryKey = 'idpes';

    protected static string $builder = LegacyPersonBuilder::class;

    protected $fillable = [
        'idpes',
        'nome',
        'data_cad',
        'tipo',
        'situacao',
        'origem_gravacao',
        'operacao',
        'email',
        'slug',
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

    /**
     * @return HasMany<LegacyPhone, $this>
     */
    public function phones(): HasMany
    {
        return $this->hasMany(LegacyPhone::class, 'idpes', 'idpes');
    }

    /**
     * @return HasOne<LegacyPhone, $this>
     */
    public function phone(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(LegacyPhone::class, 'idpes', 'idpes')->where('fone', '<>', 0)->orderBy('tipo');
    }

    /**
     * @return HasOne<LegacyIndividual, $this>
     */
    public function individual(): HasOne
    {
        return $this->hasOne(LegacyIndividual::class, 'idpes', 'idpes');
    }

    /**
     * @return BelongsToMany<LegacyDeficiency, $this>
     */
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

    /**
     * @return HasOneThrough<Place, PersonHasPlace, $this>
     */
    public function place(): HasOneThrough
    {
        return $this->hasOneThrough( // @phpstan-ignore-line
            Place::class,
            PersonHasPlace::class,
            'person_id',
            'id',
            'idpes',
            'place_id'
        )->orderBy('type');
    }

    /**
     * @return HasOne<Employee, $this>
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'idpes');
    }

    /**
     * @return HasOne<LegacyEmployee, $this>
     */
    public function legacyEmployee()
    {
        return $this->hasOne(LegacyEmployee::class, 'ref_cod_pessoa_fj', 'idpes');
    }

    /**
     * @return BelongsToMany<LegacyDeficiency, $this>
     */
    public function considerableDeficiencies(): BelongsToMany
    {
        // @phpstan-ignore-next-line
        return $this->deficiencies()->where('desconsidera_regra_diferenciada', false);
    }

    /**
     * @return HasOne<LegacyStudent, $this>
     */
    public function student(): HasOne
    {
        return $this->hasOne(LegacyStudent::class, 'ref_idpes', 'idpes');
    }
}
