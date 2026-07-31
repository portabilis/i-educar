<?php

namespace App\Models\Builders;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;

class EmployeeAllocationBuilder extends LegacyBuilder
{
    /**
     * Filtra por Ano
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('ano', $year);
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por Servidor
     */
    public function whereEmployee(int $employee): self
    {
        return $this->where('ref_cod_servidor', $employee);
    }

    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where($this->model->getTable() . '.ref_ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por Período
     */
    public function wherePeriod(int $period): self
    {
        return $this->where('periodo', $period);
    }

    /**
     * Filtra por Carga horária
     */
    public function whereWorkload(string $workload): self
    {
        return $this->where('carga_horaria', $workload);
    }

    /**
     * Filtra pelas escolas em que o usuário tem acesso
     */
    public function whereUser(int $user): self
    {
        return $this->whereExists(fn (QueryBuilder $query) => $query->selectRaw('1')
            ->from('pmieducar.escola_usuario')
            ->whereColumn('escola_usuario.ref_cod_escola', $this->model->getTable() . '.ref_cod_escola')
            ->where('escola_usuario.ref_cod_usuario', $user));
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where($this->model->getTable() . '.ativo', 1);
    }

    /**
     * Desconsidera alocações cuja data de saída já passou
     */
    public function withoutLeaveDate(): self
    {
        return $this->where(fn (self $query) => $query->where('data_saida', '>', now())->orWhereNull('data_saida'));
    }
}
