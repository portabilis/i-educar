<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Individual extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'gender' => 'integer',
        'nationality' => 'integer',
        'registry_origin' => 'integer',
        'localization_zone' => 'integer',
    ];

    protected function genderDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Gender())->getDescriptiveValues()[$this->gender]
        );
    }

    protected function nationalityDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Nationality())->getDescriptiveValues()[$this->nationality]
        );
    }

    protected function registryOriginDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistryOrigin())->getDescriptiveValues()[$this->registry_origin]
        );
    }

    protected function localizationZoneDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new LocalizationZone())->getDescriptiveValues()[$this->localization_zone]
        );
    }

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->social_name)) {
                    return $this->person->name;
                }

                return $this->social_name;
            }
        );
    }

    protected function motherName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->mother->real_name ?? $value
        );
    }

    protected function fatherName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->father->real_name ?? $value
        );
    }

    protected function guardianName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->guardian->real_name ?? $value
        );
    }

    /**
     * @return HasOne
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * @return BelongsTo
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo
     */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'mother_individual_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'father_individual_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'guardian_individual_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function place(): HasOne
    {
        $hasOne = $this->hasOne(Place::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo
     */
    public function birthplace(): BelongsTo
    {
        return $this->belongsTo(City::class, 'idmun_nascimento');
    }

    /**
     * @return HasOne
     */
    public function phone(): HasOne
    {
        $hasOne = $this->hasOne(Phone::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    /**
     * @return MorphOne
     */
    public function unification(): MorphOne
    {
        return $this->morphOne(LogUnification::class, 'main', 'type', 'main_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'cod_usuario');
    }

    /**
     * @return HasOne
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'id');
    }
}
