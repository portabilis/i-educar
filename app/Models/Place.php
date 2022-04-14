<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
