<?php

namespace App\Services;

use App\Models\EmployeeAllocation;
use App\Models\LegacyAbsenceDelayCompensate;
use Carbon\Carbon;

class EmployeeService
{
    public function getQuantityHours(
        $cod_servidor,
        $cod_escola,
        $cod_instituicao,
        $dia_semana
    ): array {
        return EmployeeAllocation::query()
            ->selectRaw('
                EXTRACT(HOUR FROM (SUM(hora_final - hora_inicial))) AS hora,
                EXTRACT(MINUTE FROM (SUM(hora_final - hora_inicial))) AS min
            ')->whereSchool($cod_escola)
            ->whereEmployee($cod_servidor)
            ->whereInstitution($cod_instituicao)
            ->where('dia_semana', $dia_semana)
            ->first()->toArray();
    }

    public function getHoursCompensate(
        $cod_servidor,
        $cod_escola,
        $cod_instituicao
    ) {
        $registros = LegacyAbsenceDelayCompensate::query()
            ->where('ref_cod_servidor', $cod_servidor)
            ->where('ref_cod_escola', $cod_escola)
            ->where('ref_ref_cod_instituicao', $cod_instituicao)
            ->get();

        $horas_total = 0;
        $minutos_total = 0;

        foreach ($registros as $registro) {
            $data_atual = strtotime($registro['data_inicio']);
            $data_fim = strtotime($registro['data_fim']);

            do {
                $dia_semana = Carbon::createFromFormat('Y-m-d', date('Y-m-d', $data_atual))->dayOfWeek;

                $horas = $this->getQuantityHours($cod_servidor, $cod_escola, $cod_instituicao, $dia_semana);

                if ($horas) {
                    $horas_total += $horas['hora'];
                    $minutos_total += $horas['min'];
                }

                $data_atual += 86400;
            } while ($data_atual <= $data_fim);
        }

        return [
            'hora' => $horas_total,
            'min' => $minutos_total,
        ];
    }
}
