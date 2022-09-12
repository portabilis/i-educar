<?php

namespace App\Services\Educacenso\Version2022\Models;

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
        $this->certidaoNascimento = $arrayColumns[39];
        $this->paisResidencia = $arrayColumns[40];
        $this->cep = $arrayColumns[41];
        $this->municipioResidencia = $arrayColumns[42];
        $this->localizacaoResidencia = $arrayColumns[43];
        $this->localizacaoDiferenciada = $arrayColumns[44];
        $this->escolaridade = $arrayColumns[45];
        $this->tipoEnsinoMedioCursado = $arrayColumns[46];
        $this->formacaoCurso = [
            $arrayColumns[47],
            $arrayColumns[50],
            $arrayColumns[53],
        ];
        $this->formacaoAnoConclusao = [
            $arrayColumns[48],
            $arrayColumns[51],
            $arrayColumns[54],
        ];
        $this->formacaoInstituicao = [
            $arrayColumns[49],
            $arrayColumns[52],
            $arrayColumns[55],
        ];

        $this->complementacaoPedagogica = array_filter([
            $arrayColumns[56],
            $arrayColumns[57],
            $arrayColumns[58],
        ]);

        $this->posGraduacoes = [];

        if (!empty($arrayColumns[59])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[59],
                'area' => $arrayColumns[60],
                'ano_conclusao' => $arrayColumns[61],
            ];
        }

        if (!empty($arrayColumns[62])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[62],
                'area' => $arrayColumns[63],
                'ano_conclusao' => $arrayColumns[64],
            ];
        }

        if (!empty($arrayColumns[65])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[65],
                'area' => $arrayColumns[66],
                'ano_conclusao' => $arrayColumns[67],
            ];
        }

        if (!empty($arrayColumns[68])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[68],
                'area' => $arrayColumns[69],
                'ano_conclusao' => $arrayColumns[70],
            ];
        }

        if (!empty($arrayColumns[71])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[71],
                'area' => $arrayColumns[72],
                'ano_conclusao' => $arrayColumns[73],
            ];
        }

        if (!empty($arrayColumns[74])) {
            $this->posGraduacoes[] = [
                'tipo' => $arrayColumns[74],
                'area' => $arrayColumns[75],
                'ano_conclusao' => $arrayColumns[76],
            ];
        }

        $this->formacaoContinuadaCreche = $arrayColumns[78];
        $this->formacaoContinuadaPreEscola = $arrayColumns[79];
        $this->formacaoContinuadaAnosIniciaisFundamental = $arrayColumns[80];
        $this->formacaoContinuadaAnosFinaisFundamental = $arrayColumns[81];
        $this->formacaoContinuadaEnsinoMedio = $arrayColumns[82];
        $this->formacaoContinuadaEducacaoJovensAdultos = $arrayColumns[83];
        $this->formacaoContinuadaEducacaoEspecial = $arrayColumns[84];
        $this->formacaoContinuadaEducacaoIndigena = $arrayColumns[85];
        $this->formacaoContinuadaEducacaoCampo = $arrayColumns[86];
        $this->formacaoContinuadaEducacaoAmbiental = $arrayColumns[87];
        $this->formacaoContinuadaEducacaoDireitosHumanos = $arrayColumns[88];
        $this->formacaoContinuadaGeneroDiversidadeSexual = $arrayColumns[89];
        $this->formacaoContinuadaDireitosCriancaAdolescente = $arrayColumns[90];
        $this->formacaoContinuadaEducacaoRelacoesEticoRaciais = $arrayColumns[91];
        $this->formacaoContinuadaEducacaoGestaoEscolar = $arrayColumns[92];
        $this->formacaoContinuadaEducacaoOutros = $arrayColumns[93];
        $this->formacaoContinuadaEducacaoNenhum = $arrayColumns[94];
        $this->email = $arrayColumns[95];

        if ($this->escolaridade) {
            $this->tipos[self::TIPO_TEACHER] = true;
            $this->tipos[self::TIPO_MANAGER] = true;
        } else {
            $this->tipos[self::TIPO_STUDENT] = true;
        }
    }
}
