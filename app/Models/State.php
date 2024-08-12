<?php

namespace App\Models;

use App\Models\Builders\StateBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property array<int, string> $fillable
 */
class State extends Model
{
    use DateSerializer;

    /** @use HasBuilder<StateBuilder> */
    use HasBuilder;

    use HasIbgeCode;

    protected $fillable = [
        'country_id',
        'name',
        'abbreviation',
        'ibge_code',
    ];

    /**
     * Builder dos filtros
     */
    protected static string $builder = StateBuilder::class;

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return HasMany<City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public static function findByAbbreviation(string $abbreviation): ?self
    {
        /** @var self|null */
        return static::query()->where('abbreviation', $abbreviation)->first();
    }

    /**
     * @return Collection<string, string>
     */
    public static function getListKeyAbbreviation(): Collection
    {
        return static::query()->orderBy('name')->pluck('name', 'abbreviation');
    }

    public static function getNameByAbbreviation(?string $abbreviation): string
    {
        if ($abbreviation === null) {
            return '';
        }

        $state = static::findByAbbreviation($abbreviation);

        return $state->name ?? '';
    }
}
