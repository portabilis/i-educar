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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public static function findByAbbreviation(string $abbreviation): ?self
    {
        return static::query()->where('abbreviation', $abbreviation)->first();
    }

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
