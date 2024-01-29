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
            'turno_id' => $turnoId
        ], [
            'cod_turma_inep' => $codigoInepEducacenso,
        ]);
    }

    public function delete($codTurma, $turnoId = null)
    {
        $schoolClassInep = SchoolClassInep::query()
            ->where('turno_id', $turnoId)
            ->find($codTurma);

        if ($schoolClassInep instanceof SchoolClassInep) {
            $schoolClassInep->delete();
        }
    }
}
