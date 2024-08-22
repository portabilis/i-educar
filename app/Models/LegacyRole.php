<?php

namespace App\Models;

use App\Models\Builders\LegacyRoleBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 * @property int $cod_funcao
 * @property string $nm_funcao
 */
class LegacyRole extends LegacyModel
{
    /** @use HasBuilder<LegacyRoleBuilder> */
    use HasBuilder;

    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;

    protected $table = 'pmieducar.funcao';

    protected $primaryKey = 'cod_funcao';

    protected static string $builder = LegacyRoleBuilder::class;

    protected $fillable = [
        'nm_funcao',
        'abreviatura',
        'professor',
    ];

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

    /**
     * @return HasMany<LegacyEmployeeRole, $this>
     */
    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_funcao');
    }
}
