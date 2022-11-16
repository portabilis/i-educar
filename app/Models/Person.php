<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Person extends Model
{
    protected $table = 'persons';

    public function individual(): HasOne
    {
        return $this->hasOne(Individual::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    public function place(): HasOneThrough
    {
        return $this->hasOneThrough(
            Place::class,
            PersonHasPlace::class,
            'person_id',
            'id',
            'id',
            'place_id'
        )->orderBy('type');
    }

    protected function typeDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new PersonType())->getDescriptiveValues()[(int) $this->type],
        );
    }

    protected function registryOriginDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistryOrigin())->getDescriptiveValues()[(int) $this->registry_origin],
        );
    }
}
