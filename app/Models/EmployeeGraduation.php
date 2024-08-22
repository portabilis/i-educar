<?php

namespace App\Models;

use App\Models\Builders\EmployeeGraduationBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class EmployeeGraduation extends LegacyModel
{
    /** @use HasBuilder<EmployeeGraduationBuilder> */
    use HasBuilder;

    protected $fillable = [
        'employee_id',
        'course_id',
        'completion_year',
        'college_id',
        'discipline_id',
    ];

    protected static string $builder = EmployeeGraduationBuilder::class;

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * @return BelongsTo<EducacensoDegree, $this>
     */
    public function educacensoDegree(): BelongsTo
    {
        return $this->belongsTo(EducacensoDegree::class, 'course_id');
    }

    /**
     * @return BelongsTo<EducacensoInstitution, $this>
     */
    public function educacensoInstitution(): BelongsTo
    {
        return $this->belongsTo(EducacensoInstitution::class, 'college_id');
    }
}
