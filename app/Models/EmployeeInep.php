<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmployeeInep
 *
 * @property Employee $employee
 *
 */
class EmployeeInep extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_cod_docente';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_servidor';

    protected $fillable = ['cod_servidor', 'cod_docente_inep'];

    public function getNumberAttribute()
    {
        return $this->cod_docente_inep;
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'cod_servidor', 'cod_servidor');
    }
}
