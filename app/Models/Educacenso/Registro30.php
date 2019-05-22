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

}
