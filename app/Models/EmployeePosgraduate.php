<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePosgraduate extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.employee_posgraduate';

    /**
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'entity_id',
        'type_id',
        'area_id',
        'completion_year',
    ];

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }
}
