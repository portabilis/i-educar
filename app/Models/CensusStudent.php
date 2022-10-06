<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CensusStudent extends Model
{
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
