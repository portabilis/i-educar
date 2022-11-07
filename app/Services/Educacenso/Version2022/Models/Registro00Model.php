<?php

namespace App\Services\Educacenso\Version2022\Models;

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\Model\FormasContratacaoPoderPublico;
use iEducar\Modules\Educacenso\Model\PoderPublicoConveniado;

class Registro00Model extends Registro00
{
    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->registro = $arrayColumns[1];
        $this->codigoInep = $arrayColumns[2];
        $this->situacaoFuncionamento = $arrayColumns[3];
        $this->inicioAnoLetivo = $arrayColumns[4];
        $this->fimAnoLetivo = $arrayColumns[5];
        $this->nome = $arrayColumns[6];
        $this->cep = $arrayColumns[7];
        $this->codigoIbgeMunicipio = $arrayColumns[8];
        $this->codigoIbgeDistrito = $arrayColumns[9];
        $this->logradouro = $arrayColumns[10];
        $this->numero = $arrayColumns[11];
        $this->complemento = $arrayColumns[12];
        $this->bairro = $arrayColumns[13];
        $this->ddd = $arrayColumns[14];
        $this->telefone = $arrayColumns[15];
        $this->telefoneOutro = $arrayColumns[16];
        $this->email = $arrayColumns[17];
        $this->orgaoRegional = $arrayColumns[18];
        $this->zonaLocalizacao = $arrayColumns[19];
        $this->localizacaoDiferenciada = $arrayColumns[20];
        $this->dependenciaAdministrativa = $arrayColumns[21];
        $this->orgaoEducacao = $arrayColumns[22];
        $this->orgaoSeguranca = $arrayColumns[23];
        $this->orgaoSaude = $arrayColumns[24];
        $this->orgaoOutro = $arrayColumns[25];
        $this->mantenedoraEmpresa = $arrayColumns[26];
        $this->mantenedoraSindicato = $arrayColumns[27];
        $this->mantenedoraOng = $arrayColumns[28];
        $this->mantenedoraInstituicoes = $arrayColumns[29];
        $this->mantenedoraSistemaS = $arrayColumns[30];
        $this->mantenedoraOscip = $arrayColumns[31];
        $this->categoriaEscolaPrivada = $arrayColumns[32];


        $this->poderPublicoConveniado = array_filter([
            $arrayColumns[33] ? PoderPublicoConveniado::ESTADUAL : null,
            $arrayColumns[34] ? PoderPublicoConveniado::MUNICIPAL : null,
            $arrayColumns[35] ? PoderPublicoConveniado::NAO_POSSUI : null,
        ]);

        $this->formasContratacaoPoderPublico = array_filter([
            $arrayColumns[36] ? FormasContratacaoPoderPublico::TERMO_COLABORACAO : null,
            $arrayColumns[37] ? FormasContratacaoPoderPublico::TERMO_FOMENTO : null,
            $arrayColumns[38] ? FormasContratacaoPoderPublico::ACORDO_COOPERACAO : null,
            $arrayColumns[39] ? FormasContratacaoPoderPublico::CONTRATO_PRESTACAO_SERVICO : null,
            $arrayColumns[40] ? FormasContratacaoPoderPublico::TERMO_COOPERACAO_TECNICA : null,
            $arrayColumns[41] ? FormasContratacaoPoderPublico::CONTRATO_CONSORCIO : null,
        ]);

        $this->qtdMatAtividadesComplentar = $arrayColumns[42];
        $this->qtdMatAee = $arrayColumns[43];
        $this->qtdMatCrecheParcial = $arrayColumns[44];
        $this->qtdMatCrecheIntegral = $arrayColumns[45];
        $this->qtdMatPreEscolaParcial = $arrayColumns[46];
        $this->qtdMatPreEscolaIntegral = $arrayColumns[47];
        $this->qtdMatFundamentalIniciaisParcial = $arrayColumns[48];
        $this->qtdMatFundamentalIniciaisIntegral = $arrayColumns[49];
        $this->qtdMatFundamentalFinaisParcial = $arrayColumns[50];
        $this->qtdMatFundamentalFinaisIntegral = $arrayColumns[51];
        $this->qtdMatEnsinoMedioParcial = $arrayColumns[52];
        $this->qtdMatEnsinoMedioIntegral = $arrayColumns[53];
        $this->qdtMatClasseEspecialParcial = $arrayColumns[54];
        $this->qdtMatClasseEspecialIntegral = $arrayColumns[55];
        $this->qdtMatEjaFundamental = $arrayColumns[56];
        $this->qtdMatEjaEnsinoMedio = $arrayColumns[57];
        $this->qtdMatEdProfIntegradaEjaFundamentalParcial = $arrayColumns[58];
        $this->qtdMatEdProfIntegradaEjaFundamentalIntegral = $arrayColumns[59];
        $this->qtdMatEdProfIntegradaEjaNivelMedioParcial = $arrayColumns[60];
        $this->qtdMatEdProfIntegradaEjaNivelMedioIntegral = $arrayColumns[61];
        $this->qtdMatEdProfConcomitanteEjaNivelMedioParcial = $arrayColumns[62];
        $this->qtdMatEdProfConcomitanteEjaNivelMedioIntegral = $arrayColumns[63];
        $this->qtdMatEdProfIntercomentarEjaNivelMedioParcial = $arrayColumns[64];
        $this->qtdMatEdProfIntercomentarEjaNivelMedioIntegral = $arrayColumns[65];
        $this->qtdMatEdProfIntegradaEnsinoMedioParcial = $arrayColumns[66];
        $this->qtdMatEdProfIntegradaEnsinoMedioIntegral = $arrayColumns[67];
        $this->qtdMatEdProfConcomitenteEnsinoMedioParcial = $arrayColumns[68];
        $this->qtdMatEdProfConcomitenteEnsinoMedioIntegral = $arrayColumns[69];
        $this->qtdMatEdProfIntercomplementarEnsinoMedioParcial = $arrayColumns[70];
        $this->qtdMatEdProfIntercomplementarEnsinoMedioIntegral = $arrayColumns[71];
        $this->qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial = $arrayColumns[72];
        $this->qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral = $arrayColumns[73];
        $this->qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial = $arrayColumns[74];
        $this->qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral = $arrayColumns[75];
        $this->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial = $arrayColumns[76];
        $this->qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral = $arrayColumns[77];
        $this->qtdMatEdProfTecnicaSubsequenteEnsinoMedio = $arrayColumns[78];
        $this->qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial = $arrayColumns[79];
        $this->qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral = $arrayColumns[80];
        $this->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial = $arrayColumns[81];
        $this->qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral = $arrayColumns[82];
        $this->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial = $arrayColumns[83];
        $this->qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioIntegral = $arrayColumns[84];
        $this->cnpjMantenedoraPrincipal = $arrayColumns[85];
        $this->cnpjEscolaPrivada = $arrayColumns[86];
        $this->regulamentacao = $arrayColumns[87];
        $this->esferaFederal = $arrayColumns[88];
        $this->esferaEstadual = $arrayColumns[89];
        $this->esferaMunicipal = $arrayColumns[90];
        $this->unidadeVinculada = $arrayColumns[91];
        $this->inepEscolaSede = $arrayColumns[92];
        $this->codigoIes = $arrayColumns[93];
    }
}
