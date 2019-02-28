<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    public function getGuardianTypeDescriptionAttribute()
    {
        return (new GuardianType)->getDescriptiveValues()[(int) $this->guardian_type];
    }

    public function getTransportationProviderDescriptionAttribute()
    {
        return (new TransportationProvider)->getDescriptiveValues()[(int) $this->transportation_provider];
    }

    public function getTransportationVehicleTypeDescriptionAttribute()
    {
        return (new TransportationVehicleType)->getDescriptiveValues()[(int) $this->transportation_vehicle_type];
    }
}
