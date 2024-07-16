<?php

namespace App\Models;

use App\Models\Builders\CountryBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use DateSerializer;

    /** @use HasBuilder<CountryBuilder> */
    use HasBuilder;

    use HasIbgeCode;

    public const BRASIL = 45;

    /**
     * Builder dos filtros
     */
    protected static string $builder = CountryBuilder::class;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'ibge_code',
    ];

    /**
     * @return HasMany<State, $this>
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
