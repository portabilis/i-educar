<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public function student()
    {
        return $this->belogsTo(Student::class);
    }

    public function getStatusDescriptionAttribute()
    {
        return (new RegistrationStatus)->getDescriptiveValues()[(int) $this->status];
    }
}
