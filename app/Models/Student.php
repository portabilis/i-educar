<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function individual(): BelongsTo
    {
        return $this->belongsTo(Individual::class);
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    public function census(): HasOne
    {
        return $this->hasOne(CensusStudent::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    public function getGuardianTypeAttribute($value): int|null
    {
        if ($value) {
            return $value;
        }

        if ($this->individual->father) {
            return GuardianType::FATHER;
        }

        if ($this->individual->mother) {
            return GuardianType::MOTHER;
        }

        if ($this->individual->guardian) {
            return GuardianType::OTHER;
        }

        return null;
    }

    public function getGuardianTypeDescriptionAttribute(): string
    {
        return (new GuardianType())->getDescriptiveValues()[(int) $this->guardian_type];
    }

    public function getTransportationProviderDescriptionAttribute(): string
    {
        return (new TransportationProvider())->getDescriptiveValues()[(int) $this->transportation_provider];
    }

    public function getTransportationVehicleTypeDescriptionAttribute(): string
    {
        return (new TransportationVehicleType())->getDescriptiveValues()[(int) $this->transportation_vehicle_type];
    }

    public function unification(): MorphOne
    {
        return $this->morphOne(LogUnification::class, 'main', 'type', 'main_id');
    }
}
