<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class Place extends Model
{
    use DateSerializer;

    protected $fillable = [
        'city_id',
        'address',
        'number',
        'complement',
        'neighborhood',
        'postal_code',
        'latitude',
        'longitude',
    ];

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return HasMany<PersonHasPlace, $this>
     */
    public function personHasPlace(): HasMany
    {
        return $this->hasMany(PersonHasPlace::class);
    }
}
