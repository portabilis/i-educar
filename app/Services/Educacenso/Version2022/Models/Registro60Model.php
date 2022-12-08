<?php

namespace App\Services\Educacenso\Version2022\Models;

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
        $this->etapaAluno = $arrayColumns[8];
        $this->tipoItinerarioLinguagens = $arrayColumns[9];
        $this->tipoItinerarioMatematica = $arrayColumns[10];
        $this->tipoItinerarioCienciasNatureza = $arrayColumns[11];
        $this->tipoItinerarioCienciasHumanas = $arrayColumns[12];
        $this->tipoItinerarioFormacaoTecnica = $arrayColumns[13];
        $this->tipoItinerarioIntegrado = $arrayColumns[14];
        $this->composicaoItinerarioLinguagens = $arrayColumns[15];
        $this->composicaoItinerarioMatematica = $arrayColumns[16];
        $this->composicaoItinerarioCienciasNatureza = $arrayColumns[17];
        $this->composicaoItinerarioCienciasHumanas = $arrayColumns[18];
        $this->composicaoItinerarioFormacaoTecnica = $arrayColumns[19];
        $this->cursoItinerario = $arrayColumns[20];
        $this->itinerarioConcomitante = $arrayColumns[21];
        $this->tipoAtendimentoDesenvolvimentoFuncoesGognitivas = $arrayColumns[22];
        $this->tipoAtendimentoDesenvolvimentoVidaAutonoma = $arrayColumns[23];
        $this->tipoAtendimentoEnriquecimentoCurricular = $arrayColumns[24];
        $this->tipoAtendimentoEnsinoInformaticaAcessivel = $arrayColumns[25];
        $this->tipoAtendimentoEnsinoLibras = $arrayColumns[26];
        $this->tipoAtendimentoEnsinoLinguaPortuguesa = $arrayColumns[27];
        $this->tipoAtendimentoEnsinoSoroban = $arrayColumns[29];
        $this->tipoAtendimentoEnsinoBraile = $arrayColumns[29];
        $this->tipoAtendimentoEnsinoOrientacaoMobilidade = $arrayColumns[30];
        $this->tipoAtendimentoEnsinoCaa = $arrayColumns[31];
        $this->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos = $arrayColumns[32];
        $this->recebeEscolarizacaoOutroEspacao = $arrayColumns[33];
        $this->transportePublico = $arrayColumns[34];
        $this->poderPublicoResponsavelTransporte = $arrayColumns[35];
        $this->veiculoTransporteBicicleta = $arrayColumns[36];
        $this->veiculoTransporteMicroonibus = $arrayColumns[37];
        $this->veiculoTransporteOnibus = $arrayColumns[38];
        $this->veiculoTransporteTracaoAnimal = $arrayColumns[39];
        $this->veiculoTransporteVanKonbi = $arrayColumns[40];
        $this->veiculoTransporteOutro = $arrayColumns[41];
        $this->veiculoTransporteAquaviarioCapacidade5 = $arrayColumns[42];
        $this->veiculoTransporteAquaviarioCapacidade5a15 = $arrayColumns[43];
        $this->veiculoTransporteAquaviarioCapacidade15a35 = $arrayColumns[44];
        $this->veiculoTransporteAquaviarioCapacidadeAcima35 = (int) $arrayColumns[45];
    }
}
