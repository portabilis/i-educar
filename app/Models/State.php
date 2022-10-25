<?php

namespace App\Models;

use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class State extends Model
{
    use DateSerializer;
    use HasIbgeCode;

    /**
     * @var array
     */
    protected $fillable = [
        'country_id', 'name', 'abbreviation', 'ibge_code',
    ];

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
     */
    public static function findByAbbreviation($abbreviation): self|null
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
    public static function getNameByAbbreviation($abbreviation): string
    {
        $state = static::findByAbbreviation($abbreviation);

        return $state->name ?? '';
    }
}
