<?php

namespace App\Models;

use App\Models\Builders\LegacyStudentBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyStudent extends LegacyModel
{
    use LegacyAttribute;
    use HasLegacyDates;

    public string $builder = LegacyStudentBuilder::class;

    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_idpes',
        'tipo_responsavel',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->data_cadastro = now();
        });
    }

    /**
     * @return BelongsTo
     */
    public function individual()
    {
        return $this->belongsTo(LegacyIndividual::class, 'ref_idpes');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    public function registrations()
    {
        return $this->hasMany(LegacyRegistration::class, 'ref_cod_aluno');
    }

    /**
     * @return BelongsToMany
     */
    public function guardians()
    {
        return $this->belongsToMany(
            LegacyPerson::class,
            'pmieducar.responsaveis_aluno',
            'ref_cod_aluno',
            'ref_idpes',
            'cod_aluno',
            'idpes'
        );
    }

    public function getGuardianTypeAttribute()
    {
        return $this->tipo_responsavel;
    }

    public function getGuardianName(): ?string
    {
        return match ($this->guardianType) {
            'm' => $this->individual->mother->name,
            'p' => $this->individual->father->name,
            'r' => $this->individual->responsible->name,
            'a' => $this->joinGuardionNames(),
            default => null
        };
    }

    public function getGuardianCpf()
    {
        return match ($this->guardianType) {
            'm' => $this->individual->mother->individual->cpf ?? 'não informado',
            'p' => $this->individual->father->individual->cpf ?? 'não informado',
            'r' => $this->individual->responsible->individual->cpf ?? 'não informado',
            'a' => $this->joinGuardionCpfs(),
            default => null
        };
    }

    private function joinGuardionCpfs(): ?string
    {
        $join = ($this->individual->mother->individual->cpf ?? 'não informado') . ', ' . ($this->individual->father->individual->cpf ?? 'não informado');

        return strlen($join) < 3 ? null : $join;
    }

    private function joinGuardionNames(): ?string
    {
        $join = $this->individual->mother->name . ', ' . $this->individual->father->name;

        return strlen($join) < 3 ? null : $join;
    }

    public function getInepNumberAttribute()
    {
        return $this->inep ? $this->inep->number : null;
    }

    public function getStateRegistrationIdAttribute()
    {
        return $this->aluno_estado_id;
    }

    public function setStateRegistrationIdAttribute($value)
    {
        $this->aluno_estado_id = $value;
    }

    public function inep()
    {
        return $this->hasOne(StudentInep::class, 'cod_aluno', 'cod_aluno');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('aluno.ativo', 1);
    }

    public function scopeMale(Builder $query)
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query)
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'F');
    }

    public function benefits()
    {
        return $this->belongsToMany(LegacyBenefit::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_id', 'aluno_beneficio_id');
    }
}
