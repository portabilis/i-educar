<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string $name
 * @property string $type
 * @property string $registry_origin
 */
class Person extends Model
{
    protected $table = 'persons';

    /**
     * @return HasOne<Individual, $this>
     */
    public function individual(): HasOne
    {
        return $this->hasOne(Individual::class);
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'created_by');
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function place(): HasOneThrough
    {
        /** @phpstan-ignore-next-line */
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
            get: fn () => (new PersonType)->getDescriptiveValues()[(int) $this->type],
        );
    }

    protected function registryOriginDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistryOrigin)->getDescriptiveValues()[(int) $this->registry_origin],
        );
    }
}
