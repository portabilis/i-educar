<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin)->getDescriptiveValues()[(int) $this->registry_origin];
    }
}
