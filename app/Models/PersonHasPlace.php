<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonHasPlace extends Pivot
{
    /**
     * @var array
     */
    protected $fillable = [
        'person_id',
        'place_id',
        'type',
    ];

    protected $relatedKey = 'person_id';

    protected $foreignKey = 'place_id';

    public $incrementing = true;

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
