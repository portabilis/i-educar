<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Person $person
 * @property int $gender
 * @property int $nationality
 * @property int $registry_origin
 * @property int $localization_zone
 * @property Individual $father
 * @property Individual $mother
 * @property Individual $guardian
 */
class Individual extends Model
{
    use SoftDeletes;

    /**
     * @var array<string, string>
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
            get: fn () => (new Gender)->getDescriptiveValues()[$this->gender]
        );
    }

    protected function nationalityDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Nationality)->getDescriptiveValues()[$this->nationality]
        );
    }

    protected function registryOriginDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new RegistryOrigin)->getDescriptiveValues()[$this->registry_origin]
        );
    }

    protected function localizationZoneDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => (new LocalizationZone)->getDescriptiveValues()[$this->localization_zone]
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

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => int2CPF($value),
        );
    }

    /**
     * @return HasOne<Student, $this>
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'mother_individual_id', 'id');
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'father_individual_id', 'id');
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'guardian_individual_id', 'id');
    }

    /**
     * @return HasOne<Place, $this>
     */
    public function place(): HasOne
    {
        $hasOne = $this->hasOne(Place::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function birthplace(): BelongsTo
    {
        return $this->belongsTo(City::class, 'idmun_nascimento');
    }

    /**
     * @return HasOne<Phone, $this>
     */
    public function phone(): HasOne
    {
        $hasOne = $this->hasOne(Phone::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<Religion, $this>
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'updated_by', 'id');
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'created_by', 'id');
    }

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'deleted_by', 'id');
    }

    /**
     * @return MorphOne<LogUnification, $this>
     */
    public function unification(): MorphOne
    {
        return $this->morphOne(LogUnification::class, 'main', 'type', 'main_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'cod_usuario');
    }

    /**
     * @return HasOne<Employee, $this>
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'id');
    }
}
