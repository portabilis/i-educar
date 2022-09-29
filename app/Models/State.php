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
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;

#[
    Schema,
    Property(Type::INT, 'id', 'State ID', 1),
    Property(Type::INT, 'country_id', 'Country ID', 1),
    Property(Type::STRING, 'name', 'State name', 'Paraná'),
    Property(Type::STRING, 'abbreviation', 'Name Abbreviation', 'PR'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class State extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;


    /**
     * @var array
     */
    protected $fillable = [
        'country_id', 'name', 'abbreviation', 'ibge_code',
    ];

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = StateBuilder::class;

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
