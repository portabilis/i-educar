<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Builder;

class LegacyRole extends LegacyModel
{
    use LegacySoftDeletes;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use HasInstitution;

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

    public function getIdAttribute(): int
    {
        return $this->cod_funcao;
    }
}
