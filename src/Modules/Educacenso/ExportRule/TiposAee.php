<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro60;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;

class TiposAee implements EducacensoExportRule
{
    /**
     * @param Registro60 $registro60
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro60): RegistroEducacenso
    {
        if ($registro60->tipoAtendimentoTurma != TipoAtendimentoTurma::AEE) {
            $registro60->tipoAtendimentoDesenvolvimentoFuncoesGognitivas = null;
            $registro60->tipoAtendimentoDesenvolvimentoVidaAutonoma = null;
            $registro60->tipoAtendimentoEnriquecimentoCurricular = null;
            $registro60->tipoAtendimentoEnsinoInformaticaAcessivel = null;
            $registro60->tipoAtendimentoEnsinoLibras = null;
            $registro60->tipoAtendimentoEnsinoLinguaPortuguesa = null;
            $registro60->tipoAtendimentoEnsinoSoroban = null;
            $registro60->tipoAtendimentoEnsinoBraile = null;
            $registro60->tipoAtendimentoEnsinoOrientacaoMobilidade = null;
            $registro60->tipoAtendimentoEnsinoCaa = null;
            $registro60->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos = null;
        }

        return $registro60;
    }
}
