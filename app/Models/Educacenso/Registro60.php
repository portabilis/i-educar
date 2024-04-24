<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\PoderPublicoTransporte;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;

class Registro60 implements ItemOfRegistro30, RegistroEducacenso
{
    public $registro;

    public $inepEscola;

    public $codigoPessoa;

    public $inepAluno;

    public $inepTurma;

    public $matriculaAluno;

    public $etapaAluno;

    public $tipoItinerarioLinguagens;

    public $tipoItinerarioMatematica;

    public $tipoItinerarioCienciasNatureza;

    public $tipoItinerarioCienciasHumanas;

    public $tipoItinerarioFormacaoTecnica;

    public $tipoItinerarioIntegrado;

    public $composicaoItinerarioLinguagens;

    public $composicaoItinerarioMatematica;

    public $composicaoItinerarioCienciasNatureza;

    public $composicaoItinerarioCienciasHumanas;

    public $composicaoItinerarioFormacaoTecnica;

    public $codCursoProfissional;

    public $cursoItinerario;

    public $itinerarioConcomitante;

    public $tipoAtendimentoDesenvolvimentoFuncoesGognitivas;

    public $tipoAtendimentoDesenvolvimentoVidaAutonoma;

    public $tipoAtendimentoEnriquecimentoCurricular;

    public $tipoAtendimentoEnsinoInformaticaAcessivel;

    public $tipoAtendimentoEnsinoLibras;

    public $tipoAtendimentoEnsinoLinguaPortuguesa;

    public $tipoAtendimentoEnsinoSoroban;

    public $tipoAtendimentoEnsinoBraile;

    public $tipoAtendimentoEnsinoOrientacaoMobilidade;

    public $tipoAtendimentoEnsinoCaa;

    public $tipoAtendimentoEnsinoRecursosOpticosNaoOpticos;

    public $recebeEscolarizacaoOutroEspacao;

    public $transportePublico;

    public $poderPublicoResponsavelTransporte;

    public $veiculoTransporteBicicleta;

    public $veiculoTransporteMicroonibus;

    public $veiculoTransporteOnibus;

    public $veiculoTransporteTracaoAnimal;

    public $veiculoTransporteVanKonbi;

    public $veiculoTransporteOutro;

    public $veiculoTransporteAquaviarioCapacidade5;

    public $veiculoTransporteAquaviarioCapacidade5a15;

    public $veiculoTransporteAquaviarioCapacidade15a35;

    public $veiculoTransporteAquaviarioCapacidadeAcima35;

    public $modalidadeCurso;

    /**
     * @var string Campo usado somente na análise
     */
    public $nomeEscola;

    /**
     * @var string Campo usado somente na análise
     */
    public $nomeAluno;

    /**
     * @var string Campo usado somente na análise
     */
    public $codigoAluno;

    /**
     * @var string Campo usado somente na análise
     */
    public $tipoAtendimentoTurma;

    /**
     * @var string Campo usado somente na análise
     */
    public $codigoTurma;

    /**
     * @var string Campo usado somente na análise
     */
    public $etapaTurma;

    /**
     * @var array Campo usado somente na análise
     */
    public $estruturaCurricularTurma;

    /**
     * @var int Campo usado somente na análise
     */
    public $enturmacaoId;

    /**
     * @var string Campo usado somente na análise
     */
    public $codigoMatricula;

    /**
     * @var string Campo usado somente na análise
     */
    public $nomeTurma;

    /**
     * @var string Campo usado somente na análise
     */
    public $tipoAtendimentoMatricula;

    /**
     * @var string Campo usado somente na análise
     */
    public $tipoMediacaoTurma;

    /**
     * @var string Campo usado somente na análise
     */
    public $veiculoTransporteEscolar;

    /**
     * @var string Campo usado somente na análise
     */
    public $localFuncionamentoDiferenciadoTurma;

    /**
     * @var string Campo usado somente na análise
     */
    public $paisResidenciaAluno;

    /**
     * @return bool
     */
    public function transportePublicoRequired()
    {
        $tiposMediacaoPresencial = [
            TipoMediacaoDidaticoPedagogico::PRESENCIAL,
            TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
        ];

        return $this->tipoAtendimentoTurma == TipoAtendimentoTurma::ESCOLARIZACAO
            && in_array($this->tipoMediacaoTurma, $tiposMediacaoPresencial)
            && $this->paisResidenciaAluno == PaisResidencia::BRASIL;
    }

    /**
     * @return bool
     */
    public function veiculoTransporteEscolarRequired()
    {
        $transportePublico = [
            PoderPublicoTransporte::MUNICIPAL,
            PoderPublicoTransporte::ESTADUAL,
        ];

        return in_array($this->transportePublico, $transportePublico);
    }

    public function isAtividadeComplementarOrAee()
    {
        return $this->tipoAtendimentoTurma == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR ||
            $this->tipoAtendimentoTurma == TipoAtendimentoTurma::AEE;
    }

    /**
     * @return bool
     */
    public function recebeEscolarizacaoOutroEspacoIsRequired()
    {
        return $this->tipoAtendimentoTurma == TipoAtendimentoTurma::ESCOLARIZACAO &&
            $this->tipoMediacaoTurma == TipoMediacaoDidaticoPedagogico::PRESENCIAL &&
            $this->localFuncionamentoDiferenciadoTurma == \App_Model_LocalFuncionamentoDiferenciado::NAO_ESTA &&
            $this->localFuncionamentoDiferenciadoTurma == \App_Model_LocalFuncionamentoDiferenciado::SALA_ANEXA;
    }

    /**
     * @return bool
     */
    public function analisaDadosItinerario()
    {
        if (
            in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $this->estruturaCurricularTurma) &&
            count($this->estruturaCurricularTurma) === 1
        ) {
            return true;
        }

        $etapasValidas = [25, 26, 27, 28, 30, 31, 32, 33, 35, 36, 37, 38, 71, 74];

        if (
            in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $this->estruturaCurricularTurma) &&
            in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $this->estruturaCurricularTurma) &&
            in_array($this->etapaTurma, $etapasValidas)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function tipoItinerarioNaoPreenchido()
    {
        return
            !$this->tipoItinerarioLinguagens &&
            !$this->tipoItinerarioMatematica &&
            !$this->tipoItinerarioCienciasNatureza &&
            !$this->tipoItinerarioCienciasHumanas &&
            !$this->tipoItinerarioFormacaoTecnica &&
            !$this->tipoItinerarioIntegrado;
    }

    /**
     * @return bool
     */
    public function composicaoItinerarioNaoPreenchido()
    {
        return
            !$this->composicaoItinerarioLinguagens &&
            !$this->composicaoItinerarioMatematica &&
            !$this->composicaoItinerarioCienciasNatureza &&
            !$this->composicaoItinerarioCienciasHumanas &&
            !$this->composicaoItinerarioFormacaoTecnica;
    }

    public function etapaTurmaDescritiva()
    {
        $etapasEducacenso = loadJson('educacenso_json/etapas_ensino.json');

        return $etapasEducacenso[$this->etapaTurma];
    }

    public function getCodigoPessoa()
    {
        return $this->codigoPessoa;
    }

    public function getCodigoAluno()
    {
        return $this->codigoAluno;
    }

    public function getCodigoServidor()
    {
        return null;
    }
}
