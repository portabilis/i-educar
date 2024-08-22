<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $nome
 */
class LegacyPeriod extends Model
{
    protected $table = 'pmieducar.turma_turno';

    protected $fillable = [
        'nome',
        'ativo',
    ];

    public $timestamps = false;

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nome
        );
    }

    /**
     * @return HasMany<LegacySchoolClass, $this>
     */
    public function schoolClass(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'turma_turno_id');
    }

    /**
     * @return HasMany<LegacySchoolClassTeacher, $this>
     */
    public function schoolClassTeacher(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacher::class, 'turno_id');
    }
}
