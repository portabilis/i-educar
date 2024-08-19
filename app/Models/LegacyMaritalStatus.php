<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $ideciv
 */
class LegacyMaritalStatus extends Model
{
    protected $table = 'cadastro.estado_civil';

    protected $primaryKey = 'ideciv';

    protected $fillable = [
        'ideciv',
        'descricao',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(static function (self $legacyMaritalStatus) {
            $legacyMaritalStatus->ideciv = self::query()->max('ideciv') + 1;
        });
    }

    public $timestamps = false;

    /**
     * @return HasMany<LegacyIndividual, $this>
     */
    public function individuals(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'ideciv');
    }
}
