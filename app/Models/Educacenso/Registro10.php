<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\AreasExternas;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Model\Dormitorios;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\EquipamentosAcessoInternet;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\SalasAtividades;
use iEducar\Modules\Educacenso\Model\SalasGerais;
use iEducar\Modules\Educacenso\Model\SalasFuncionais;
use iEducar\Modules\Educacenso\Model\Banheiros;
use iEducar\Modules\Educacenso\Model\Laboratorios;
use iEducar\Modules\Educacenso\Model\OrganizacaoEnsino;
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
    public $codigoInep;

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
    public $aguaPotavelConsumo;

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
    public $esgotoFossaComum;

    /**
      * @var string
      */
    public $esgotoInexistente;

    /**
      * @var string
      */
    public $esgotoFossaRudimentar;

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
    public $lixoDestinacaoPoderPublico;

    /**
      * @var string
      */
    public $lixoEnterra;

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
    public $numeroSalasClimatizadas;

    /**
      * @var string
      */
    public $numeroSalasAcessibilidade;

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
    public $lousasDigitais;

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
    public $quantidadeComputadoresAlunosMesa;

    /**
      * @var string
      */
    public $quantidadeComputadoresAlunosPortateis;

    /**
      * @var string
      */
    public $quantidadeComputadoresAlunosTablets;

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
      * @var array
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
    public $codigoInepEscolaCompartilhada2;

    /**
      * @var string
      */
    public $codigoInepEscolaCompartilhada3;

    /**
      * @var string
      */
    public $codigoInepEscolaCompartilhada4;

    /**
      * @var string
      */
    public $codigoInepEscolaCompartilhada5;

    /**
      * @var string
      */
    public $codigoInepEscolaCompartilhada6;

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
     * @var int
     */
    public $organizacaoEnsino;

    /**
     * @var int
     */
    public $instrumentosPedagogicos;

    /**
     * @var int
     */
    public $compartilhaEspacosAtividadesIntegracao;

    /**
     * @var int
     */
    public $usaEspacosEquipamentosAtividadesRegulares;

    /**
     * @var int
     */
    public $projetoPoliticoPedagogico;

    /**
     * @var int
     */
    public $url;

    /**
     * @return bool
     */
    public function salasOutraEscola()
    {
        return in_array(LocalFuncionamento::SALAS_OUTRA_ESCOLA, $this->localFuncionamento);
    }

    /**
     * @return bool
     */
    public function galpao()
    {
        return in_array(LocalFuncionamento::GALPAO, $this->localFuncionamento);
    }

    /**
     * @return bool
     */
    public function unidadeAtendimentoSocioeducativa()
    {
        return in_array(LocalFuncionamento::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA, $this->localFuncionamento);
    }

    /**
     * @return bool
     */
    public function unidadePrisional()
    {
        return in_array(LocalFuncionamento::UNIDADE_PRISIONAL, $this->localFuncionamento);
    }

    /**
     * @return bool
     */
    public function outros()
    {
        return in_array(LocalFuncionamento::OUTROS, $this->localFuncionamento);
    }

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
            $this->esgotoFossaComum ||
            $this->esgotoFossaRudimentar ||
            $this->esgotoInexistente;
    }

    /**
     * @return bool
     */
    public function esgotoSanitarioInexistenteEOutrosCamposPreenchidos()
    {
        return $this->esgotoInexistente && ($this->esgotoRedePublica || $this->esgotoFossaComum || $this->esgotoFossaRudimentar);
    }

    /**
     * @return bool
     */
    public function existeDestinacaoLixo()
    {
        return $this->lixoColetaPeriodica ||
            $this->lixoQueima ||
            $this->lixoJogaOutraArea ||
            $this->lixoDestinacaoPoderPublico ||
            $this->lixoEnterra;
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
    public function tratamentoLixoNaoFaz()
    {
        return in_array(TratamentoLixo::NAO_FAZ, $this->tratamentoLixo);
    }

    /**
     * @return bool
     */
    public function tratamentoLixoSeparacao()
    {
        return in_array(TratamentoLixo::SEPARACAO, $this->tratamentoLixo);
    }

    /**
     * @return bool
     */
    public function tratamentoLixoReaproveitamento()
    {
        return in_array(TratamentoLixo::REAPROVEITAMENTO, $this->tratamentoLixo);
    }

    /**
     * @return bool
     */
    public function tratamentoLixoReciclagem()
    {
        return in_array(TratamentoLixo::RECICLAGEM, $this->tratamentoLixo);
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
    public function salasGeraisSalaDiretoria()
    {
        return in_array(SalasGerais::SALA_DIRETORIA, $this->salasGerais);
    }

    /**
     * @return bool
     */
    public function salasGeraisSalaSecretaria()
    {
        return in_array(SalasGerais::SALA_SECRETARIA, $this->salasGerais);
    }

    /**
     * @return bool
     */
    public function salasGeraisSalaProfessores()
    {
        return in_array(SalasGerais::SALA_PROFESSORES, $this->salasGerais);
    }

    /**
     * @return bool
     */
    public function salasGeraisBiblioteca()
    {
        return in_array(SalasGerais::BIBLIOTECA, $this->salasGerais);
    }

    /**
     * @return bool
     */
    public function salasGeraisAuditorio()
    {
        return in_array(SalasGerais::AUDITORIO, $this->salasGerais);
    }

    /**
     * @return bool
     */
    public function salasFuncionaisCozinha()
    {
        return in_array(SalasFuncionais::COZINHA, $this->salasFuncionais);
    }

    /**
     * @return bool
     */
    public function salasFuncionaisRefeitorio()
    {
        return in_array(SalasFuncionais::REFEITORIO, $this->salasFuncionais);
    }

    /**
     * @return bool
     */
    public function salasFuncionaisDespensa()
    {
        return in_array(SalasFuncionais::DESPENSA, $this->salasFuncionais);
    }

    /**
     * @return bool
     */
    public function salasFuncionaisAlmoxarifado()
    {
        return in_array(SalasFuncionais::ALMOXARIFADO, $this->salasFuncionais);
    }

    /**
     * @return bool
     */
    public function banheirosBanheiro()
    {
        return in_array(Banheiros::BANHEIRO, $this->banheiros);
    }

    /**
     * @return bool
     */
    public function banheirosBanheiroFuncionarios()
    {
        return in_array(Banheiros::BANHEIRO_FUNCIONARIOS, $this->banheiros);
    }

    /**
     * @return bool
     */
    public function banheirosBanheiroChuveiro()
    {
        return in_array(Banheiros::BANHEIRO_CHUVEIRO, $this->banheiros);
    }

    /**
     * @return bool
     */
    public function banheirosBanheiroEducacaoInfantil()
    {
        return in_array(Banheiros::BANHEIRO_EDUCACAO_INFANTIL, $this->banheiros);
    }

    /**
     * @return bool
     */
    public function banheirosBanheiroAcessivel()
    {
        return in_array(Banheiros::BANHEIRO_ACESSIVEL, $this->banheiros);
    }

    /**
     * @return bool
     */
    public function laboratoriosInformatica()
    {
        return in_array(Laboratorios::INFORMATICA, $this->laboratorios);
    }

    /**
     * @return bool
     */
    public function laboratoriosCiencias()
    {
        return in_array(Laboratorios::CIENCIAS, $this->laboratorios);
    }

    /**
     * @return bool
     */
    public function salasAtividadesLeitura()
    {
        return in_array(SalasAtividades::LEITURA, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesAtelie()
    {
        return in_array(SalasAtividades::ATELIE, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesMusica()
    {
        return in_array(SalasAtividades::MUSICA, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesEstudioDanca()
    {
        return in_array(SalasAtividades::ESTUDIO_DANCA, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesMultiuso()
    {
        return in_array(SalasAtividades::MULTIUSO, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesRecursosAee()
    {
        return in_array(SalasAtividades::RECURSOS_AEE, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function salasAtividadesRepousoAluno()
    {
        return in_array(SalasAtividades::REPOUSO_ALUNO, $this->salasAtividades);
    }

    /**
     * @return bool
     */
    public function dormitoriosAluno()
    {
        return in_array(Dormitorios::ALUNO, $this->dormitorios);
    }

    /**
     * @return bool
     */
    public function dormitoriosProfessor()
    {
        return in_array(Dormitorios::PROFESSOR, $this->dormitorios);
    }

    /**
     * @return bool
     */
    public function areasExternasQuadraCoberta()
    {
        return in_array(AreasExternas::QUADRA_COBERTA, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasQuadraDescoberta()
    {
        return in_array(AreasExternas::QUADRA_DESCOBERTA, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasPatioCoberto()
    {
        return in_array(AreasExternas::PATIO_COBERTO, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasPatioDescoberto()
    {
        return in_array(AreasExternas::PATIO_DESCOBERTO, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasParqueInfantil()
    {
        return in_array(AreasExternas::PARQUE_INFANTIL, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasPiscina()
    {
        return in_array(AreasExternas::PISCINA, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasAreaVerde()
    {
        return in_array(AreasExternas::AREA_VERDE, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasTerreirao()
    {
        return in_array(AreasExternas::TERREIRAO, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function areasExternasViveiro()
    {
        return in_array(AreasExternas::VIVEIRO, $this->areasExternas);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeNenhum()
    {
        return in_array(RecursosAcessibilidade::NENHUM, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeCorrimao()
    {
        return in_array(RecursosAcessibilidade::CORRIMAO, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeElevador()
    {
        return in_array(RecursosAcessibilidade::ELEVADOR, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadePisosTateis()
    {
        return in_array(RecursosAcessibilidade::PISOS_TATEIS, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadePortasVaoLivre()
    {
        return in_array(RecursosAcessibilidade::PORTAS_VAO_LIVRE, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeRampas()
    {
        return in_array(RecursosAcessibilidade::RAMPAS, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeSinalizacaoSonora()
    {
        return in_array(RecursosAcessibilidade::SINALIZACAO_SONORA, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeSinalizacaoTatil()
    {
        return in_array(RecursosAcessibilidade::SINALIZACAO_TATIL, $this->recursosAcessibilidade);
    }

    /**
     * @return bool
     */
    public function recursosAcessibilidadeSinalizacaoVisual()
    {
        return in_array(RecursosAcessibilidade::SINALIZACAO_VISUAL, $this->recursosAcessibilidade);
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
    public function possuiImpressoras()
    {
        return in_array(Equipamentos::IMPRESSORAS, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function possuiImpressorasMultifuncionais()
    {
        return in_array(Equipamentos::IMPRESSORAS_MULTIFUNCIONAIS, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function possuiCopiadora()
    {
        return in_array(Equipamentos::COPIADORA, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function possuiScanner()
    {
        return in_array(Equipamentos::SCANNER, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function possuiAntenaParabolica()
    {
        return in_array(Equipamentos::ANTENA_PARABOLICA, $this->equipamentos);
    }

    /**
     * @return bool
     */
    public function equipamentosAcessoInternetComputadorMesa()
    {
        return in_array(EquipamentosAcessoInternet::COMPUTADOR_MESA, $this->equipamentosAcessoInternet);
    }

    /**
     * @return bool
     */
    public function equipamentosAcessoInternetDispositivosPessoais()
    {
        return in_array(EquipamentosAcessoInternet::DISPOSITIVOS_PESSOAIS, $this->equipamentosAcessoInternet);
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
    public function redeLocalNenhuma()
    {
        return in_array(RedeLocal::NENHUMA, $this->redeLocal);
    }

    /**
     * @return bool
     */
    public function redeLocalACabo()
    {
        return in_array(RedeLocal::A_CABO, $this->redeLocal);
    }

    /**
     * @return bool
     */
    public function redeLocalWireless()
    {
        return in_array(RedeLocal::WIRELESS, $this->redeLocal);
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
    public function orgaosColegiadosOutros()
    {
        return in_array(OrgaosColegiados::OUTROS, $this->orgaosColegiados);
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosAssociacaoPais()
    {
        return in_array(OrgaosColegiados::ASSOCIACAO_PAIS, $this->orgaosColegiados);
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosAssociacaoPaisEMestres()
    {
        return in_array(OrgaosColegiados::ASSOCIACAO_PAIS_E_MESTRES, $this->orgaosColegiados);
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosConselhoEscolar()
    {
        return in_array(OrgaosColegiados::CONSELHO_ESCOLAR, $this->orgaosColegiados);
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosGremioEstudantil()
    {
        return in_array(OrgaosColegiados::GREMIO_ESTUDANTIL, $this->orgaosColegiados);
    }

    /**
     * @return bool
     */
    public function orgaosColegiadosNenhum()
    {
        return in_array(OrgaosColegiados::NENHUM, $this->orgaosColegiados);
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

    /**
     * @return bool
     */
    public function usoInternetNaoPossui()
    {
        return in_array(UsoInternet::NAO_POSSUI, $this->usoInternet);
    }

    /**
     * @return bool
     */
    public function usoInternetAdministrativo()
    {
        return in_array(UsoInternet::ADMINISTRATIVO, $this->usoInternet);
    }

    /**
     * @return bool
     */
    public function usoInternetProcessosEnsino()
    {
        return in_array(UsoInternet::PROCESSOS_ENSINO, $this->usoInternet);
    }

    /**
     * @return bool
     */
    public function usoInternetAlunos()
    {
        return in_array(UsoInternet::ALUNOS, $this->usoInternet);
    }

    /**
     * @return bool
     */
    public function usoInternetComunidade()
    {
        return in_array(UsoInternet::COMUNIDADE, $this->usoInternet);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoSerieAno()
    {
        return in_array(OrganizacaoEnsino::SERIE_ANO, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoPeriodosSemestrais()
    {
        return in_array(OrganizacaoEnsino::PERIODOS_SEMESTRAIS, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoCliclosEnsinoFundamental()
    {
        return in_array(OrganizacaoEnsino::CLICLOS_ENSINO_FUNDAMENTAL, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoGruposNaoSeriados()
    {
        return in_array(OrganizacaoEnsino::GRUPOS_NAO_SERIADOS, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoModulos()
    {
        return in_array(OrganizacaoEnsino::MODULOS, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function organizacaoEnsinoAlternanciaRegular()
    {
        return in_array(OrganizacaoEnsino::ALTERNANCIA_REGULAR, $this->organizacaoEnsino);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosAcervoMultimidia()
    {
        return in_array(InstrumentosPedagogicos::ACERVO_MULTIMIDIA, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosBrinquedrosEducacaoInfantil()
    {
        return in_array(InstrumentosPedagogicos::BRINQUEDROS_EDUCACAO_INFANTIL, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisCientificos()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_CIENTIFICOS, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosAmplificacaoDifusaoSom()
    {
        return in_array(InstrumentosPedagogicos::AMPLIFICACAO_DIFUSAO_SOM, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosInstrumentosMusicais()
    {
        return in_array(InstrumentosPedagogicos::INSTRUMENTOS_MUSICAIS, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosJogosEducativos()
    {
        return in_array(InstrumentosPedagogicos::JOGOS_EDUCATIVOS, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisAtividadesCulturais()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_ATIVIDADES_CULTURAIS, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisPraticaDesportiva()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_PRATICA_DESPORTIVA, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisEducacaoIndigena()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_EDUCACAO_INDIGENA, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisRelacoesEtnicosRaciais()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_RELACOES_ETNICOS_RACIAIS, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function instrumentosPedagogicosMateriaisEducacaoCampo()
    {
        return in_array(InstrumentosPedagogicos::MATERIAIS_EDUCACAO_CAMPO, $this->instrumentosPedagogicos);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasNaoPossui()
    {
        return in_array(ReservaVagasCotas::NAO_POSSUI, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasAutodeclaracaoPpi()
    {
        return in_array(ReservaVagasCotas::AUTODECLARACAO_PPI, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasCondicaoRenda()
    {
        return in_array(ReservaVagasCotas::CONDICAO_RENDA, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasEscolaPublica()
    {
        return in_array(ReservaVagasCotas::ESCOLA_PUBLICA, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasPcd()
    {
        return in_array(ReservaVagasCotas::PCD, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function reservaVagasCotasOutros()
    {
        return in_array(ReservaVagasCotas::OUTROS, $this->reservaVagasCotas);
    }

    /**
     * @return bool
     */
    public function linguaMinistradaPortugues()
    {
        return $this->linguaMinistrada == 1;
    }

    /**
     * @return bool
     */
    public function linguaMinistradaIndigena()
    {
        return $this->linguaMinistrada == 2;
    }
}
