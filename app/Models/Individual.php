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

    public function whoUpdated()
    {
        return $this->belongsTo(Individual::class, 'who_updated', 'id');
    }

    public function whoCreated()
    {
        return $this->belongsTo(Individual::class, 'who_created', 'id');
    }

    public function whoDeleted()
    {
        return $this->belongsTo(Individual::class, 'who_deleted', 'id');
    }
}
