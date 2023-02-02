<?php

namespace App\Models\Builders;

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
            1,
            2,
            12,
            13,
            14
        ]);
    }

    public function notFinalized(): LegacyBuilder
    {
        return $this->whereNotIn('aprovado', [
            1,
            2,
            12,
            13,
            14
        ]);
    }

    public function statusTransfer(): LegacyBuilder
    {
        return $this->where('aprovado', 4);
    }

    public function modalityRegular(): LegacyBuilder
    {
        return $this->whereHas('course', static fn (
            $q
        ) => $q->where('curso.modalidade_curso', 1));
    }

    public function serviceTypeNotComplementaryActivity(): LegacyBuilder
    {
        return $this->whereHas('schoolClasses', static fn (
            $q
        ) => $q->where('turma.tipo_atendimento', '<>', 4)->orWhereNull('turma.tipo_atendimento'));
    }

    /**
     * Filtra por Escola
     *
     * @param int $school
     *
     * @return LegacyRegistrationBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Turma
     *
     * @param int $schoolClass
     *
     * @return LegacyRegistrationBuilder
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->whereHas('enrollments', static fn ($q) => $q->where('ref_cod_turma', $schoolClass));
    }

    /**
     * Filtra por ativo
     *
     * @return LegacyRegistrationBuilder
     */
    public function active(): self
    {
        return $this->where($this->model->getTable().'.ativo', 1);
    }

    /**
     * Filtra por ano
     *
     * @param int $year
     *
     * @return $this
     */
    public function whereYearEq(int $year): self
    {
        return $this->where($this->model->getTable().'.ano', $year);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacyRegistrationBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->joinPerson()->orderBy('nome', $direction);
    }

    /**
     * Realiza a junçao com organização
     *
     * @return LegacyRegistrationBuilder
     */
    public function joinPerson(): self
    {
        $this->join('pmieducar.aluno', 'ref_cod_aluno', 'cod_aluno');
        $this->join('cadastro.pessoa', 'idpes', 'ref_idpes');

        return $this;
    }
}
