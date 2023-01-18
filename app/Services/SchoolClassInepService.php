<?php

namespace App\Services;

use App\Models\SchoolClassInep;

class SchoolClassInepService
{
    /**
     * @param $codTurma
     * @param $codigoInepEducacenso
     *
     * @return SchoolClassInep
     */
    public function store($codTurma, $codigoInepEducacenso)
    {
        return SchoolClassInep::updateOrCreate([
            'cod_turma' => $codTurma,
        ], [
            'cod_turma_inep' => $codigoInepEducacenso,
        ]);
    }

    public function delete($codTurma)
    {
        $schoolClassInep = SchoolClassInep::find($codTurma);
        if ($schoolClassInep instanceof SchoolClassInep) {
            $schoolClassInep->delete();
        }
    }
}
