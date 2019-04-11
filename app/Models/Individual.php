<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Individual extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $casts = [
        'gender' => 'integer',
        'nationality' => 'integer',
        'registry_origin' => 'integer',
        'localization_zone' => 'integer',
    ];

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
        return (new Gender)->getDescriptiveValues()[$this->gender];
    }

    public function getNationalityDescriptionAttribute()
    {
        return (new Nationality)->getDescriptiveValues()[$this->nationality];
    }

    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin)->getDescriptiveValues()[$this->registry_origin];
    }

    public function getLocalizationZoneDescriptionAttribute()
    {
        return (new LocalizationZone)->getDescriptiveValues()[$this->localization_zone];
    }

    public function getRealNameAttribute()
    {
        if (empty($this->social_name)) {
            return $this->person->name;
        }

        return $this->social_name;
    }

    public function getMotherNameAttribute($value)
    {
        if ($this->mother) {
            return $this->mother->real_name;
        }

        return $value;
    }

    public function getFatherNameAttribute($value)
    {
        if ($this->father) {
            return $this->father->real_name;
        }

        return $value;
    }

    public function getGuardianNameAttribute($value)
    {
        if ($this->guardian) {
            return $this->guardian->real_name;
        }

        return $value;
    }
}
