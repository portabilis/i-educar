<?php

namespace App\Models;

use App\Models\Registration\RegistrationBuilder;
use App\Models\Registration\RegistrationScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Registration
 *
 * @property string $status
 *
 * @method static RegistrationBuilder query()
 */
class Registration extends Model
{
    /** @use HasBuilder<RegistrationBuilder> */
    use HasBuilder;

    use RegistrationScopes;
    use SoftDeletes;

    protected static string $builder = RegistrationBuilder::class;

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function statusDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistrationStatus)->getDescriptiveValues()[(int) $this->status],
        );
    }
}
