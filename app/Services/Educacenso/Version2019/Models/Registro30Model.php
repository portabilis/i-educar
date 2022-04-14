<?php

namespace App\Services\Educacenso\Version2019\Models;

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
        $this->justificativaFaltaDocumentacao = $arrayColumns[41];
        $this->paisResidencia = $arrayColumns[42];
        $this->cep = $arrayColumns[43];
        $this->municipioResidencia = $arrayColumns[44];
        $this->localizacaoResidencia = $arrayColumns[45];
        $this->localizacaoDiferenciada = $arrayColumns[46];
        $this->escolaridade = $arrayColumns[47];
        $this->tipoEnsinoMedioCursado = $arrayColumns[48];
        $this->formacaoCurso = [
            $arrayColumns[49],
            $arrayColumns[52],
            $arrayColumns[55],
        ];
        $this->formacaoAnoConclusao = [
            $arrayColumns[50],
            $arrayColumns[53],
            $arrayColumns[56],
        ];
        $this->formacaoInstituicao = [
            $arrayColumns[51],
            $arrayColumns[54],
            $arrayColumns[57],
        ];
        $this->formacaoComponenteCurricular = [
            $arrayColumns[58],
            $arrayColumns[59],
            $arrayColumns[60],
        ];
        $this->posGraduacaoEspecializacao = $arrayColumns[61];
        $this->posGraduacaoMestrado = $arrayColumns[62];
        $this->posGraduacaoDoutorado = $arrayColumns[63];
        $this->posGraduacaoNaoPossui = $arrayColumns[64];
        $this->formacaoContinuadaCreche = $arrayColumns[65];
        $this->formacaoContinuadaPreEscola = $arrayColumns[66];
        $this->formacaoContinuadaAnosIniciaisFundamental = $arrayColumns[67];
        $this->formacaoContinuadaAnosFinaisFundamental = $arrayColumns[68];
        $this->formacaoContinuadaEnsinoMedio = $arrayColumns[69];
        $this->formacaoContinuadaEducacaoJovensAdultos = $arrayColumns[70];
        $this->formacaoContinuadaEducacaoEspecial = $arrayColumns[71];
        $this->formacaoContinuadaEducacaoIndigena = $arrayColumns[72];
        $this->formacaoContinuadaEducacaoCampo = $arrayColumns[73];
        $this->formacaoContinuadaEducacaoAmbiental = $arrayColumns[74];
        $this->formacaoContinuadaEducacaoDireitosHumanos = $arrayColumns[75];
        $this->formacaoContinuadaGeneroDiversidadeSexual = $arrayColumns[76];
        $this->formacaoContinuadaDireitosCriancaAdolescente = $arrayColumns[77];
        $this->formacaoContinuadaEducacaoRelacoesEticoRaciais = $arrayColumns[78];
        $this->formacaoContinuadaEducacaoGestaoEscolar = $arrayColumns[79];
        $this->formacaoContinuadaEducacaoOutros = $arrayColumns[80];
        $this->formacaoContinuadaEducacaoNenhum = $arrayColumns[81];
        $this->email = $arrayColumns[82];

        if ($this->escolaridade) {
            $this->tipos[self::TIPO_TEACHER] = true;
            $this->tipos[self::TIPO_MANAGER] = true;
        } else {
            $this->tipos[self::TIPO_STUDENT] = true;
        }
    }
}
