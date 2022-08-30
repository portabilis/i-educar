<?php

namespace App\Models;

use App\Models\Builders\LegacyStudentBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyStudent extends Model
{
    use LegacyAttribute;

    public $builder = LegacyStudentBuilder::class;

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
        'ref_idpes', 'data_cadastro', 'tipo_responsavel',
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

    /**
     * @return BelongsToMany
     */
    public function personMaleIndividual()
    {
        return $this->belongsTo(
            LegacyIndividual::class,
            'pmieducar.responsaveis_aluno',
            'ref_cod_aluno',
            'ref_idpes'
        );
    }

    public function getGuardianTypeAttribute()
    {
        return $this->tipo_responsavel;
    }

    public function getGuardianName(): ?string {

        return match ($this->guardianType) {
            'm' => $this->individual->mae->name,
            'p' => $this->individual->pai->name,
            'r' => $this->individual->responsavel->name,
            'a' => $this->joinGuardionNames(),
            default => null
        };
    }
    public function getGuardianCpf()
    {
        return match ($this->guardianType) {
            'm' => $this->individual->mae->individual->cpf,
            'p' => $this->individual->pai->individual->cpf,
            'r' => $this->individual->responsavel->individual->cpf,
            'a' => $this->joinGuardionCpfs(),
            default => null
        };
    }

    private function joinGuardionCpfs(): ?string
    {
        $join = $this->individual->mae->individual->cpf . ', ' . $this->individual->pai->individual->cpf;
        return strlen($join) < 3 ? null : $join;
    }

    private function joinGuardionNames(): ?string
    {
        $join = $this->individual->mae->name . ', ' . $this->individual->pai->name;
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
