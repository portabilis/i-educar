<?php

namespace App\Models;

use App\Models\Registration\RegistrationBuilder;
use App\Models\Registration\RegistrationScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     */
    public function newEloquentBuilder($query): RegistrationBuilder
    {
        return new RegistrationBuilder($query);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function statusDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistrationStatus())->getDescriptiveValues()[(int) $this->status],
        );
    }
}
