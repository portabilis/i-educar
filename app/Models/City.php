<?php

namespace App\Models;

use App\Models\Builders\CityBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class City extends Model
{
    use DateSerializer;

    /** @use HasBuilder<CityBuilder> */
    use HasBuilder;

    use HasIbgeCode;

    protected $fillable = [
        'state_id',
        'name',
        'ibge_code',
    ];

    /**
     * Builder dos filtros
     */
    protected static string $builder = CityBuilder::class;

    protected function nameWithState(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name . '-' . $this->state?->abbreviation
        );
    }

    /**
     * @return BelongsTo<State, $this>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return HasMany<District, $this>
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * @return HasMany<Place, $this>
     */
    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    /**
     * @param string $name
     * @return Builder<City>
     */
    public static function queryFindByName($name): Builder
    {
        $name = str_replace('\'', '\'\'', $name);

        return static::query()->whereRaw(
            "translate(upper(name),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$name}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')"
        );
    }

    /**
     * @param int $id
     */
    public static function getNameById($id): string
    {
        $city = static::query()->find($id);

        return $city->name ?? '';
    }
}
