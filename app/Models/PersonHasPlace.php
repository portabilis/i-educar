<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property array<int, string> $fillable
 */
class PersonHasPlace extends Pivot
{
    protected $fillable = [
        'person_id',
        'place_id',
        'type',
    ];

    protected $relatedKey = 'person_id';

    protected $foreignKey = 'place_id';

    public $incrementing = true;

    /**
     * @return BelongsTo<Place, $this>
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
