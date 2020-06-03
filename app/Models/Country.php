<?php

namespace App\Models;

use App\Models\Concerns\HasIbgeCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasIbgeCode;

    const BRASIL = 45;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'ibge_code',
    ];

    /**
     * @return HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
