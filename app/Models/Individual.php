<?php

namespace App\Models;

use App\User;
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

    /**
     * @return string
     */
    public function getGenderDescriptionAttribute()
    {
        return (new Gender())->getDescriptiveValues()[$this->gender];
    }

    /**
     * @return string
     */
    public function getNationalityDescriptionAttribute()
    {
        return (new Nationality())->getDescriptiveValues()[$this->nationality];
    }

    /**
     * @return string
     */
    public function getRegistryOriginDescriptionAttribute()
    {
        return (new RegistryOrigin())->getDescriptiveValues()[$this->registry_origin];
    }

    /**
     * @return string
     */
    public function getLocalizationZoneDescriptionAttribute()
    {
        return (new LocalizationZone())->getDescriptiveValues()[$this->localization_zone];
    }

    /**
     * @return string
     */
    public function getRealNameAttribute()
    {
        if (empty($this->social_name)) {
            return $this->person->name;
        }

        return $this->social_name;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getMotherNameAttribute($value)
    {
        if ($this->mother) {
            return $this->mother->real_name;
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getFatherNameAttribute($value)
    {
        if ($this->father) {
            return $this->father->real_name;
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getGuardianNameAttribute($value)
    {
        if ($this->guardian) {
            return $this->guardian->real_name;
        }

        return $value;
    }

    /**
     * @return HasOne
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return BelongsTo
     */
    public function mother()
    {
        return $this->belongsTo(Individual::class, 'mother_individual_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function father()
    {
        return $this->belongsTo(Individual::class, 'father_individual_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function guardian()
    {
        return $this->belongsTo(Individual::class, 'guardian_individual_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function place()
    {
        $hasOne = $this->hasOne(Place::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo
     */
    public function birthplace()
    {
        return $this->belongsTo(City::class, 'idmun_nascimento');
    }

    /**
     * @return HasOne
     */
    public function phone()
    {
        $hasOne = $this->hasOne(Phone::class, 'person_id');

        $hasOne->getQuery()->orderBy('type_id');

        return $hasOne;
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo
     */
    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(Individual::class, 'updated_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(Individual::class, 'created_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function deletedBy()
    {
        return $this->belongsTo(Individual::class, 'deleted_by', 'id');
    }

    /**
     * @return MorphOne
     */
    public function unification()
    {
        return $this->morphOne(LogUnification::class, 'main', 'type', 'main_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'cod_usuario');
    }

    /**
     * @return HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'cod_servidor', 'id');
    }
}
