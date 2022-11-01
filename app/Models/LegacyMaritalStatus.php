<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyMaritalStatus extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.estado_civil';

    /**
     * @var string
     */
    protected $primaryKey = 'ideciv';

    /**
     * @var array
     */
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

    /**
     * @var bool
     */
    public $timestamps = false;

    public function individuals(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'ideciv');
    }
}
