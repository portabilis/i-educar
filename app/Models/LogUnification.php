<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogUnification extends Model
{
    public function type()
    {
        return $this->belongsTo(LogUnificationType::class, 'type_id', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }
}
