<?php

namespace App\Models;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
