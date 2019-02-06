<?php

namespace App\Services;

use App\Models\SchoolClass;

class SchoolClassService
{
    /**
     * Retorna se o nome está disponível para cadastro. Ignora a turma com ID
     * caso seja informado.
     *
     * @param string   $name         Nome da turma
     * @param int      $school       ID da escola
     * @param int      $academicYear Ano letivo
     * @param int|null $idToIgnore   ID da turma que deve ser ignorado (opcional)
     *
     * @return bool
     */
    public function isAvailableName($name, $school, $academicYear, $idToIgnore = null)
    {
        $query = SchoolClass::query()
            ->where('nm_turma', (string) $name)
            ->where('ref_ref_cod_escola', $school)
            ->where('ano', $academicYear);

        if ($idToIgnore) {
            $query->where('cod_turma', '!=', $idToIgnore);
        }

        $isAvailable = $query->count() === 0;

        return $isAvailable;
    }
}