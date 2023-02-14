<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class EmployeeBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return EmployeeBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por servidor
     *
     * @param int $id
     *
     * @return $this
     */
    public function whereEmployee(int $id): self
    {
        return $this->where('cod_servidor', $id);
    }

    /**
     * Filtra por escola
     *
     * @param int $schoolingDegree
     *
     * @return $this
     */
    public function whereSchoolingDegree(int $schoolingDegree): self
    {
        return $this->where('ref_idesco', $schoolingDegree);
    }

    /**
     * Filtra por Carga Horária
     *
     * @param int $workload
     *
     * @return $this
     */
    public function whereWorkload(int $workload): self
    {
        return $this->where('carga_horaria', $workload);
    }

    /**
     * Filtra por Nome
     *
     * @param string $name
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereHas('person', function (Builder $q) use ($name) {
            $q->whereRaw('slug ~* unaccent(?)', addcslashes($name, '[]()\''))
                ->orWhereRaw('SOUNDEX(nome) = SOUNDEX(?)', $name);
        });
    }

    /**
     * Filtra por escola
     *
     * @param int $school
     *
     * @return $this
     */
    public function whereSchool(int $school): self
    {
        return $this->whereHas('employeeAllocations', function (Builder $q) use ($school) {
            $q->where('ativo', 1);
            $q->when($school, fn ($q) => $q->where('ref_cod_escola', $school));
        });
    }

    /**
     * Filtra somente servidor com alocação
     *
     * @return $this
     */
    public function whereAllocation(bool $withNotAllocation, int $school = null, int $year = null): self
    {
        $this->where(function (Builder $q) use ($school, $year, $withNotAllocation) {
            $q->whereHas('employeeAllocations', function (Builder $q) use ($school, $year) {
                $q->where('ativo', 1);
                $q->when($school, fn ($q) => $q->where('ref_cod_escola', $school));
                $q->when($year, fn ($q) => $q->where('ano', $year));
            });

            if ($withNotAllocation) {
                $q->orWhereDoesntHave('employeeAllocations', function (Builder $q) use ($school, $year) {
                    $q->where('ativo', 1);
                    $q->when($school, fn ($q) => $q->where('ref_cod_escola', $school));
                    $q->when($year, fn ($q) => $q->where('ano', $year));
                });
            }
        });

        return $this;
    }

    /**
     * Filtra por função
     *
     * @param string $role
     *
     * @return $this
     */
    public function whereRole(string $role): self
    {
        return $this->whereHas('employeeRoles', function (Builder $q) use ($role) {
            $q->whereRaw('matricula ~* ?', $role);
        });
    }
}
