<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacySchoolingDegree extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'cadastro.escolaridade';

    /**
     * @var string
     */
    protected $primaryKey = 'idesco';

    public static function boot()
    {
        parent::boot();
        static::creating(static function (self $legacySchoolingDegree) {
            $legacySchoolingDegree->idesco = self::query()->max('idesco') + 1;
        });
    }

    /**
     * @var array
     */
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

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'ref_idesco');
    }

    public function individuals(): HasMany
    {
        return $this->hasMany(LegacyIndividual::class, 'idesco');
    }
}
