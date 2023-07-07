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

class City extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;

    /**
     * @var array
     */
    protected $fillable = [
        'state_id',
        'name',
        'ibge_code',
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
     * @return Builder
     */
    public static function queryFindByName($name)
    {
        $name = str_replace('\'', '\'\'', $name);

        return static::query()->whereRaw(
            "translate(upper(name),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$name}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')"
        );
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getNameById($id)
    {
        $city = static::query()->find($id);

        return $city->name ?? '';
    }
}
