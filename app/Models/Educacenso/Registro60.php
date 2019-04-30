<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;

class Registro60 implements RegistroEducacenso
{
    public $registro;
    public $inepEscola;
    public $codigoPessoa;
    public $inepAluno;
    public $codigoTUrma;
    public $inepTurma;
    public $matriculaAluno;
    public $etapaEducacenso;
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
}
