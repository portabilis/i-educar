<?php

namespace App\Services;

use App\Models\SchoolClassInep;
use App\Services\SchoolClass\SchoolClassService;
use iEducar\Modules\SchoolClass\Period;

class SchoolClassInepService
{
    public function __construct(private SchoolClassService $schoolClassService) {}

    /**
     * @return SchoolClassInep
     */
    public function store($codTurma, $codigoInepEducacenso, $turnoId = null)
    {
        SchoolClassInep::where('cod_turma', $codTurma)
            ->where('cod_turma_inep', $codigoInepEducacenso)
            ->where(function ($query) use ($turnoId) {
                if ($turnoId === null) {
                    $query->whereNotNull('turma_turno_id');
                } else {
                    $query->whereNull('turma_turno_id')->orWhere('turma_turno_id', '!=', $turnoId);
                }
            })
            ->delete();

        return SchoolClassInep::updateOrCreate([
            'cod_turma' => $codTurma,
            'turma_turno_id' => $turnoId,
        ], [
            'cod_turma_inep' => $codigoInepEducacenso,
        ]);
    }

    public function delete($codTurma, $turnoId = null)
    {
        SchoolClassInep::query()
            ->where('cod_turma', $codTurma)
            ->where('turma_turno_id', $turnoId)
            ->delete();
    }

    public function save(
        $codTurma,
        $codigoInepEducacenso,
        $codigoInepEducacensoMatutino,
        $codigoInepEducacensoVespertino,
        $turnoId
    ) {
        if ($codigoInepEducacenso) {
            if (!($turnoId === Period::FULLTIME && $this->schoolClassService->hasStudentsPartials($codTurma))) {
                $turnoId = null;
            }

            $this->store(
                codTurma: $codTurma,
                codigoInepEducacenso: $codigoInepEducacenso,
                turnoId: $turnoId
            );
        } else {
            $this->delete($codTurma);
        }

        if ($codigoInepEducacensoMatutino) {
            $this->store(
                codTurma: $codTurma,
                codigoInepEducacenso: $codigoInepEducacensoMatutino,
                turnoId: Period::MORNING
            );
        } else {
            $this->delete(
                codTurma: $codTurma,
                turnoId: Period::MORNING
            );
        }

        if ($codigoInepEducacensoVespertino) {
            $this->store(
                codTurma: $codTurma,
                codigoInepEducacenso: $codigoInepEducacensoVespertino,
                turnoId: Period::AFTERNOON
            );
        } else {
            $this->delete(
                codTurma: $codTurma,
                turnoId: Period::AFTERNOON
            );
        }
    }
}
