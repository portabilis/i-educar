<?php

namespace App\Models;

use App\Models\Builders\CountryBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;

    public const BRASIL = 45;

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = CountryBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'ibge_code',
    ];

    /**
     * @return HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
