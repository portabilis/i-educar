<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;

class ItinerarioFormativoAluno implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        if (self::isNullItinerarioFormativoAluno($registro60)) {
            $registro60->tipoItinerarioLinguagens = null;
            $registro60->tipoItinerarioMatematica = null;
            $registro60->tipoItinerarioCienciasNatureza = null;
            $registro60->tipoItinerarioCienciasHumanas = null;
            $registro60->tipoItinerarioFormacaoTecnica = null;
            $registro60->tipoItinerarioIntegrado = null;
            $registro60->composicaoItinerarioLinguagens = null;
            $registro60->composicaoItinerarioMatematica = null;
            $registro60->composicaoItinerarioCienciasNatureza = null;
            $registro60->composicaoItinerarioCienciasHumanas = null;
            $registro60->composicaoItinerarioFormacaoTecnica = null;
            $registro60->cursoItinerario = null;
            $registro60->itinerarioConcomitante = null;
        }

        if (!$registro60->tipoItinerarioIntegrado) {
            $registro60->composicaoItinerarioLinguagens = null;
            $registro60->composicaoItinerarioMatematica = null;
            $registro60->composicaoItinerarioCienciasNatureza = null;
            $registro60->composicaoItinerarioCienciasHumanas = null;
            $registro60->composicaoItinerarioFormacaoTecnica = null;
            $registro60->cursoItinerario = null;
            $registro60->itinerarioConcomitante = null;
        }

        if (!$registro60->composicaoItinerarioFormacaoTecnica) {
            $registro60->cursoItinerario = null;
            $registro60->itinerarioConcomitante = null;
        }

        return $registro60;
    }

    private static function isNullItinerarioFormativoAluno(RegistroEducacenso $registro60)
    {
        $etapasValidas = [25, 26, 27, 28, 30, 31, 32, 33, 35, 36, 37, 38, 71, 74];

        if (!in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $registro60->estruturaCurricularTurma)) {
            return true;
        }

        if (
            in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $registro60->estruturaCurricularTurma) &&
            in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $registro60->estruturaCurricularTurma) &&
            !in_array($registro60->etapaTurma, $etapasValidas)
        ) {
            return true;
        }

        return false;
    }
}
