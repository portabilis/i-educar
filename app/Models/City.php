<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * City
 *
 * @property int    $id
 * @property string $name
 * @property string $formatted_name
 * @property string $state_id
 * @property int    $ibge_code
 * @property int    $parent_id
 * @property int    $created_by
 * @property int    $updated_by
 * @property int    $created_at
 * @property int    $updated_at
 * @property int    $registry_origin
 */
class City extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'ibge_code' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * @return string
     */
    public function getFormattedNameAttribute()
    {
        return "{$this->name}/{$this->state_id}";
    }

    /**
     * @return BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(City::class, 'parent_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    /**
     * @return string
     */
    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin)->getDescriptiveValues()[(int) $this->registry_origin];
    }
}
