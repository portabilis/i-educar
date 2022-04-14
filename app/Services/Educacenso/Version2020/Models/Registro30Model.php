<?php

namespace App\Services\Educacenso\Version2020\Models;

use App\Models\Educacenso\Registro30;

class Registro30Model extends Registro30
{
    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->inepEscola = $arrayColumns[2];
        $this->codigoPessoa = $arrayColumns[3];
        $this->inepPessoa = $arrayColumns[4];
        $this->cpf = $arrayColumns[5];
        $this->nomePessoa = $arrayColumns[6];
        $this->dataNascimento = $arrayColumns[7];
        $this->filiacao = $arrayColumns[8];
        $this->filiacao1 = $arrayColumns[9];
        $this->filiacao2 = $arrayColumns[10];
        $this->sexo = $arrayColumns[11];
        $this->raca = $arrayColumns[12];
        $this->nacionalidade = $arrayColumns[13];
        $this->paisNacionalidade = $arrayColumns[14];
        $this->municipioNascimento = $arrayColumns[15];
        $this->deficiencia = $arrayColumns[16];
        $this->deficienciaCegueira = $arrayColumns[17];
        $this->deficienciaBaixaVisao = $arrayColumns[18];
        $this->deficienciaSurdez = $arrayColumns[19];
        $this->deficienciaAuditiva = $arrayColumns[20];
        $this->deficienciaSurdoCegueira = $arrayColumns[21];
        $this->deficienciaFisica = $arrayColumns[22];
        $this->deficienciaIntelectual = $arrayColumns[23];
        $this->deficienciaMultipla = $arrayColumns[24];
        $this->deficienciaAutismo = $arrayColumns[25];
        $this->deficienciaAltasHabilidades = $arrayColumns[26];
        $this->recursoLedor = $arrayColumns[27];
        $this->recursoTranscricao = $arrayColumns[28];
        $this->recursoGuia = $arrayColumns[29];
        $this->recursoTradutor = $arrayColumns[30];
        $this->recursoLeituraLabial = $arrayColumns[31];
        $this->recursoProvaAmpliada = $arrayColumns[32];
        $this->recursoProvaSuperampliada = $arrayColumns[33];
        $this->recursoAudio = $arrayColumns[34];
        $this->recursoLinguaPortuguesaSegundaLingua = $arrayColumns[35];
        $this->recursoVideoLibras = $arrayColumns[36];
        $this->recursoBraile = $arrayColumns[37];
        $this->recursoNenhum = $arrayColumns[38];
        $this->nis = $arrayColumns[39];
        $this->certidaoNascimento = $arrayColumns[40];
        $this->paisResidencia = $arrayColumns[41];
        $this->cep = $arrayColumns[42];
        $this->municipioResidencia = $arrayColumns[43];
        $this->localizacaoResidencia = $arrayColumns[44];
        $this->localizacaoDiferenciada = $arrayColumns[45];
        $this->escolaridade = $arrayColumns[46];
        $this->tipoEnsinoMedioCursado = $arrayColumns[47];
        $this->formacaoCurso = [
            $arrayColumns[48],
            $arrayColumns[51],
            $arrayColumns[54],
        ];
        $this->formacaoAnoConclusao = [
            $arrayColumns[49],
            $arrayColumns[52],
            $arrayColumns[55],
        ];
        $this->formacaoInstituicao = [
            $arrayColumns[50],
            $arrayColumns[53],
            $arrayColumns[56],
        ];
        $this->formacaoComponenteCurricular = [
            $arrayColumns[57],
            $arrayColumns[58],
            $arrayColumns[59],
        ];
        $this->posGraduacaoEspecializacao = $arrayColumns[60];
        $this->posGraduacaoMestrado = $arrayColumns[61];
        $this->posGraduacaoDoutorado = $arrayColumns[62];
        $this->posGraduacaoNaoPossui = $arrayColumns[63];
        $this->formacaoContinuadaCreche = $arrayColumns[64];
        $this->formacaoContinuadaPreEscola = $arrayColumns[65];
        $this->formacaoContinuadaAnosIniciaisFundamental = $arrayColumns[66];
        $this->formacaoContinuadaAnosFinaisFundamental = $arrayColumns[67];
        $this->formacaoContinuadaEnsinoMedio = $arrayColumns[68];
        $this->formacaoContinuadaEducacaoJovensAdultos = $arrayColumns[69];
        $this->formacaoContinuadaEducacaoEspecial = $arrayColumns[70];
        $this->formacaoContinuadaEducacaoIndigena = $arrayColumns[71];
        $this->formacaoContinuadaEducacaoCampo = $arrayColumns[72];
        $this->formacaoContinuadaEducacaoAmbiental = $arrayColumns[73];
        $this->formacaoContinuadaEducacaoDireitosHumanos = $arrayColumns[74];
        $this->formacaoContinuadaGeneroDiversidadeSexual = $arrayColumns[75];
        $this->formacaoContinuadaDireitosCriancaAdolescente = $arrayColumns[76];
        $this->formacaoContinuadaEducacaoRelacoesEticoRaciais = $arrayColumns[77];
        $this->formacaoContinuadaEducacaoGestaoEscolar = $arrayColumns[78];
        $this->formacaoContinuadaEducacaoOutros = $arrayColumns[79];
        $this->formacaoContinuadaEducacaoNenhum = $arrayColumns[80];
        $this->email = $arrayColumns[81];

        if ($this->escolaridade) {
            $this->tipos[self::TIPO_TEACHER] = true;
            $this->tipos[self::TIPO_MANAGER] = true;
        } else {
            $this->tipos[self::TIPO_STUDENT] = true;
        }
    }
}
