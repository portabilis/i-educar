<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\Transtornos;

class Registro30 implements RegistroEducacenso
{
    public const TIPO_MANAGER = 'manager';

    public const TIPO_TEACHER = 'teacher';

    public const TIPO_STUDENT = 'student';

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

    public $povoIndigena;

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

    public $deficienciaVisaoMonocular;

    public $transtorno;

    public $transtornoDiscalculia;

    public $transtornoDisgrafia;

    public $transtornoDislalia;

    public $transtornoDislexia;

    public $transtornoTdah;

    public $transtornoTpac;

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

    public $provaBraile;

    public $recursoTempoAdicional;

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

    public $complementacaoPedagogica;

    public $posGraduacoes;

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

    public $formacaoContinuadaEducacaoBilingueSurdos;

    public $formacaoContinuadaEducacaoTecnologiaInformacaoComunicacao;

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
     * @return int
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
            $this->deficienciaVisaoMonocular,
        ];

        if (
            empty($this->arrayDeficiencias) ||
            count(Registro30::removeTranstornosArrayDeficiencias(transformStringFromDBInArray($this->arrayDeficiencias))) === 0
        ) {
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

    /**
     * Remove "Transtornos" do array de deficiências informado
     *
     *
     * @return array
     */
    public static function removeTranstornosArrayDeficiencias($arrayDeficiencias)
    {
        return array_diff($arrayDeficiencias, [
            Transtornos::DISCALCULIA,
            Transtornos::DISGRAFIA,
            Transtornos::DISLALIA,
            Transtornos::DISLEXIA,
            Transtornos::TDAH,
            Transtornos::TPAC,
        ]);
    }
}
