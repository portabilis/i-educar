<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';

    public function individual()
    {
        return $this->hasOne(Individual::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    public function getTypeDescriptionAttribute()
    {
        return (new PersonType)->getDescriptiveValues()[(int) $this->type];
    }

    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin)->getDescriptiveValues()[(int) $this->registry_origin];
    }
}
