<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use Transporte_Model_Responsavel;

require_once __DIR__ . '/../../../ieducar/modules/Transporte/Model/Responsavel.php';

class Registro60 implements RegistroEducacenso, ItemOfRegistro30
{
    public $registro;
    public $inepEscola;
    public $codigoPessoa;
    public $inepAluno;
    public $codigoTUrma;
    public $inepTurma;
    public $matriculaAluno;
    public $etapaAluno;
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
     * @return bool
     */
    public function transportePublicoRequired()
    {
        $tiposMediacaoPresencialSemiPresencial = [
            TipoMediacaoDidaticoPedagogico::PRESENCIAL,
            TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
        ];

        return $this->tipoAtendimentoTurma == TipoAtendimentoTurma::ESCOLARIZACAO && in_array($this->tipoMediacaoTurma, $tiposMediacaoPresencialSemiPresencial);
    }

    /**
     * @return bool
     */
    public function veiculoTransporteEscolarRequired()
    {
        $transportePublico = [
            Transporte_Model_Responsavel::MUNICIPAL,
            Transporte_Model_Responsavel::ESTADUAL,
        ];

        return in_array($this->transportePublico, $transportePublico);
    }

    public function isAtividadeComplementarOrAee()
    {
        return $this->tipoAtendimentoTurma == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR ||
            $this->tipoAtendimentoTurma == TipoAtendimentoTurma::AEE;
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
