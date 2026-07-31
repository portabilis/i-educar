<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use iEducar\Modules\Servidores\Model\FuncaoExercida;

class AreaItinerarioFormativo implements EducacensoExportRule
{
    /**
     * @param Registro50 $registro50
     */
    public static function handle(RegistroEducacenso $registro50): RegistroEducacenso
    {
        if (self::isNullAreaItinerario($registro50)) {
            $registro50->areaItinerario = null;
        }

        return $registro50;
    }

    public static function isNullAreaItinerario($registro50)
    {
        $funcoes = [
            FuncaoExercida::DOCENTE,
            FuncaoExercida::DOCENTE_TITULAR_EAD,
        ];

        $organizacaoCurricular = $registro50->organizacaoCurricular ?? [];

        return !in_array($registro50->funcaoDocente, $funcoes)
            || !in_array(OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO, $organizacaoCurricular);
    }
}
