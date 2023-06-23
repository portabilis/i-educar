<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Religion extends Model
{
    use SoftDeletes;

    protected $table = 'pmieducar.religions';

    protected $fillable = [
        'name',
    ];

    public function individual(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'ref_cod_religiao');
    }
}
