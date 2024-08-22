<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<int, string> $fillable
 */
class Religion extends Model
{
    use SoftDeletes;

    protected $table = 'pmieducar.religions';

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<LegacyIndividual, $this>
     */
    public function individual(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'ref_cod_religiao');
    }
}
