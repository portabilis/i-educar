<?php

namespace App\Models;

use App\Models\Registration\RegistrationBuilder;
use App\Models\Registration\RegistrationScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * Registration
 *
 * @method static RegistrationBuilder query()
 */
class Registration extends Model
{
    use RegistrationScopes;
    use SoftDeletes;

    /**
     * @param Builder $query
     *
     * @return RegistrationBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new RegistrationBuilder($query);
    }

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return string
     */
    public function getStatusDescriptionAttribute()
    {
        return (new RegistrationStatus())->getDescriptiveValues()[(int) $this->status];
    }
}
