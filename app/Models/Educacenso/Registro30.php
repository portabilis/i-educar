<?php

namespace App\Models\Educacenso;


class Registro30 implements RegistroEducacenso
{
    CONST TIPO_MANAGER = 'manager';
    CONST TIPO_TEACHER = 'teacher';
    CONST TIPO_STUDENT = 'student';

    public $tipos = [];

    public $registro;

    public $inepEscola;

    public $codigoEscola;

    public $codigoPessoa;

    public $codigoAluno;

    public $codigoServidor;

    public $cpf;

    public $nomePessoa;

    public $dataNascimento;

    public $filiacao;

    public $filiacao1;

    public $filiacao2;

    public $sexo;

    public $raca;

    public $nacionalidade;

    public $paisNacionalidade;

    public $municipioNascimento;

    public $deficiencia;

    public $deficienciaCegueira;

    public $deficienciaBaixaVisao;

    public $deficienciaSurdez;

    public $deficienciaAuditiva;

    public $deficienciaSurdoCegueira;

    public $deficienciaFisica;

    public $deficienciaIntelectual;

    public $deficienciaMultipla;

    public $deficienciaAltasHabilidades;

    public $deficienciaAutismo;

    public $inepAluno;

    public $recursoLedor;

    public $recursoTranscricao;

    public $recursoGuia;

    public $recursoTradutor;

    public $recursoLeituraLabial;

    public $recursoProvaAmpliada;

    public $recursoProvaSuperampliada;

    public $recursoAudio;

    public $recursoLinguaPortuguesaSegundaLingua;

    public $recursoVideoLibras;

    public $recursoBraile;

    public $recursoNenhum;

    public $nis;

    public $certidaoNascimento;

    public $justificativaFaltaDocumentacao;

    public $inepServidor;

    public $codigoInstituicao;

    public $escolaridade;

    public $tipoEnsinoMedioCursado;

    public $formacaoCurso;

    public $formacaoAnoConclusao;

    public $formacaoInstituicao;

    public $formacaoComponenteCurricular;

    public $countPosGraduacao;

    public $posGraduacaoEspecializacao;

    public $posGraduacaoMestrado;

    public $posGraduacaoDoutorado;

    public $posGraduacaoNaoPossui;

    public $countFormacaoContinuada;

    public $formacaoContinuadaCreche;

    public $formacaoContinuadaPreEscola;

    public $formacaoContinuadaAnosIniciaisFundamental;

    public $formacaoContinuadaAnosFinaisFundamental;

    public $formacaoContinuadaEnsinoMedio;

    public $formacaoContinuadaEducacaoJovensAdultos;

    public $formacaoContinuadaEducacaoEspecial;

    public $formacaoContinuadaEducacaoIndigena;

    public $formacaoContinuadaEducacaoCampo;

    public $formacaoContinuadaEducacaoAmbiental;

    public $formacaoContinuadaEducacaoDireitosHumanos;

    public $formacaoContinuadaGeneroDiversidadeSexual;

    public $formacaoContinuadaDireitosCriancaAdolescente;

    public $formacaoContinuadaEducacaoRelacoesEticoRaciais;

    public $formacaoContinuadaEducacaoGestaoEscolar;

    public $formacaoContinuadaEducacaoOutros;

    public $formacaoContinuadaEducacaoNenhum;

    public $email;

    public $paisResidencia;

    public $cep;

    public $municipioResidencia;

    public $localizacaoResidencia;

    public $localizacaoDiferenciada;

    public $nomeEscola;

    public $nomeNacionalidade;

    public $arrayDeficiencias;

    public $recursosProvaInep;

    /**
     * @var Registro60
     */
    public $dadosAluno;

    public $inepPessoa;

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

    /**
     * @return bool
     */
    public function isManager()
    {
        return isset($this->tipos[self::TIPO_MANAGER]);
    }

    /**
     * @return bool
     */
    public function isTeacher()
    {
        return isset($this->tipos[self::TIPO_TEACHER]);
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return isset($this->tipos[self::TIPO_STUDENT]);
    }

    public function semDocumentacao()
    {
        return empty($this->cpf) && empty($this->nis) && empty($this->certidaoNascimento);
    }

    public function getInep()
    {
        if ($this->isStudent()) {
            return $this->inepAluno;
        }

        return $this->inepServidor;
    }

    /**
     * @return integer
     */
    public function deficienciaMultipla()
    {
        $arrayDeficienciasMultiplas = [
            $this->deficienciaCegueira,
            $this->deficienciaBaixaVisao,
            $this->deficienciaSurdez,
            $this->deficienciaAuditiva,
            $this->deficienciaSurdoCegueira,
            $this->deficienciaFisica,
            $this->deficienciaIntelectual,
        ];

        if (empty($this->arrayDeficiencias)) {
            return null;
        }

        return count(array_keys($arrayDeficienciasMultiplas, 1)) > 1 ? 1 : 0;
    }

    /**
     * @return array
     */
    public function cursosDeFormacaoSuperiorExtintos()
    {
        return [
            '145F14' => 'Letras - Língua Estrangeira - Licenciatura',
            '145F17' => 'Letras - Língua Portuguesa e Estrangeira - Licenciatura',
            '220L03' => 'Letras - Língua Portuguesa e Estrangeira - Bacharelado',
            '222L01' => 'Letras - Língua Estrangeira - Bacharelado',
            '443C01' => 'Ciência da Terra - Licenciatura',
            '999990' => 'Outro curso de formação superior - Licenciatura',
            '999991' => 'Outro curso de formação superior - Bacharelado',
            '999992' => 'Outro curso de formação superior - Tecnológico',
        ];
    }
}
