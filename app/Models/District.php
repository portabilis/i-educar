<?php

namespace App\Models;

use App\Models\Builders\DistrictBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;

#[
    Schema,
    Property(Type::INT, 'id', 'District ID', 1),
    Property(Type::INT, 'city_id', 'City ID', 1),
    Property(Type::STRING, 'name', 'District name', 'São Miguel'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class District extends Model
{
    use DateSerializer;
    use HasIbgeCode;
    use LegacyAttribute;

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = DistrictBuilder::Class;
    /**
     * @var array
     */
    protected $fillable = [
        'city_id', 'name', 'ibge_code',
    ];

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
