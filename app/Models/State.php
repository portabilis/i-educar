<?php

namespace App\Models;

use App\Models\Concerns\HasIbgeCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class State extends Model
{
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
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * @param string $abbreviation
     *
     * @return $this
     */
    public static function findByAbbreviation($abbreviation)
    {
        return static::query()->where('abbreviation', $abbreviation)->first();
    }

    /**
     * @return Collection
     */
    public static function getListKeyAbbreviation()
    {
        return static::query()->orderBy('name')->pluck('name', 'abbreviation');
    }

    /**
     * @param string $abbreviation
     *
     * @return string
     */
    public static function getNameByAbbreviation($abbreviation)
    {
        $state = static::findByAbbreviation($abbreviation);

        return $state->name ?? '';
    }
}
