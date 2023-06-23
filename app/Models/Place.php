<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use DateSerializer;

    /**
     * @var array
     */
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

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function personHasPlace(): HasMany
    {
        return $this->hasMany(PersonHasPlace::class);
    }
}
