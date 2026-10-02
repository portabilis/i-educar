<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolNotice extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const PROCESS = 1027;

    protected $table = 'school_notices';

    protected $casts = [
        'date' => 'date',
        'hour' => 'datetime',
    ];

    protected $fillable = [
        'institution_id',
        'user_id',
        'title',
        'description',
        'date',
        'hour',
        'local',
    ];

    /**
     * @return BelongsTo<LegacyInstitution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'institution_id');
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'user_id');
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /**
     * @return BelongsToMany<LegacySchool, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'school_notice_schools',
            'school_notice_id',
            'school_id'
        );
    }
}