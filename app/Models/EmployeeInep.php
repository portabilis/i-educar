<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmployeeInep
 *
 * @property Employee $employee
 */
class EmployeeInep extends LegacyModel
{
    /**
     * @var string
     */
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'cod_servidor', 'cod_servidor');
    }
}
