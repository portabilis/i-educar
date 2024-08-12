<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 * @property array<string, string> $legacy
 */
class LegacySchoolingDegree extends LegacyModel
{
    protected $table = 'cadastro.escolaridade';

    protected $primaryKey = 'idesco';

    public static function boot()
    {
        parent::boot();
        static::creating(static function (self $legacySchoolingDegree) {
            $legacySchoolingDegree->idesco = self::query()->max('idesco') + 1;
        });
    }

    protected $fillable = [
        'idesco',
        'descricao',
        'escolaridade',
    ];

    public array $legacy = [
        'id' => 'idesco',
        'description' => 'descricao',
        'schooling' => 'escolaridade',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany<LegacyEmployee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'ref_idesco');
    }

    /**
     * @return HasMany<LegacyIndividual, $this>
     */
    public function individuals(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'idesco');
    }
}
