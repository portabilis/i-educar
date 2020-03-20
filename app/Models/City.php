<?php

namespace App\Models;

use App\Models\Concerns\HasIbgeCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class City extends Model
{
    use HasIbgeCode;
    
    /**
     * @var array
     */
    protected $fillable = [
        'state_id', 'name', 'ibge_code',
    ];

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
