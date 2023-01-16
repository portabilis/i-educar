<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Servidores\Model\FuncaoExercida;

class UnidadesCurricularesServidor implements EducacensoExportRule
{
    /**
     * @param Registro50 $registro50
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro50): RegistroEducacenso
    {
        if (self::isNullUnidadesCurriculares($registro50)) {
            $registro50->unidadesCurriculares = null;
        }

        return $registro50;
    }

    public static function isNullUnidadesCurriculares($registro50)
    {
        $funcoesValidas = [
            FuncaoExercida::DOCENTE,
            FuncaoExercida::DOCENTE_TITULAR_EAD
        ];

        return
            !in_array($registro50->funcaoDocente, $funcoesValidas) ||
            !in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $registro50->estruturaCurricular);
    }
}
