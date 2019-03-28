<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\UsoInternet;

class Registro10 implements RegistroEducacenso
{
    public const PREDIO_ESCOLAR = 3;
    /**
      * @var string
      */
    public $codEscola;

    /**
      * @var string
      */
    public $localFuncionamento;

    /**
      * @var string
      */
    public $condicao;

    /**
      * @var string
      */
    public $aguaConsumida;

    /**
      * @var string
      */
    public $aguaRedePublica;

    /**
      * @var string
      */
    public $aguaPocoArtesiano;

    /**
      * @var string
      */
    public $aguaCacimbaCisternaPoco;

    /**
      * @var string
      */
    public $aguaFonteRio;

    /**
      * @var string
      */
    public $aguaInexistente;

    /**
      * @var string
      */
    public $energiaRedePublica;

    /**
      * @var string
      */
    public $energiaGerador;

    /**
      * @var string
      */
    public $energiaOutros;

    /**
      * @var string
      */
    public $energiaInexistente;

    /**
      * @var string
      */
    public $esgotoRedePublica;

    /**
      * @var string
      */
    public $esgotoFossa;

    /**
      * @var string
      */
    public $esgotoInexistente;

    /**
      * @var string
      */
    public $lixoColetaPeriodica;

    /**
      * @var string
      */
    public $lixoQueima;

    /**
      * @var string
      */
    public $lixoJogaOutraArea;

    /**
      * @var string
      */
    public $lixoRecicla;

    /**
      * @var string
      */
    public $lixoEnterra;

    /**
      * @var string
      */
    public $lixoOutros;

    /**
      * @var array
      */
    public $tratamentoLixo;

    /**
      * @var string
      */
    public $dependenciaSalaDiretoria;

    /**
      * @var string
      */
    public $dependenciaSalaProfessores;

    /**
      * @var string
      */
    public $dependnciaSalaSecretaria;

    /**
      * @var string
      */
    public $dependenciaLaboratorioInformatica;

    /**
      * @var string
      */
    public $dependenciaLaboratorioCiencias;

    /**
      * @var string
      */
    public $dependenciaSalaAee;

    /**
      * @var string
      */
    public $dependenciaQuadraCoberta;

    /**
      * @var string
      */
    public $dependenciaQuadraDescoberta;

    /**
      * @var string
      */
    public $dependenciaCozinha;

    /**
      * @var string
      */
    public $dependenciaBiblioteca;

    /**
      * @var string
      */
    public $dependenciaSalaLeitura;

    /**
      * @var string
      */
    public $dependenciaParqueInfantil;

    /**
      * @var string
      */
    public $dependenciaBercario;

    /**
      * @var string
      */
    public $dependenciaBanheiroFora;

    /**
      * @var string
      */
    public $dependenciaBanheiroDentro;

    /**
      * @var string
      */
    public $dependenciaBanheiroInfantil;

    /**
      * @var string
      */
    public $dependenciaBanheiroDeficiente;

    /**
      * @var string
      */
    public $dependenciaBanheiroChuveiro;

    /**
      * @var string
      */
    public $dependenciaRefeitorio;

    /**
      * @var string
      */
    public $dependenciaDispensa;

    /**
      * @var string
      */
    public $dependenciaAumoxarifado;

    /**
      * @var string
      */
    public $dependenciaAuditorio;

    /**
      * @var string
      */
    public $dependenciaPatioCoberto;

    /**
      * @var string
      */
    public $dependenciaPatioDescoberto;

    /**
      * @var string
      */
    public $dependenciaAlojamentoAluno;

    /**
      * @var string
      */
    public $dependenciaAlojamentoProfessor;

    /**
      * @var string
      */
    public $dependenciaAreaVerde;

    /**
      * @var string
      */
    public $dependenciaLavanderia;

    /**
      * @var string
      */
    public $dependenciaNenhumaRelacionada;

    /**
      * @var string
      */
    public $numeroSalasUtilizadasDentroPredio;

    /**
      * @var string
      */
    public $numeroSalasUtilizadasForaPredio;

    /**
      * @var string
      */
    public $televisoes;

    /**
      * @var string
      */
    public $videocassetes;

    /**
      * @var string
      */
    public $dvds;

    /**
      * @var string
      */
    public $antenasParabolicas;

    /**
      * @var string
      */
    public $copiadoras;

    /**
      * @var string
      */
    public $retroprojetores;

    /**
      * @var string
      */
    public $impressoras;

    /**
      * @var string
      */
    public $aparelhosDeSom;

    /**
      * @var string
      */
    public $projetoresDigitais;

    /**
      * @var string
      */
    public $faxs;

    /**
      * @var string
      */
    public $maquinasFotograficas;

    /**
      * @var string
      */
    public $computadores;

    /**
      * @var string
      */
    public $computadoresAdministrativo;

    /**
      * @var string
      */
    public $computadoresAlunos;

    /**
      * @var string
      */
    public $impressorasMultifuncionais;

    /**
      * @var string
      */
    public $totalFuncionario;

    /**
      * @var string
      */
    public $atendimentoAee;

    /**
      * @var string
      */
    public $atividadeComplementar;

    /**
      * @var string
      */
    public $localizacaoDiferenciada;

    /**
      * @var string
      */
    public $materiaisDidaticosEspecificos;

    /**
      * @var string
      */
    public $linguaMinistrada;

    /**
      * @var string
      */
    public $educacaoIndigena;

    /**
      * @var string
      */
    public $nomeEscola;

    /**
      * @var string
      */
    public $predioCompartilhadoOutraEscola;

    /**
      * @var string
      */
    public $codigoInepEscolaCompartilhada;

    /**
     * @var string
     */
    public $possuiDependencias;

    /**
     * @var string
     */
    public $salasGerais;

    /**
     * @var string
     */
    public $salasFuncionais;

    /**
     * @var string
     */
    public $banheiros;

    /**
     * @var string
     */
    public $laboratorios;

    /**
     * @var string
     */
    public $salasAtividades;

    /**
     * @var string
     */
    public $dormitorios;

    /**
     * @var string
     */
    public $areasExternas;

    /**
     * @var array
     */
    public $recursosAcessibilidade;

    /**
     * @var string
     */
    public $usoInternet;

    /**
     * @var string
     */
    public $equipamentosAcessoInternet;

    /**
     * @return bool
     */
    public function predioEscolar()
    {
        return in_array(self::PREDIO_ESCOLAR, $this->localFuncionamento);
    }

    /**
     * @return bool
     */
    public function existeAbastecimentoAgua()
    {
        return $this->aguaRedePublica ||
            $this->aguaPocoArtesiano ||
            $this->aguaCacimbaCisternaPoco ||
            $this->aguaFonteRio ||
            $this->aguaInexistente;
    }

    /**
     * @return bool
     */
    public function aguaInexistenteEOutrosCamposPreenchidos()
    {
        return $this->aguaInexistente == 1 &&
            ($this->aguaRedePublica || $this->aguaPocoArtesiano || $this->aguaCacimbaCisternaPoco || $this->aguaFonteRio);
    }

    /**
     * @return bool
     */
    public function existeAbastecimentoEnergia()
    {
        return $this->energiaRedePublica ||
            $this->energiaGerador ||
            $this->energiaOutros ||
            $this->energiaInexistente;
    }

    /**
     * @return bool
     */
    public function energiaInexistenteEOutrosCamposPreenchidos()
    {
        return $this->energiaInexistente == 1 &&
            ($this->energiaRedePublica || $this->energiaGerador || $this->energiaOutros);
    }

    /**
     * @return bool
     */
    public function existeEsgotoSanitario()
    {
        return $this->esgotoRedePublica ||
            $this->esgotoFossa ||
            $this->esgotoInexistente;
    }

    /**
     * @return bool
     */
    public function esgotoSanitarioInexistenteEOutrosCamposPreenchidos()
    {
        return $this->esgotoInexistente && ($this->esgotoRedePublica || $this->esgotoFossa);
    }

    /**
     * @return bool
     */
    public function existeDestinacaoLixo()
    {
        return $this->lixoColetaPeriodica ||
            $this->lixoQueima ||
            $this->lixoJogaOutraArea ||
            $this->lixoRecicla ||
            $this->lixoEnterra ||
            $this->lixoOutros;
    }

    /**
     * @return bool
     */
    public function existeTratamentoLixo()
    {
        return !empty($this->tratamentoLixo);
    }

    /**
     * @return bool
     */
    public function tratamentoLixoInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(TratamentoLixo::NAO_FAZ, $this->tratamentoLixo) && count($this->tratamentoLixo) > 1;
    }

    /**
     * @return bool
     */
    public function existeRecursosAcessibilidade()
    {
        return !empty($this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(RecursosAcessibilidade::NENHUM, $this->recursosAcessibilidade) && count($this->recursosAcessibilidade) > 1;
    }

    /**
     * @return bool
     */
    public function existeDependencia()
    {
        return !empty(array_filter(
            [
                $this->salasGerais,
                $this->salasFuncionais,
                $this->banheiros,
                $this->laboratorios,
                $this->salasAtividades,
                $this->dormitorios,
                $this->areasExternas,
            ]
        ));
    }

    /**
     * @return bool
     */
    public function existeUsoInternet()
    {
        return !empty($this->usoInternet);
    }

    /**
     * @return bool
     */
    public function usoInternetInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(UsoInternet::NAO_POSSUI, $this->usoInternet) && count($this->usoInternet) > 1;
    }

    /**
     * @return bool
     */
    public function usaInternet()
    {
        return !in_array(UsoInternet::NAO_POSSUI, $this->usoInternet) && count($this->usoInternet) > 0;
    }

    /**
     * @return bool
     */
    public function existeEquipamentos()
    {
        return $this->televisoes ||
            $this->videocassetes ||
            $this->dvds ||
            $this->antenasParabolicas ||
            $this->copiadoras ||
            $this->retroprojetores ||
            $this->impressoras ||
            $this->aparelhosDeSom ||
            $this->projetoresDigitais ||
            $this->faxs ||
            $this->maquinasFotograficas ||
            $this->computadores ||
            $this->computadoresAdministrativo ||
            $this->computadoresAlunos ||
            $this->impressorasMultifuncionais;
    }
}
