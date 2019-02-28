<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }
}
