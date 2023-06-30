<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerRole extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function schoolManagers(): HasMany
    {
        return $this->hasMany(SchoolManager::class, 'role_id');
    }
}
