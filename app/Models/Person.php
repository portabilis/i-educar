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

    public function whoCreated()
    {
        return $this->belongsTo(Individual::class, 'who_created', 'id');
    }

    public function whoUpdated()
    {
        return $this->belongsTo(Individual::class, 'who_updated', 'id');
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
