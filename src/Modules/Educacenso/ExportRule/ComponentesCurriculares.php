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
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro50): RegistroEducacenso
    {
        if (self::isNullComponentes($registro50)) {
            $registro50->componente1;
            $registro50->componente2;
            $registro50->componente3;
            $registro50->componente4;
            $registro50->componente5;
            $registro50->componente6;
            $registro50->componente7;
            $registro50->componente8;
            $registro50->componente9;
            $registro50->componente10;
            $registro50->componente11;
            $registro50->componente12;
            $registro50->componente13;
            $registro50->componente14;
            $registro50->componente15;
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


        return in_array($registro50->funcaoDocente, $funcoes)
            || !in_array($registro50->etapaEducacensoTurma, $etapas)
            || $registro50->tipoAtendimentoTurma == TipoAtendimentoTurma::ESCOLARIZACAO;
    }
}
