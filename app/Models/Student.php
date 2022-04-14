<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function census()
    {
        return $this->hasOne(CensusStudent::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    public function getGuardianTypeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->individual->father) {
            return GuardianType::FATHER;
        }

        if ($this->individual->mother) {
            return GuardianType::MOTHER;
        }

        if ($this->individual->guardian) {
            return GuardianType::OTHER;
        }

        return null;
    }

    public function getGuardianTypeDescriptionAttribute()
    {
        return (new GuardianType())->getDescriptiveValues()[(int) $this->guardian_type];
    }

    public function getTransportationProviderDescriptionAttribute()
    {
        return (new TransportationProvider())->getDescriptiveValues()[(int) $this->transportation_provider];
    }

    public function getTransportationVehicleTypeDescriptionAttribute()
    {
        return (new TransportationVehicleType())->getDescriptiveValues()[(int) $this->transportation_vehicle_type];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function unification()
    {
        return $this->morphOne(LogUnification::class, 'main', 'type', 'main_id');
    }
}
