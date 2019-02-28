<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function mother()
    {
        return $this->belongsTo(Individual::class, 'mother_individual_id', 'id');
    }

    public function father()
    {
        return $this->belongsTo(Individual::class, 'father_individual_id', 'id');
    }

    public function guardian()
    {
        return $this->belongsTo(Individual::class, 'guardian_individual_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    public function getGenderDescriptionAttribute()
    {
        return (new Gender)->getDescriptiveValues()[(int) $this->gender];
    }

    public function getNationalityDescriptionAttribute()
    {
        return (new Nationality)->getDescriptiveValues()[(int) $this->nationality];
    }

    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin)->getDescriptiveValues()[(int) $this->registry_origin];
    }

    public function getLocalizationZoneDescriptionAttribute()
    {
        return (new LocalizationZone)->getDescriptiveValues()[(int) $this->localization_zone];
    }
}
