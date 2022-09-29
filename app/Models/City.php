<?php

namespace App\Models;

use App\Models\Builders\CityBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;

#[
    Schema,
    Property(Type::INT, 'id', 'City ID', 1),
    Property(Type::INT, 'state_id', 'State ID', 1),
    Property(Type::STRING, 'name', 'City name', 'Francisco Beltrão'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class City extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;

    /**
     * @var array
     */
    protected $fillable = [
        'state_id', 'name', 'ibge_code',
    ];

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = CityBuilder::class;

    /**
     * @return BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return HasMany
     */
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    /**
     * @return HasMany
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**
     * @param string $name
     *
     * @return Builder
     */
    public static function queryFindByName($name)
    {
        return static::query()->whereRaw("translate(upper(name),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$name}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')");
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getNameById($id)
    {
        $city = static::query()->find($id);

        return $city->name ?? '';
    }

    /**
     * @param string $abbreviation
     *
     * @return Collection
     */
    public static function getListByAbbreviation($abbreviation)
    {
        return static::query()->whereHas('state', function ($query) use ($abbreviation) {
            $query->where('abbreviation', $abbreviation);
        })->orderBy('name')->pluck('name', 'id');
    }
}
