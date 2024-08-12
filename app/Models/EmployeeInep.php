<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmployeeInep
 *
 * @property Employee $employee
 * @property int $cod_docente_inep
 * @property array<int, string> $fillable
 */
class EmployeeInep extends LegacyModel
{
    protected $table = 'modules.educacenso_cod_docente';

    protected $fillable = [
        'cod_servidor',
        'cod_docente_inep',
    ];

    protected function number(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_docente_inep
        );
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'cod_servidor', 'cod_servidor');
    }
}
