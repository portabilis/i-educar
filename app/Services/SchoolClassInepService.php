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
        $schoolClassInep = SchoolClassInep::firstOrNew(['cod_turma' => $codTurma]);

        $schoolClassInep->cod_turma = $codTurma;
        $schoolClassInep->cod_turma_inep = $codigoInepEducacenso;

        $schoolClassInep->save();

        return $schoolClassInep;
    }

    public function delete($codTurma)
    {
        $schoolClassInep = SchoolClassInep::find($codTurma);
        if ($schoolClassInep instanceof SchoolClassInep) {
            $schoolClassInep->delete();
        }
    }
}
