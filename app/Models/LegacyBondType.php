<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 */
class LegacyBondType extends Model
{
    /**
     * @var string
     */
    protected $table = 'portal.funcionario_vinculo';

    protected $primaryKey = 'cod_funcionario_vinculo';

    /**
     * @var array
     */
    protected $fillable = [
        'nm_vinculo',
        'abreviatura',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->nm_vinculo;
            },
        );
    }

    protected function abbreviation(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->abreviatura,
        );
    }
}
