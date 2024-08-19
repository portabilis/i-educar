<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $fantasia
 */
class LegacyOrganization extends LegacyModel
{
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

    /**
     * {@inheritDoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (config('legacy.app.uppercase_names')) {
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

    /**
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'idpes');
    }
}
