<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LegacyOrganization extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'cadastro.juridica';

    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = 'data_rev';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'idpes',
        'fantasy' => 'fantasia',
    ];

    /**
     * @var array
     */
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

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'idpes');
    }
}
