<?php

namespace App\Models;

use App\Models\Builders\LegacyOrganizationBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $fantasia
 *
 * @method static LegacyOrganizationBuilder query()
 */
class LegacyOrganization extends LegacyModel
{
    /** @use HasBuilder<LegacyOrganizationBuilder> */
    use HasBuilder;

    protected static string $builder = LegacyOrganizationBuilder::class;

    protected $table = 'cadastro.juridica';

    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = 'data_rev';

    protected $primaryKey = 'idpes';

    public array $legacy = [
        'id' => 'idpes',
        'fantasy' => 'fantasia',
    ];

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

    protected $attributes = [
        'origem_gravacao' => 'M',
        'operacao' => 'I',
    ];

    /**
     * {@inheritDoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('fantasia') && config('legacy.app.uppercase_names')) {
                $model->fantasia = Str::upper($model->fantasia);
            }
        });
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->fantasia
        );
    }

    protected function fantasia(): Attribute
    {
        return Attribute::set(fn (?string $value) => $value ?: null);
    }

    protected function capitalSocial(): Attribute
    {
        return Attribute::set(fn (?string $value) => $value ?: null);
    }

    protected function cnpj(): Attribute
    {
        return Attribute::set(fn ($value) => normalizaCnpj($value));
    }

    /**
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'idpes');
    }
}
