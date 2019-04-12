<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;

class Registro10 implements RegistroEducacenso
{
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
    public $codigoLinguaIndigena;

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
     * @var array
     */
    public $salasGerais;

    /**
     * @var array
     */
    public $salasFuncionais;

    /**
     * @var array
     */
    public $banheiros;

    /**
     * @var array
     */
    public $laboratorios;

    /**
     * @var array
     */
    public $salasAtividades;

    /**
     * @var array
     */
    public $dormitorios;

    /**
     * @var array
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
    public $acessoInternet;

    /**
     * @var string
     */
    public $equipamentosAcessoInternet;

    /**
     * @var array
     */
    public $equipamentos;

    /**
     * @var array
     */
    public $redeLocal;

    /**
     * @var int
     */
    public $qtdSecretarioEscolar;

    /**
     * @var int
     */
    public $qtdAuxiliarAdministrativo;

    /**
     * @var int
     */
    public $qtdApoioPedagogico;

    /**
     * @var int
     */
    public $qtdCoordenadorTurno;

    /**
     * @var int
     */
    public $qtdTecnicos;

    /**
     * @var int
     */
    public $qtdBibliotecarios;

    /**
     * @var int
     */
    public $qtdSegurancas;

    /**
     * @var int
     */
    public $qtdAuxiliarServicosGerais;

    /**
     * @var int
     */
    public $qtdNutricionistas;

    /**
     * @var int
     */
    public $qtdProfissionaisPreparacao;

    /**
     * @var int
     */
    public $qtdBombeiro;

    /**
     * @var int
     */
    public $qtdPsicologo;

    /**
     * @var int
     */
    public $qtdFonoaudiologo;

    /**
     * @var array
     */
    public $orgaosColegiados;

    /**
     * @var string
     */
    public $exameSelecaoIngresso;

    /**
     * @var array
     */
    public $reservaVagasCotas;

    /**
     * @var int
     */
    public $alimentacaoEscolarAlunos;

    /**
     * @return bool
     */
    public function predioEscolar()
    {
        return in_array(LocalFuncionamento::PREDIO_ESCOLAR, $this->localFuncionamento);
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
                array_filter($this->salasGerais),
                array_filter($this->salasFuncionais),
                array_filter($this->banheiros),
                array_filter($this->laboratorios),
                array_filter($this->salasAtividades),
                array_filter($this->dormitorios),
                array_filter($this->areasExternas),
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
    public function alunosUsamInternet()
    {
        return in_array(UsoInternet::ALUNOS, $this->usoInternet);
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
    public function possuiComputadores()
    {
        return in_array(Equipamentos::COMPUTADORES, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function redeLocalInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(RedeLocal::NENHUMA, $this->redeLocal) && count($this->redeLocal) > 1;
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(ReservaVagasCotas::NAO_POSSUI, $this->reservaVagasCotas) && count($this->reservaVagasCotas) > 1;
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosInexistenteEOutrosCamposPreenchidos()
    {
        return in_array(OrgaosColegiados::NENHUM, $this->orgaosColegiados) && count($this->orgaosColegiados) > 1;
    }

    /**
     * @return bool
     */
    public function quantidadeProfissionaisPreenchida()
    {

        return $this->qtdSecretarioEscolar ||
            $this->qtdAuxiliarAdministrativo ||
            $this->qtdApoioPedagogico ||
            $this->qtdCoordenadorTurno ||
            $this->qtdTecnicos ||
            $this->qtdBibliotecarios ||
            $this->qtdSegurancas ||
            $this->qtdAuxiliarServicosGerais ||
            $this->qtdNutricionistas ||
            $this->qtdProfissionaisPreparacao ||
            $this->qtdBombeiro ||
            $this->qtdPsicologo ||
            $this->qtdFonoaudiologo;
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
