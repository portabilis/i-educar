<?php

namespace App\Services;

use App\Models\SchoolClassInep;

class SchoolClassInepService
{
    /**
     * @return SchoolClassInep
     */
    public function store($codTurma, $codigoInepEducacenso, $turnoId = null)
    {
        return SchoolClassInep::updateOrCreate([
            'cod_turma' => $codTurma,
            'turma_turno_id' => $turnoId,
        ], [
            'cod_turma_inep' => $codigoInepEducacenso,
        ]);
    }

    public function delete($codTurma, $turnoId = null)
    {
        $schoolClassInep = SchoolClassInep::query()
            ->where('cod_turma', $codTurma)
            ->where('turma_turno_id', $turnoId)
            ->first();

        if ($schoolClassInep instanceof SchoolClassInep) {
            $schoolClassInep->delete();
        }
    }
}
