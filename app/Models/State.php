<?php

namespace App\Models;

use App\Models\Builders\StateBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class State extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;

    /**
     * @var array
     */
    protected $fillable = [
        'country_id',
        'name',
        'abbreviation',
        'ibge_code',
    ];

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = StateBuilder::class;

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * @param string $abbreviation
     *
     * @return State|null
     */
    public static function findByAbbreviation(string $abbreviation): self|null
    {
        return static::query()->where('abbreviation', $abbreviation)->first();
    }

    /**
     * @return Collection
     */
    public static function getListKeyAbbreviation(): Collection
    {
        return static::query()->orderBy('name')->pluck('name', 'abbreviation');
    }

    /**
     * @param string $abbreviation
     *
     * @return string
     */
    public static function getNameByAbbreviation(string|null $abbreviation): string
    {
        if ($abbreviation === null) {
            return  '';
        }

        $state = static::findByAbbreviation($abbreviation);

        return $state->name ?? '';
    }
}
