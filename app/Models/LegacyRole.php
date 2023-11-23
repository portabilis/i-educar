<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyRole extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    protected $table = 'pmieducar.funcao';

    protected $primaryKey = 'cod_funcao';

    protected $fillable = [
        'nm_funcao',
        'abreviatura',
        'professor',
    ];

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('ativo', 1);
    }

    public function scopeProfessor(Builder $query): Builder
    {
        return $query->where('professor', 1);
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_funcao
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nm_funcao
        );
    }

    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_funcao');
    }
}
