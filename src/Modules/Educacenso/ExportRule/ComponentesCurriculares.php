<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Servidores\Model\FuncaoExercida;

class ComponentesCurriculares implements EducacensoExportRule
{
    /**
     * @param Registro50 $registro50
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro50): RegistroEducacenso
    {
        if (self::isNullComponentes($registro50)) {
            $registro50->componentes = null;
        }

        return $registro50;
    }

    public static function isNullComponentes($registro50)
    {
        $funcoes = [
            FuncaoExercida::DOCENTE,
            FuncaoExercida::DOCENTE_TITULAR_EAD,
        ];

        $etapas = [
            1, 2, 3
        ];

        return !in_array($registro50->funcaoDocente, $funcoes)
            || in_array($registro50->etapaEducacensoTurma, $etapas)
            || $registro50->tipoAtendimentoTurma != TipoAtendimentoTurma::ESCOLARIZACAO;
    }
}
