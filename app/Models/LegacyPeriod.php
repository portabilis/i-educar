<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyPeriod extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_turno';

    /**
     * @var array
     */
    protected $fillable = [
        'nome',
        'ativo',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome
        );
    }

    public function schoolClass(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'turma_turno_id');
    }

    public function schoolClassTeacher(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacher::class, 'turno_id');
    }
}
