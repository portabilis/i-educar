<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Servidores\Model\FuncaoExercida;

class TipoVinculoServidor implements EducacensoExportRule
{
    /**
     * @param Registro50 $registro50
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro50): RegistroEducacenso
    {
        $funcoes = [
            FuncaoExercida::DOCENTE,
            FuncaoExercida::DOCENTE_TITULAR_EAD,
            FuncaoExercida::DOCENTE_TUTOR_EAD
        ];
        if ($registro50->dependenciaAdministrativaEscola == DependenciaAdministrativaEscola::PRIVADA || !in_array($registro50->funcaoDocente, $funcoes)) {
            $registro50->tipoVinculo = null;
        }

        return $registro50;
    }
}
