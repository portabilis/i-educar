<?php

namespace App\Models;

use App\Models\Builders\SchoolManagerBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property string $chief
 */
class SchoolManager extends Model
{
    /** @use HasBuilder<SchoolManagerBuilder> */
    use HasBuilder;

    protected static string $builder = SchoolManagerBuilder::class;

    protected $fillable = [
        'employee_id',
        'school_id',
        'role_id',
        'access_criteria_id',
        'access_criteria_description',
        'link_type_id',
        'chief',
    ];

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'employee_id', 'id');
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'school_id', 'cod_escola');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'cod_servidor');
    }

    /**
     * @return BelongsTo<ManagerRole, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ManagerRole::class, 'role_id', 'id');
    }

    /**
     * @return BelongsTo<ManagerAccessCriteria, $this>
     */
    public function accessCriteria(): BelongsTo
    {
        return $this->belongsTo(ManagerAccessCriteria::class, 'access_criteria_id', 'id');
    }

    /**
     * @return BelongsTo<ManagerLinkType, $this>
     */
    public function linkType(): BelongsTo
    {
        return $this->belongsTo(ManagerLinkType::class, 'link_type_id', 'id');
    }

    public function isChief(): bool
    {
        return (bool) $this->chief;
    }
}
