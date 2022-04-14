<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Religion extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }
}
