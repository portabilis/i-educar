<?php

namespace App\Models;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
