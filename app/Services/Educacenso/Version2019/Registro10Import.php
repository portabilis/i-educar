<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\Services\Educacenso\Version2019\Models\Registro10Model;
use App\User;
use iEducar\Modules\Educacenso\Model\AbastecimentoAgua;
use iEducar\Modules\Educacenso\Model\AreasExternas;
use iEducar\Modules\Educacenso\Model\Banheiros;
use iEducar\Modules\Educacenso\Model\DestinacaoLixo;
use iEducar\Modules\Educacenso\Model\Dormitorios;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\EquipamentosAcessoInternet;
use iEducar\Modules\Educacenso\Model\EsgotamentoSanitario;
use iEducar\Modules\Educacenso\Model\FonteEnergia;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\Laboratorios;
use iEducar\Modules\Educacenso\Model\LinguaMinistrada;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\OrganizacaoEnsino;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\SalasAtividades;
use iEducar\Modules\Educacenso\Model\SalasFuncionais;
use iEducar\Modules\Educacenso\Model\SalasGerais;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\UsoInternet;

class Registro10Import implements RegistroImportInterface
{
    /**
     * @var Registro10
     */
    protected $model;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var int
     */
    protected $year;

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int $year
     * @param $user
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;

        $schoolInep = $this->getSchool();

        if (empty($schoolInep)) {
            return;
        }

        /** @var LegacySchool $school */
        $school = $schoolInep->school;
        $model = $this->model;

        $school->local_funcionamento = $this->getArrayLocalFuncionamento();
        $school->condicao = $model->condicao ?: null;
        $school->predio_compartilhado_outra_escola = $model->predioCompartilhadoOutraEscola ?: null;
        $school->codigo_inep_escola_compartilhada = $model->codigoInepEscolaCompartilhada ?: null;
        $school->codigo_inep_escola_compartilhada2 = $model->codigoInepEscolaCompartilhada2 ?: null;
        $school->codigo_inep_escola_compartilhada3 = $model->codigoInepEscolaCompartilhada3 ?: null;
        $school->codigo_inep_escola_compartilhada4 = $model->codigoInepEscolaCompartilhada4 ?: null;
        $school->codigo_inep_escola_compartilhada5 = $model->codigoInepEscolaCompartilhada5 ?: null;
        $school->codigo_inep_escola_compartilhada6 = $model->codigoInepEscolaCompartilhada6 ?: null;
        $school->agua_potavel_consumo = $model->aguaPotavelConsumo;
        $school->abastecimento_agua = $this->getArrayAbastecimentoAgua();
        $school->abastecimento_energia = $this->getArrayAbastecimentoEnergia();
        $school->esgoto_sanitario = $this->getArrayEsgotamentoSanitario();
        $school->destinacao_lixo = $this->getArrayDestinacaoLixo();
        $school->tratamento_lixo = $this->getArrayTratamentoLixo();
        $school->salas_funcionais = $this->getArraySalasFuncionais();
        $school->salas_gerais = $this->getArraySalasGerais();
        $school->banheiros = $this->getArrayBanheiros();
        $school->dormitorios = $this->getArrayDormitorios();
        $school->laboratorios = $this->getArrayLaboratorios();
        $school->areas_externas = $this->getArrayAreasExternas();
        $school->salas_atividades = $this->getArraySalasAtividades();
        $school->recursos_acessibilidade = $this->getArrayRecursosAcessibilidade();
        $school->possui_dependencias = $this->getPossuiDependencias($school);
        $school->numero_salas_utilizadas_dentro_predio = $model->numeroSalasUtilizadasDentroPredio ?: null;
        $school->numero_salas_utilizadas_fora_predio = $model->numeroSalasUtilizadasForaPredio ?: null;
        $school->numero_salas_climatizadas = $model->numeroSalasClimatizadas ?: null;
        $school->numero_salas_acessibilidade = $model->numeroSalasAcessibilidade ?: null;
        $school->equipamentos = $this->getArrayEquipamentos();
        $school->dvds = $model->dvds ?: null;
        $school->aparelhos_de_som = $model->aparelhosDeSom ?: null;
        $school->televisoes = $model->televisoes ?: null;
        $school->lousas_digitais = $model->lousasDigitais ?: null;
        $school->projetores_digitais = $model->projetoresDigitais ?: null;
        $school->quantidade_computadores_alunos_mesa = $model->quantidadeComputadoresAlunosMesa ?: null;
        $school->quantidade_computadores_alunos_portateis = $model->quantidadeComputadoresAlunosPortateis ?: null;
        $school->quantidade_computadores_alunos_tablets = $model->quantidadeComputadoresAlunosTablets ?: null;
        $school->uso_internet = $this->getArrayUsoInternet();
        $school->equipamentos_acesso_internet = $this->getArrayEquipamentosAcessoInternet();
        $school->acesso_internet = $model->acessoInternet;
        $school->rede_local = $this->getArrayRedeLocal();
        $school->qtd_auxiliar_administrativo = $model->qtdAuxiliarAdministrativo ?: null;
        $school->qtd_auxiliar_servicos_gerais = $model->qtdAuxiliarServicosGerais ?: null;
        $school->qtd_bibliotecarios = $model->qtdBibliotecarios ?: null;
        $school->qtd_bombeiro = $model->qtdBombeiro ?: null;
        $school->qtd_coordenador_turno = $model->qtdCoordenadorTurno ?: null;
        $school->qtd_fonoaudiologo = $model->qtdFonoaudiologo ?: null;
        $school->qtd_nutricionistas = $model->qtdNutricionistas ?: null;
        $school->qtd_psicologo = $model->qtdPsicologo ?: null;
        $school->qtd_profissionais_preparacao = $model->qtdProfissionaisPreparacao ?: null;
        $school->qtd_apoio_pedagogico = $model->qtdApoioPedagogico ?: null;
        $school->qtd_secretario_escolar = $model->qtdSecretarioEscolar ?: null;
        $school->qtd_segurancas = $model->qtdSegurancas ?: null;
        $school->qtd_tecnicos = $model->qtdTecnicos ?: null;
        $school->alimentacao_escolar_alunos = $model->alimentacaoEscolarAlunos ?: null;
        $school->organizacao_ensino = $this->getArrayOrganizacaoEnsino();
        $school->instrumentos_pedagogicos = $this->getArrayInstrumentosPedagogicos();
        $school->educacao_indigena = $model->educacaoIndigena ?: null;
        $school->lingua_ministrada = $model->linguaIndigena ? LinguaMinistrada::INDIGENA : LinguaMinistrada::PORTUGUESA;
        $school->codigo_lingua_indigena = $this->getArrayLinguaIndigena();
        $school->exame_selecao_ingresso = $model->exameSelecaoIngresso ?: null;
        $school->reserva_vagas_cotas = $this->getArrayReservaVagas();
        $school->predio_compartilhado_outra_escola = $model->predioCompartilhadoOutraEscola ?: null;
        $school->usa_espacos_equipamentos_atividades_regulares = $model->usaEspacosEquipamentosAtividadesRegulares ?: null;
        $school->orgaos_colegiados = $this->getArrayOrgaosColegiados();
        $school->projeto_politico_pedagogico = $model->projetoPoliticoPedagogico ?: null;

        $school->save();
    }

    /**
     * @param $arrayColumns
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro10Model();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }

    protected function getSchool()
    {
        return SchoolInep::where('cod_escola_inep', $this->model->codigoInep)->first();
    }

    private function getPostgresIntegerArray($array)
    {
        return '{' . implode(',', $array) . '}';
    }

    private function getArrayLocalFuncionamento()
    {
        $arrayLocal = [];

        if ($this->model->localFuncionamentoPredioEscolar) {
            $arrayLocal[] = LocalFuncionamento::PREDIO_ESCOLAR;
        }

        if ($this->model->localFuncionamentoSalasOutraEscola) {
            $arrayLocal[] = LocalFuncionamento::SALAS_OUTRA_ESCOLA;
        }

        if ($this->model->localFuncionamentoGalpao) {
            $arrayLocal[] = LocalFuncionamento::GALPAO;
        }

        if ($this->model->localFuncionamentoUnidadeAtendimentoSocioeducativa) {
            $arrayLocal[] = LocalFuncionamento::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA;
        }

        if ($this->model->localFuncionamentoUnidadePrisional) {
            $arrayLocal[] = LocalFuncionamento::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA;
        }

        if ($this->model->localFuncionamentoOutros) {
            $arrayLocal[] = LocalFuncionamento::OUTROS;
        }

        return $this->getPostgresIntegerArray($arrayLocal);
    }

    private function getArrayAbastecimentoAgua()
    {
        $arrayAbastecimentoAgua = [];

        if ($this->model->aguaRedePublica) {
            $arrayAbastecimentoAgua[] = AbastecimentoAgua::REDE_PUBLICA;
        }

        if ($this->model->aguaPocoArtesiano) {
            $arrayAbastecimentoAgua[] = AbastecimentoAgua::POCO_ARTESIANO;
        }

        if ($this->model->aguaCacimbaCisternaPoco) {
            $arrayAbastecimentoAgua[] = AbastecimentoAgua::CACIMBA_CISTERNA_POCO;
        }

        if ($this->model->aguaFonteRio) {
            $arrayAbastecimentoAgua[] = AbastecimentoAgua::FONTE;
        }

        if ($this->model->aguaInexistente) {
            $arrayAbastecimentoAgua[] = AbastecimentoAgua::INEXISTENTE;
        }

        return $this->getPostgresIntegerArray($arrayAbastecimentoAgua);
    }

    private function getArrayAbastecimentoEnergia()
    {
        $arrayAbastecimentoEnergia = [];

        if ($this->model->energiaRedePublica) {
            $arrayAbastecimentoEnergia[] = FonteEnergia::REDE_PUBLICA;
        }

        if ($this->model->energiaGerador) {
            $arrayAbastecimentoEnergia[] = FonteEnergia::GERADOR_COMBUSTIVEL_FOSSIL;
        }

        if ($this->model->energiaOutros) {
            $arrayAbastecimentoEnergia[] = FonteEnergia::FONTES_RENOVAVEIS;
        }

        if ($this->model->energiaInexistente) {
            $arrayAbastecimentoEnergia[] = FonteEnergia::INEXISTENTE;
        }

        return $this->getPostgresIntegerArray($arrayAbastecimentoEnergia);
    }

    private function getArrayEsgotamentoSanitario()
    {
        $arrayEsgotamentoSanitario = [];

        if ($this->model->esgotoRedePublica) {
            $arrayEsgotamentoSanitario[] = EsgotamentoSanitario::REDE_PUBLICA;
        }

        if ($this->model->esgotoFossaComum) {
            $arrayEsgotamentoSanitario[] = EsgotamentoSanitario::FOSSA_SEPTICA;
        }

        if ($this->model->esgotoFossaRudimentar) {
            $arrayEsgotamentoSanitario[] = EsgotamentoSanitario::FOSSA_RUDIMENTAR;
        }

        if ($this->model->esgotoInexistente) {
            $arrayEsgotamentoSanitario[] = EsgotamentoSanitario::INEXISTENTE;
        }

        return $this->getPostgresIntegerArray($arrayEsgotamentoSanitario);
    }

    private function getArrayDestinacaoLixo()
    {
        $arrayDestinacaoLixo = [];

        if ($this->model->lixoColetaPeriodica) {
            $arrayDestinacaoLixo[] = DestinacaoLixo::SERVICO_COLETA;
        }

        if ($this->model->lixoQueima) {
            $arrayDestinacaoLixo[] = DestinacaoLixo::QUEIMA;
        }

        if ($this->model->lixoEnterra) {
            $arrayDestinacaoLixo[] = DestinacaoLixo::ENTERRA;
        }

        if ($this->model->lixoDestinacaoPoderPublico) {
            $arrayDestinacaoLixo[] = DestinacaoLixo::DESTINACAO_LICENCIADA;
        }

        if ($this->model->lixoJogaOutraArea) {
            $arrayDestinacaoLixo[] = DestinacaoLixo::DESCARTA_OUTRA_AREA;
        }

        return $this->getPostgresIntegerArray($arrayDestinacaoLixo);
    }

    private function getArrayTratamentoLixo()
    {
        $arrayTratamentoLixo = [];

        if ($this->model->tratamentoLixoSeparacao) {
            $arrayTratamentoLixo[] = TratamentoLixo::SEPARACAO;
        }

        if ($this->model->tratamentoLixoReciclagem) {
            $arrayTratamentoLixo[] = TratamentoLixo::RECICLAGEM;
        }

        if ($this->model->tratamentoLixoNaoFaz) {
            $arrayTratamentoLixo[] = TratamentoLixo::NAO_FAZ;
        }

        return $this->getPostgresIntegerArray($arrayTratamentoLixo);
    }

    private function getArrayBanheiros()
    {
        $arrayBanheiros = [];

        if ($this->model->dependenciaBanheiro) {
            $arrayBanheiros[] = Banheiros::BANHEIRO;
        }

        if ($this->model->dependenciaBanheiroDeficiente) {
            $arrayBanheiros[] = Banheiros::BANHEIRO_ACESSIVEL;
        }

        if ($this->model->dependenciaBanheiroInfantil) {
            $arrayBanheiros[] = Banheiros::BANHEIRO_EDUCACAO_INFANTIL;
        }

        if ($this->model->dependenciaBanheiroFuncionarios) {
            $arrayBanheiros[] = Banheiros::BANHEIRO_FUNCIONARIOS;
        }

        if ($this->model->dependenciaBanheiroChuveiro) {
            $arrayBanheiros[] = Banheiros::BANHEIRO_CHUVEIRO;
        }

        return $this->getPostgresIntegerArray($arrayBanheiros);
    }

    private function getArrayDormitorios()
    {
        $arrayDormitorios = [];

        if ($this->model->dependenciaDormitorioAluno) {
            $arrayDormitorios[] = Dormitorios::ALUNO;
        }

        if ($this->model->dependenciaDormitorioProfessor) {
            $arrayDormitorios[] = Dormitorios::PROFESSOR;
        }

        return $this->getPostgresIntegerArray($arrayDormitorios);
    }

    private function getArrayLaboratorios()
    {
        $arrayLaboratorios = [];

        if ($this->model->dependenciaLaboratorioCiencias) {
            $arrayLaboratorios[] = Laboratorios::CIENCIAS;
        }

        if ($this->model->dependenciaLaboratorioInformatica) {
            $arrayLaboratorios[] = Laboratorios::INFORMATICA;
        }

        return $this->getPostgresIntegerArray($arrayLaboratorios);
    }

    private function getArrayAreasExternas()
    {
        $arrayAreas = [];

        if ($this->model->dependenciaAreaVerde) {
            $arrayAreas[] = AreasExternas::AREA_VERDE;
        }

        if ($this->model->dependenciaParqueInfantil) {
            $arrayAreas[] = AreasExternas::PARQUE_INFANTIL;
        }

        if ($this->model->dependenciaPatioCoberto) {
            $arrayAreas[] = AreasExternas::PATIO_COBERTO;
        }

        if ($this->model->dependenciaPatioDescoberto) {
            $arrayAreas[] = AreasExternas::PATIO_DESCOBERTO;
        }

        if ($this->model->dependenciaPiscina) {
            $arrayAreas[] = AreasExternas::PISCINA;
        }

        if ($this->model->dependenciaQuadraCoberta) {
            $arrayAreas[] = AreasExternas::QUADRA_COBERTA;
        }

        if ($this->model->dependenciaQuadraDescoberta) {
            $arrayAreas[] = AreasExternas::QUADRA_DESCOBERTA;
        }

        if ($this->model->dependenciaTerreirao) {
            $arrayAreas[] = AreasExternas::TERREIRAO;
        }

        if ($this->model->dependenciaViveiro) {
            $arrayAreas[] = AreasExternas::VIVEIRO;
        }

        return $this->getPostgresIntegerArray($arrayAreas);
    }

    private function getArraySalasFuncionais()
    {
        $arraySalas = [];

        if ($this->model->dependenciaAumoxarifado) {
            $arraySalas[] = SalasFuncionais::ALMOXARIFADO;
        }

        if ($this->model->dependenciaCozinha) {
            $arraySalas[] = SalasFuncionais::COZINHA;
        }

        if ($this->model->dependenciaRefeitorio) {
            $arraySalas[] = SalasFuncionais::REFEITORIO;
        }

        if ($this->model->dependenciaDispensa) {
            $arraySalas[] = SalasFuncionais::DESPENSA;
        }

        return $this->getPostgresIntegerArray($arraySalas);
    }

    private function getArraySalasGerais()
    {
        $arraySalas = [];

        if ($this->model->dependenciaAuditorio) {
            $arraySalas[] = SalasGerais::AUDITORIO;
        }

        if ($this->model->dependenciaBiblioteca) {
            $arraySalas[] = SalasGerais::BIBLIOTECA;
        }

        if ($this->model->dependenciaSalaDiretoria) {
            $arraySalas[] = SalasGerais::SALA_DIRETORIA;
        }

        if ($this->model->dependenciaSalaSecretaria) {
            $arraySalas[] = SalasGerais::SALA_SECRETARIA;
        }

        if ($this->model->dependenciaSalaProfessores) {
            $arraySalas[] = SalasGerais::SALA_PROFESSORES;
        }

        return $this->getPostgresIntegerArray($arraySalas);
    }

    private function getArraySalasAtividades()
    {
        $arraySalas = [];

        if ($this->model->dependenciaSalaLeitura) {
            $arraySalas[] = SalasAtividades::LEITURA;
        }

        if ($this->model->dependenciaSalaArtes) {
            $arraySalas[] = SalasAtividades::ATELIE;
        }

        if ($this->model->dependenciaSalaMusica) {
            $arraySalas[] = SalasAtividades::MUSICA;
        }

        if ($this->model->dependenciaSalaDanca) {
            $arraySalas[] = SalasAtividades::ESTUDIO_DANCA;
        }

        if ($this->model->dependenciaSalaMultiuso) {
            $arraySalas[] = SalasAtividades::MULTIUSO;
        }

        if ($this->model->dependenciaSalaAee) {
            $arraySalas[] = SalasAtividades::RECURSOS_AEE;
        }

        if ($this->model->dependenciaSalaRepouso) {
            $arraySalas[] = SalasAtividades::REPOUSO_ALUNO;
        }

        return $this->getPostgresIntegerArray($arraySalas);
    }

    private function getArrayRecursosAcessibilidade()
    {
        $arrayRecursosAcessibilidade = [];

        if ($this->model->acessoInternetNaoPossui) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::NENHUM;
        }

        if ($this->model->recursoCorrimao) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::CORRIMAO;
        }

        if ($this->model->recursoElevador) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::ELEVADOR;
        }

        if ($this->model->recursoPisosTateis) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::PISOS_TATEIS;
        }

        if ($this->model->recursoPortaVaoLivre) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::PORTAS_VAO_LIVRE;
        }

        if ($this->model->recursoRampas) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::RAMPAS;
        }

        if ($this->model->recursoSinalizacaoSonora) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::SINALIZACAO_SONORA;
        }

        if ($this->model->recursoSinalizacaoTatil) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::SINALIZACAO_TATIL;
        }

        if ($this->model->recursoSinalizacaoVisual) {
            $arrayRecursosAcessibilidade[] = RecursosAcessibilidade::SINALIZACAO_VISUAL;
        }

        return $this->getPostgresIntegerArray($arrayRecursosAcessibilidade);
    }

    private function getPossuiDependencias($school)
    {
        return !empty(array_filter(
            [
                str_replace(['{','}'], '', $school->salas_gerais),
                str_replace(['{','}'], '', $school->salas_funcionais),
                str_replace(['{','}'], '', $school->banheiros),
                str_replace(['{','}'], '', $school->laboratorios),
                str_replace(['{','}'], '', $school->salas_atividades),
                str_replace(['{','}'], '', $school->dormitorios),
                str_replace(['{','}'], '', $school->areas_externas),
            ]
        ));
    }

    private function getArrayEquipamentos()
    {
        $arrayEquipamentos = [];

        if ($this->model->antenasParabolicas) {
            $arrayEquipamentos[] = Equipamentos::ANTENA_PARABOLICA;
        }

        if ($this->model->computadores) {
            $arrayEquipamentos[] = Equipamentos::COMPUTADORES;
        }

        if ($this->model->impressoras) {
            $arrayEquipamentos[] = Equipamentos::IMPRESSORAS;
        }

        if ($this->model->impressorasMultifuncionais) {
            $arrayEquipamentos[] = Equipamentos::IMPRESSORAS_MULTIFUNCIONAIS;
        }

        if ($this->model->copiadoras) {
            $arrayEquipamentos[] = Equipamentos::COPIADORA;
        }

        if ($this->model->equipamentosScanner) {
            $arrayEquipamentos[] = Equipamentos::SCANNER;
        }

        return $this->getPostgresIntegerArray($arrayEquipamentos);
    }

    private function getArrayUsoInternet()
    {
        $arrayUsoInternet = [];

        if ($this->model->acessoInternetNaoPossui) {
            $arrayUsoInternet[] = UsoInternet::NAO_POSSUI;
        }

        if ($this->model->acessoInternetAdministrativo) {
            $arrayUsoInternet[] = UsoInternet::ADMINISTRATIVO;
        }

        if ($this->model->acessoInternetProcessoEnsino) {
            $arrayUsoInternet[] = UsoInternet::PROCESSOS_ENSINO;
        }

        if ($this->model->acessoInternetAlunos) {
            $arrayUsoInternet[] = UsoInternet::ALUNOS;
        }

         if ($this->model->acessoInternetComunidade) {
             $arrayUsoInternet[] = UsoInternet::COMUNIDADE;
         }

        return $this->getPostgresIntegerArray($arrayUsoInternet);
    }

    private function getArrayEquipamentosAcessoInternet()
    {
        $equipamentosAcessoInternet = [];

        if ($this->model->computadoresMesaAcessoInternet) {
            $equipamentosAcessoInternet[] = EquipamentosAcessoInternet::COMPUTADOR_MESA;
        }

        if ($this->model->dispositovosPessoaisAcessoInternet) {
            $equipamentosAcessoInternet[] = EquipamentosAcessoInternet::DISPOSITIVOS_PESSOAIS;
        }

        return $this->getPostgresIntegerArray($equipamentosAcessoInternet);
    }

    private function getArrayRedeLocal()
    {
        $arrayRedeLocal = [];

        if ($this->model->redeLocalNaoExiste) {
            $arrayRedeLocal[] = RedeLocal::NENHUMA;
        }

        if ($this->model->redeLocalCabo) {
            $arrayRedeLocal[] = RedeLocal::A_CABO;
        }

        if ($this->model->redeLocalWireless) {
            $arrayRedeLocal[] = RedeLocal::WIRELESS;
        }

        return $this->getPostgresIntegerArray($arrayRedeLocal);
    }

    private function getArrayOrganizacaoEnsino()
    {
        $arrayOrganizacaoEnsino = [];

        if ($this->model->organizacaoEnsinoSerieAno) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::SERIE_ANO;
        }

        if ($this->model->organizacaoEnsinoPeriodosSemestrais) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::PERIODOS_SEMESTRAIS;
        }

        if ($this->model->organizacaoEnsinoCiclos) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::CLICLOS_ENSINO_FUNDAMENTAL;
        }

        if ($this->model->organizacaoEnsinoGrupos) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::GRUPOS_NAO_SERIADOS;
        }

        if ($this->model->organizacaoEnsinoModulos) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::MODULOS;
        }

        if ($this->model->organizacaoEnsinoAlternancia) {
            $arrayOrganizacaoEnsino[] = OrganizacaoEnsino::ALTERNANCIA_REGULAR;
        }

        return $this->getPostgresIntegerArray($arrayOrganizacaoEnsino);
    }

    private function getArrayInstrumentosPedagogicos()
    {
        $arrayInstrumentosPedagogicos = [];

        if ($this->model->instrumentosPedagogicosAcervo) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::ACERVO_MULTIMIDIA;
        }

        if ($this->model->instrumentosPedagogicosBrinquedos) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::BRINQUEDROS_EDUCACAO_INFANTIL;
        }

        if ($this->model->instrumentosPedagogicosMateriaisCientificos) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_CIENTIFICOS;
        }

        if ($this->model->instrumentosPedagogicosEquipamentosSom) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::AMPLIFICACAO_DIFUSAO_SOM;
        }

        if ($this->model->instrumentosPedagogicosInstrumentos) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::INSTRUMENTOS_MUSICAIS;
        }

        if ($this->model->instrumentosPedagogicosJogos) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::JOGOS_EDUCATIVOS;
        }

        if ($this->model->instrumentosPedagogicosAtividadesCulturais) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_ATIVIDADES_CULTURAIS;
        }

        if ($this->model->instrumentosPedagogicosPraticaDesportiva) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_PRATICA_DESPORTIVA;
        }

        if ($this->model->instrumentosPedagogicosEducacaoIndigena) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_EDUCACAO_INDIGENA;
        }

        if ($this->model->instrumentosPedagogicosEducacaoEtnicoRacial) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_RELACOES_ETNICOS_RACIAIS;
        }

        if ($this->model->instrumentosPedagogicosEducacaoCampo) {
            $arrayInstrumentosPedagogicos[] = InstrumentosPedagogicos::MATERIAIS_EDUCACAO_CAMPO;
        }

        return $this->getPostgresIntegerArray($arrayInstrumentosPedagogicos);
    }

    private function getArrayLinguaIndigena()
    {
        $arrayLinguaIndigena = [];

        $arrayLinguaIndigena[] = $this->model->linguaIndigena1;
        $arrayLinguaIndigena[] = $this->model->linguaIndigena2;
        $arrayLinguaIndigena[] = $this->model->linguaIndigena3;

        return $this->getPostgresIntegerArray(array_filter($arrayLinguaIndigena));
    }

    private function getArrayReservaVagas()
    {
        $arrayReservaVagas = [];

        if ($this->model->reservaVagasCotasNaoFaz) {
            $arrayReservaVagas[] = ReservaVagasCotas::NAO_POSSUI;
        }

        if ($this->model->reservaVagasCotasAutodeclaracao) {
            $arrayReservaVagas[] = ReservaVagasCotas::AUTODECLARACAO_PPI;
        }

        if ($this->model->reservaVagasCotasRenda) {
            $arrayReservaVagas[] = ReservaVagasCotas::CONDICAO_RENDA;
        }

        if ($this->model->reservaVagasCotasEscolaPublica) {
            $arrayReservaVagas[] = ReservaVagasCotas::ESCOLA_PUBLICA;
        }

        if ($this->model->reservaVagasCotasPCD) {
            $arrayReservaVagas[] = ReservaVagasCotas::PCD;
        }

        if ($this->model->reservaVagasCotasOutros) {
            $arrayReservaVagas[] = ReservaVagasCotas::OUTROS;
        }

        return $this->getPostgresIntegerArray($arrayReservaVagas);
    }

    private function getArrayOrgaosColegiados()
    {
        $arrayOrgaosColegiados = [];

        if ($this->model->orgaoColegiadoOutros) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::OUTROS;
        }

        if ($this->model->orgaoColegiadoAssociacaoPais) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::ASSOCIACAO_PAIS;
        }

        if ($this->model->orgaoColegiadoAssociacaoPaisMestres) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::ASSOCIACAO_PAIS_E_MESTRES;
        }

        if ($this->model->orgaoColegiadoConselho) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::CONSELHO_ESCOLAR;
        }

        if ($this->model->orgaoColegiadoGremio) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::GREMIO_ESTUDANTIL;
        }

        if ($this->model->orgaoColegiadoNaoExiste) {
            $arrayOrgaosColegiados[] = OrgaosColegiados::NENHUM;
        }

        return $this->getPostgresIntegerArray($arrayOrgaosColegiados);
    }
}
