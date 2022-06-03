<?php

namespace iEducar\Modules\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\RegistroEducacenso;
use iEducar\Modules\Educacenso\Model\PoderPublicoConveniado as ModelPoderPublicoConveniado;

class PoderPublicoConveniado implements EducacensoExportRule
{
    /**
     * @param Registro00 $registro00
     *
     * @return RegistroEducacenso
     */
    public static function handle(RegistroEducacenso $registro00): RegistroEducacenso
    {
        if ($registro00->podePublicoConveniado == ModelPoderPublicoConveniado::NAO_POSSUI) {
            $registro00->formasContratacaoPoderPublico = null;
            $registro00->qtdMatAtividadesComplentar = null;
            $registro00->qtdMatAee = null;
            $registro00->qtdMatCrecheParcial = null;
            $registro00->qtdMatCrecheIntegral = null;
            $registro00->qtdMatPreEscolaParcial = null;
            $registro00->qtdMatPreEscolaIntegral = null;
            $registro00->qtdMatFundamentalIniciaisParcial = null;
            $registro00->qtdMatFundamentalIniciaisIntegral = null;
            $registro00->qtdMatFundamentalFinaisParcial = null;
            $registro00->qtdMatFundamentalFinaisIntegral = null;
            $registro00->qtdMatEnsinoMedioParcial = null;
            $registro00->qtdMatEnsinoMedioIntegral = null;
            $registro00->qdtMatClasseEspecialParcial = null;
            $registro00->qdtMatClasseEspecialIntegral = null;
            $registro00->qdtMatEjaFundamental = null;
            $registro00->qtdMatEjaEnsinoMedio = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEjaFundamentalParcial = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEjaFundamentalIntegral = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEjaNivelMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalConcomitanteEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalConcomitanteEjaNivelMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalIntercomentarEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalIntercomentarEjaNivelMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalIntegradaEnsinoMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalConcomitenteEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalConcomitenteEnsinoMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalIntercomplementarEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalIntercomplementarEnsinoMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntegradaEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntegradaEnsinoMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaConcomitanteEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaConcomitanteEnsinoMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntercomplementarEnsinoMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntercomplementarEnsinoMedioItegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaSubsequenteEnsinoMedio = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntegradaEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntegradaEjaNivelMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaConcomitanteEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaConcomitanteEjaNivelMedioIntegral = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntercomplementarEjaNivelMedioParcial = null;
            $registro00->qtdMatEducacaoProfissionalTecnicaIntercomplementarEjaNivelMedioIntegral = null;
        }

        return $registro00;
    }
}
