<?php


namespace App\Models\Educacenso;


class Registro10Fields implements RegistroEducacenso
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
     * @var int
     */
    public $qtdViceDiretor;

    /**
     * @var int
     */
    public $qtdOrientadorComunitario;

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

    public $registro;
    public $localFuncionamentoPredioEscolar;
    public $localFuncionamentoSalasOutraEscola;
    public $localFuncionamentoGalpao;
    public $localFuncionamentoUnidadeAtendimentoSocioeducativa;
    public $localFuncionamentoUnidadePrisional;
    public $localFuncionamentoOutros;
    public $tratamentoLixoSeparacao;
    public $tratamentoLixoReaproveitamento;
    public $tratamentoLixoReciclagem;
    public $tratamentoLixoNaoFaz;
    public $dependenciaBanheiro;
    public $dependenciaBanheiroFuncionarios;
    public $dependenciaDormitorioAluno;
    public $dependenciaDormitorioProfessor;
    public $dependenciaPiscina;
    public $dependenciaSalaRepouso;
    public $dependenciaSalaArtes;
    public $dependenciaSalaMusica;
    public $dependenciaSalaDanca;
    public $dependenciaSalaMultiuso;
    public $dependenciaTerreirao;
    public $dependenciaViveiro;
    public $dependenciaSalaSecretaria;
    public $recursoCorrimao;
    public $recursoElevador;
    public $recursoPisosTateis;
    public $recursoPortaVaoLivre;
    public $recursoRampas;
    public $recursoSinalizacaoSonora;
    public $recursoSinalizacaoTatil;
    public $recursoSinalizacaoVisual;
    public $recursoNenhum;
    public $equipamentosScanner;
    public $acessoInternetAdministrativo;
    public $acessoInternetProcessoEnsino;
    public $acessoInternetAlunos;
    public $acessoInternetComunidade;
    public $acessoInternetNaoPossui;
    public $computadoresMesaAcessoInternet;
    public $dispositovosPessoaisAcessoInternet;
    public $internetBandaLarga;
    public $redeLocalCabo;
    public $redeLocalWireless;
    public $redeLocalNaoExiste;
    public $organizacaoEnsinoSerieAno;
    public $organizacaoEnsinoPeriodosSemestrais;
    public $organizacaoEnsinoGrupos;
    public $organizacaoEnsinoCiclos;
    public $organizacaoEnsinoModulos;
    public $organizacaoEnsinoAlternancia;
    public $instrumentosPedagogicosAcervo;
    public $instrumentosPedagogicosBrinquedos;
    public $instrumentosPedagogicosMateriaisCientificos;
    public $instrumentosPedagogicosEquipamentosSom;
    public $instrumentosPedagogicosInstrumentos;
    public $instrumentosPedagogicosJogos;
    public $instrumentosPedagogicosAtividadesCulturais;
    public $instrumentosPedagogicosPraticaDesportiva;
    public $instrumentosPedagogicosEducacaoIndigena;
    public $instrumentosPedagogicosEducacaoEtnicoRacial;
    public $instrumentosPedagogicosEducacaoCampo;
    public $linguaIndigena;
    public $linguaPortuguesa;
    public $linguaIndigena1;
    public $linguaIndigena2;
    public $linguaIndigena3;
    public $reservaVagasCotasAutodeclaracao;
    public $reservaVagasCotasRenda;
    public $reservaVagasCotasEscolaPublica;
    public $reservaVagasCotasPCD;
    public $reservaVagasCotasOutros;
    public $reservaVagasCotasNaoFaz;
    public $orgaoColegiadoAssociacaoPais;
    public $orgaoColegiadoAssociacaoPaisMestres;
    public $orgaoColegiadoConselho;
    public $orgaoColegiadoGremio;
    public $orgaoColegiadoOutros;
    public $orgaoColegiadoNaoExiste;

}
