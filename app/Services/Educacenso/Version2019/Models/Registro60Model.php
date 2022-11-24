<?php

namespace App\Services\Educacenso\Version2019\Models;

use App\Models\Educacenso\Registro60;

class Registro60Model extends Registro60
{
    public function hydrateModel($arrayColumns): void
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->inepEscola = $arrayColumns[2];
        $this->inepAluno = $arrayColumns[4];
        $this->inepTurma = $arrayColumns[6];
        $this->etapaAluno = $arrayColumns[8] ?: null;
        $this->tipoAtendimentoDesenvolvimentoFuncoesGognitivas = $arrayColumns[9];
        $this->tipoAtendimentoDesenvolvimentoVidaAutonoma = $arrayColumns[10];
        $this->tipoAtendimentoEnriquecimentoCurricular = $arrayColumns[11];
        $this->tipoAtendimentoEnsinoInformaticaAcessivel = $arrayColumns[12];
        $this->tipoAtendimentoEnsinoLibras = $arrayColumns[13];
        $this->tipoAtendimentoEnsinoLinguaPortuguesa = $arrayColumns[14];
        $this->tipoAtendimentoEnsinoSoroban = $arrayColumns[15];
        $this->tipoAtendimentoEnsinoBraile = $arrayColumns[16];
        $this->tipoAtendimentoEnsinoOrientacaoMobilidade = $arrayColumns[17];
        $this->tipoAtendimentoEnsinoCaa = $arrayColumns[18];
        $this->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos = $arrayColumns[19];
        $this->recebeEscolarizacaoOutroEspacao = $arrayColumns[20] ?: null;
        $this->transportePublico = $arrayColumns[21] ?: null;
        $this->poderPublicoResponsavelTransporte = $arrayColumns[22] ?: null;
        $this->veiculoTransporteBicicleta = $arrayColumns[23];
        $this->veiculoTransporteMicroonibus = $arrayColumns[24];
        $this->veiculoTransporteOnibus = $arrayColumns[25];
        $this->veiculoTransporteTracaoAnimal = $arrayColumns[26];
        $this->veiculoTransporteVanKonbi = $arrayColumns[27];
        $this->veiculoTransporteOutro = $arrayColumns[29];
        $this->veiculoTransporteAquaviarioCapacidade5 = $arrayColumns[29];
        $this->veiculoTransporteAquaviarioCapacidade5a15 = $arrayColumns[30];
        $this->veiculoTransporteAquaviarioCapacidade15a35 = $arrayColumns[31];
        $this->veiculoTransporteAquaviarioCapacidadeAcima35 = (int) $arrayColumns[32];
    }
}
