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

    public function whoCreated()
    {
        return $this->belongsTo(Individual::class, 'who_created', 'id');
    }

    public function whoDeleted()
    {
        return $this->belongsTo(Individual::class, 'who_deleted', 'id');
    }
}
