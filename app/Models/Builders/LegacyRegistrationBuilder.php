<?php

namespace App\Models\Builders;

use App\Models\RegistrationStatus;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

class LegacyRegistrationBuilder extends LegacyBuilder
{
    public function transfer(): LegacyBuilder
    {
        return $this->active()
            ->currentYear()
            ->statusTransfer()
            ->modalityRegular()
            ->serviceTypeNotComplementaryActivity()
            ->whereHas('student', static function (
                $q
            ) {
                $q->whereDoesntHave('registrations', static function (
                    $q
                ) {
                    $q->currentYear();
                    $q->active();
                    $q->finalized();
                });
            })
            ->orderBy('cod_matricula', 'desc');
    }

    public function finalized(): LegacyBuilder
    {
        return $this->whereIn('aprovado', [
            RegistrationStatus::APPROVED,
            RegistrationStatus::REPROVED,
            RegistrationStatus::APPROVED_WITH_DEPENDENCY,
            RegistrationStatus::APPROVED_BY_BOARD,
            RegistrationStatus::REPROVED_BY_ABSENCE,
        ]);
    }

    public function notFinalized(): LegacyBuilder
    {
        return $this->whereNotIn('aprovado', [
            RegistrationStatus::APPROVED,
            RegistrationStatus::REPROVED,
            RegistrationStatus::APPROVED_WITH_DEPENDENCY,
            RegistrationStatus::APPROVED_BY_BOARD,
            RegistrationStatus::REPROVED_BY_ABSENCE,
        ]);
    }

    public function whereName(string $name): self
    {
        return $this->whereHas('student.person', fn ($q) => $q->whereRaw('unaccent(pessoa.nome) ~* unaccent(?)', $name));
    }

    public function statusTransfer(): LegacyBuilder
    {
        return $this->where('aprovado', RegistrationStatus::TRANSFERRED);
    }

    public function modalityRegular(): LegacyBuilder
    {
        return $this->whereHas('course', static fn (
            $q
        ) => $q->where('curso.modalidade_curso', ModalidadeCurso::ENSINO_REGULAR));
    }

    public function serviceTypeNotComplementaryActivity(): LegacyBuilder
    {
        return $this->whereHas('schoolClasses', static fn (
            $q
        ) => $q->where('turma.tipo_atendimento', '<>', TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR)->orWhereNull('turma.tipo_atendimento'));
    }

    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->whereHas('school', static fn ($q) => $q->whereInstitution($institution));
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Curso
     */
    public function whereCourse(int $course): self
    {
        return $this->where('ref_cod_curso', $course);
    }

    /**
     * Filtra por Série
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ref_ref_cod_serie', $grade);
    }

    /**
     * Filtra por Aluno
     */
    public function whereStudent(int $student): self
    {
        return $this->where('ref_cod_aluno', $student);
    }

    /**
     * Filtra por Tipos de Deficiência
     */
    public function whereDeficiencyTypes(string $deficiencyTypes): self
    {
        return $this->whereHas('student.person', fn ($q) => $q->whereDeficiencyTypes($deficiencyTypes));
    }

    /**
     * Filtra por Situacao
     */
    public function whereSituation(int $situation): self
    {
        return $this->whereHas('situations', fn ($q) => $q->situation($situation));
    }

    /**
     * Filtra por Turma
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->whereHas('enrollments', static fn ($q) => $q->whereValid()->where('ref_cod_turma', $schoolClass));
    }

    /**
     * Filtra por Matricula
     */
    public function whereRegistration(int $registration): self
    {
        return $this->whereKey($registration);
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where($this->model->getTable().'.ativo', 1);
    }

    /**
     * Filtra por ano
     *
     *
     * @return $this
     */
    public function whereYearEq(int $year): self
    {
        return $this->where($this->model->getTable().'.ano', $year);
    }

    /**
     * Ordena por nome
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->joinPerson()->orderBy('nome', $direction);
    }

    /**
     * Realiza a junçao com organização
     */
    public function joinPerson(): self
    {
        $this->join('pmieducar.aluno', 'ref_cod_aluno', 'cod_aluno');
        $this->join('cadastro.pessoa', 'idpes', 'ref_idpes');

        return $this;
    }

    /**
     * Não considera alunos reclassificados
     */
    public function notReclassified(): self
    {
        return $this->where('aprovado', '<>', RegistrationStatus::RECLASSIFIED);
    }

    public function allRelationsActive(): self
    {
        $this->active();
        $this->whereHas('student', fn ($q) => $q->active());
        $this->whereHas('school', fn ($q) => $q->active());
        $this->whereHas('course', fn ($q) => $q->active());
        $this->whereHas('grade', fn ($q) => $q->active());
        $this->whereHas('schoolClass', fn ($q) => $q->active());

        return $this;
    }

    /**
     * Filtra por data inicial
     *
     * @return $this
     */
    public function whereStartAfter(string $start): self
    {
        return $this->whereDate('data_matricula', '>=', $start);
    }

    /**
     * Filtra por data final
     *
     * @return $this
     */
    public function whereEndBefore(string $end): self
    {
        return $this->whereDate('data_matricula', '<=', $end);
    }

    public function whereDependency(int $dependency): self
    {
        if ($dependency === 1) {
            return $this->where('dependencia', true);
        }
        if ($dependency === 2) {
            return $this->where('dependencia', false);
        }

        return $this;
    }

    /**
     * Filtra por zona de localização
     */
    public function whereLocalizationZone(int $localizationZone): self
    {
        return $this->whereHas('school', fn ($q) => $q->whereLocalizationZone($localizationZone));
    }

    /**
     * Filtra por localizacao diferenciada
     */
    public function whereDifferentiatedLocalizationArea(int $differentiatedLocalizationArea): self
    {
        return $this->whereHas('school', fn ($q) => $q->whereDifferentiatedLocalizationArea($differentiatedLocalizationArea));
    }
}
