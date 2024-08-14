<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacyDisciplineExemption
 *
 * @property LegacyRegistration $registration
 * @property array<int, string> $fillable
 * @property string $nm_vinculo
 * @property string $abreviatura
 */
class LegacyBondType extends Model
{
    protected $table = 'portal.funcionario_vinculo';

    protected $primaryKey = 'cod_funcionario_vinculo';

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
