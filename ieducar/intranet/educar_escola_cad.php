<?php

use App\Models\EmployeeInep;
use App\Models\LegacyPerson;
use App\Models\SchoolManager;
use App\Rules\SchoolManagerAtLeastOneChief;
use App\Rules\SchoolManagerUniqueIndividuals;
use App\Services\SchoolManagerService;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\AreasExternas;
use iEducar\Modules\Educacenso\Model\Banheiros;
use iEducar\Modules\Educacenso\Model\Dormitorios;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\EquipamentosAcessoInternet;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\Laboratorios;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\OrganizacaoEnsino;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use iEducar\Modules\Educacenso\Model\OrgaoVinculadoEscola;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaEscola;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Validator\SchoolManagers;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\SalasAtividades;
use iEducar\Modules\Educacenso\Model\SalasFuncionais;
use iEducar\Modules\Educacenso\Model\SalasGerais;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Validator\Telefone;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;
use iEducar\Support\View\SelectOptions;
use iEducar\Modules\Educacenso\Validator\School\HasDifferentStepsOfChildEducationValidator;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'App/Model/ZonaLocalizacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Escola");
        $this->processoAp = "561";
    }
}

class indice extends clsCadastro
{
    use LegacyAddressingFields;

    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_escola;
    public $ref_usuario_cad;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $ref_cod_escola_rede_ensino;
    public $ref_idpes;
    public $cnpj;
    public $sigla;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_escola;
    public $passou;
    public $escola_curso;
    public $escola_curso_autorizacao;
    public $escola_curso_anos_letivos;
    public $ref_cod_curso;
    public $autorizacao;
    public $fantasia;
    public $p_ddd_telefone_1;
    public $p_telefone_1;
    public $p_ddd_telefone_2;
    public $p_telefone_2;
    public $p_ddd_telefone_mov;
    public $p_telefone_mov;
    public $p_ddd_telefone_fax;
    public $p_telefone_fax;
    public $p_email;
    public $p_http;
    public $tipo_pessoa;
    public $situacao_funcionamento;
    public $dependencia_administrativa;
    public $orgao_vinculado_escola;
    public $latitude;
    public $longitude;
    public $regulamentacao;
    public $gestor_id;
    public $cargo_gestor;
    public $email_gestor;
    public $local_funcionamento;
    public $condicao;
    public $predio_compartilhado_outra_escola;
    public $codigo_inep_escola_compartilhada;
    public $codigo_inep_escola_compartilhada2;
    public $codigo_inep_escola_compartilhada3;
    public $codigo_inep_escola_compartilhada4;
    public $codigo_inep_escola_compartilhada5;
    public $codigo_inep_escola_compartilhada6;
    public $agua_potavel_consumo;
    public $abastecimento_agua;
    public $abastecimento_energia;
    public $esgoto_sanitario;
    public $destinacao_lixo;
    public $tratamento_lixo;
    public $alimentacao_escolar_alunos;
    public $compartilha_espacos_atividades_integracao;
    public $usa_espacos_equipamentos_atividades_regulares;
    public $salas_funcionais;
    public $salas_gerais;
    public $banheiros;
    public $laboratorios;
    public $salas_atividades;
    public $dormitorios;
    public $areas_externas;
    public $recursos_acessibilidade;
    public $possui_dependencias;
    public $numero_salas_utilizadas_dentro_predio;
    public $numero_salas_utilizadas_fora_predio;
    public $numero_salas_climatizadas;
    public $numero_salas_acessibilidade;
    public $total_funcionario;
    public $atendimento_aee;
    public $fundamental_ciclo;
    public $organizacao_ensino;
    public $instrumentos_pedagogicos;
    public $orgaos_colegiados;
    public $exame_selecao_ingresso;
    public $reserva_vagas_cotas;
    public $projeto_politico_pedagogico;
    public $localizacao_diferenciada;
    public $educacao_indigena;
    public $lingua_ministrada;
    public $codigo_lingua_indigena;
    public $equipamentos;
    public $uso_internet;
    public $rede_local;
    public $equipamentos_acesso_internet;
    public $quantidade_computadores_alunos_mesa;
    public $quantidade_computadores_alunos_portateis;
    public $quantidade_computadores_alunos_tablets;
    public $lousas_digitais;
    public $televisoes;
    public $dvds;
    public $aparelhos_de_som;
    public $projetores_digitais;
    public $acesso_internet;
    public $ato_criacao;
    public $ato_autorizativo;
    public $secretario_id;
    public $utiliza_regra_diferenciada;
    public $categoria_escola_privada;
    public $conveniada_com_poder_publico;
    public $mantenedora_escola_privada;
    public $cnpj_mantenedora_principal;
    public $incluir_curso;
    public $excluir_curso;
    public $sem_cnpj;
    public $com_cnpj;
    public $esfera_administrativa;
    public $managers_inep_id;
    public $managers_role_id;
    public $servidor_id;
    public $managers_access_criteria_id;
    public $managers_link_type_id;
    public $managers_chief;
    public $managers_email;
    public $qtd_secretario_escolar;
    public $qtd_auxiliar_administrativo;
    public $qtd_apoio_pedagogico;
    public $qtd_coordenador_turno;
    public $qtd_tecnicos;
    public $qtd_bibliotecarios;
    public $qtd_segurancas;
    public $qtd_auxiliar_servicos_gerais;
    public $qtd_nutricionistas;
    public $qtd_profissionais_preparacao;
    public $qtd_bombeiro;
    public $qtd_psicologo;
    public $qtd_fonoaudiologo;
    public $qtd_vice_diretor;
    public $qtd_orientador_comunitario;
    public $iddis;

    private $inputsRecursos = [
        'qtd_secretario_escolar' => 'Secretário(a) escolar',
        'qtd_auxiliar_administrativo' => 'Auxiliares de secretaria ou auxiliares administrativos, atendentes',
        'qtd_apoio_pedagogico' => 'Profissionais de apoio e supervisão pedagógica: pedagogo(a), coordenador(a) pedagógico(a), orientador(a) educacional, supervisor(a) escolar e coordenador(a) de área de ensino',
        'qtd_coordenador_turno' => 'Coordenador(a) de turno/disciplina',
        'qtd_tecnicos' => 'Técnicos(as), monitores(as), supervisores(as) ou auxiliares de laboratório(s), de apoio a tecnologias educacionais ou em multimeios/multimídias eletrônico-digitais',
        'qtd_bibliotecarios' => 'Bibliotecário(a), auxiliar de biblioteca ou monitor(a) da sala de leitura',
        'qtd_segurancas' => 'Seguranças, guarda ou segurança patrimonial',
        'qtd_auxiliar_servicos_gerais' => 'Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), horticultor(a), jardineiro(a)',
        'qtd_nutricionistas' => 'Nutricionista',
        'qtd_profissionais_preparacao' => 'Profissionais de preparação e segurança alimentar, cozinheiro(a), merendeira e auxiliar de cozinha',
        'qtd_bombeiro' => 'Bombeiro(a) brigadista, profissionais de assistência a saúde (urgência e emergência), Enfermeiro(a), Técnico(a) de enfermagem e socorrista',
        'qtd_psicologo' => 'Psicólogo(a) Escolar',
        'qtd_fonoaudiologo' => 'Fonoaudiólogo(a)',
        'qtd_vice_diretor' => 'Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa e/ou financeira',
        'qtd_orientador_comunitario' => 'Orientador(a) comunitário(a) ou assistente social'
    ];

    public function Inicializar()
    {
        $retorno = "Novo";


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7, "educar_escola_lst.php");

        $this->cod_escola = $this->getQueryString('cod_escola');

        $this->sem_cnpj = false;

        // cadastro Novo sem CNPJ
        if (is_numeric($_POST["sem_cnpj"]) && !$this->cod_escola) {
            $this->sem_cnpj = true;
            $retorno = "Novo";
        } // cadastro Novo com CNPJ
        elseif ($_POST["cnpj"]) {
            $this->com_cnpj = true;
            $obj_juridica = new clsPessoaJuridica();
            $lst_juridica = $obj_juridica->lista(idFederal2int($_POST["cnpj"]));
            // caso exista o CNPJ na BD
            if (is_array($lst_juridica)) {
                $retorno = "Editar";
                $det_juridica = array_shift($lst_juridica);
                $this->ref_idpes = $det_juridica["idpes"];
                $obj = new clsPmieducarEscola();
                $lst_escola = $obj->lista(null, null, null, null, null, null, $this->ref_idpes, null, null, null, 1);
                if (is_array($lst_escola)) {
                    $registro = array_shift($lst_escola);
                    $this->cod_escola = $registro["cod_escola"];
                }
            } // caso nao exista o CNPJ
            else {
                $retorno = "Editar";
            }
        } // cadastro Editar
        if (is_numeric($this->cod_escola) && !$_POST["passou"]) {
            $obj = new clsPmieducarEscola($this->cod_escola);
            $registro = $obj->detalhe();

            if ($registro["ref_idpes"]) {
                $this->com_cnpj = true;
            } else {
                $this->sem_cnpj = true;
            }

            if ($registro) {

                foreach ($registro as $campo => $val) {
                    // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->loadAddress($this->ref_idpes);

                $this->gestor_id = $registro['ref_idpes_gestor'];
                $this->secretario_id = $registro['ref_idpes_secretario_escolar'];

                $this->fantasia = $registro['nome'];
                $objJuridica = new clsPessoaJuridica($this->ref_idpes);
                $det = $objJuridica->detalhe();
                $this->cnpj = int2CNPJ($det["cnpj"]);
                $this->fexcluir = $obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 3);
                $retorno = "Editar";

                if ($registro["tipo_cadastro"] == 1) {
                    $objJuridica = new clsPessoaJuridica(false, idFederal2int($this->cnpj));
                    $det = $objJuridica->detalhe();
                    $objPessoa = new clsPessoaFj($det["idpes"]);
                    list(
                        $this->p_ddd_telefone_1,
                        $this->p_telefone_1,
                        $this->p_ddd_telefone_2,
                        $this->p_telefone_2,
                        $this->p_ddd_telefone_mov,
                        $this->p_telefone_mov,
                        $this->p_ddd_telefone_fax,
                        $this->p_telefone_fax,
                        $this->p_email,
                        $this->p_http,
                        $this->tipo_pessoa
                    ) = $objPessoa->queryRapida($det["idpes"],
                        "ddd_1",
                        "fone_1",
                        "ddd_2",
                        "fone_2",
                        "ddd_mov",
                        "fone_mov",
                        "ddd_fax",
                        "fone_fax",
                        "email",
                        "url",
                        "tipo"
                    );
                }
            }
        } elseif ($_POST['cnpj'] && !$_POST["passou"]) {
            $objJuridica = new clsPessoaJuridica(false, idFederal2int($_POST['cnpj']));
            $det = $objJuridica->detalhe();
            $objPessoa = new clsPessoaFj($det["idpes"]);
            list(
                $this->p_ddd_telefone_1,
                $this->p_telefone_1,
                $this->p_ddd_telefone_2,
                $this->p_telefone_2,
                $this->p_ddd_telefone_mov,
                $this->p_telefone_mov,
                $this->p_ddd_telefone_fax,
                $this->p_telefone_fax,
                $this->p_email,
                $this->p_http,
                $this->tipo_pessoa
            ) = $objPessoa->queryRapida($det["idpes"],
                "ddd_1",
                "fone_1",
                "ddd_2",
                "fone_2",
                "ddd_mov",
                "fone_mov",
                "ddd_fax",
                "fone_fax",
                "email",
                "url",
                "tipo"
            );
        }

        if ($this->cnpj_mantenedora_principal) {
            $this->cnpj_mantenedora_principal = int2CNPJ($this->cnpj_mantenedora_principal);
        }

        if (is_string($this->local_funcionamento)) {
            $this->local_funcionamento = explode(',', str_replace(array('{', "}"), '', $this->local_funcionamento));
        }

        if (is_string($this->abastecimento_agua)) {
            $this->abastecimento_agua = explode(',', str_replace(array('{', "}"), '', $this->abastecimento_agua));
        }

        if (is_string($this->abastecimento_energia)) {
            $this->abastecimento_energia = explode(',', str_replace(array('{', "}"), '', $this->abastecimento_energia));
        }

        if (is_string($this->esgoto_sanitario)) {
            $this->esgoto_sanitario = explode(',', str_replace(array('{', "}"), '', $this->esgoto_sanitario));
        }

        if (is_string($this->destinacao_lixo)) {
            $this->destinacao_lixo = explode(',', str_replace(array('{', "}"), '', $this->destinacao_lixo));
        }

        if (is_string($this->tratamento_lixo)) {
            $this->tratamento_lixo = explode(',', str_replace(array('{', "}"), '', $this->tratamento_lixo));
        }

        if (is_string($this->salas_funcionais)) {
            $this->salas_funcionais = explode(',', str_replace(array('{', "}"), '', $this->salas_funcionais));
        }

        if (is_string($this->salas_gerais)) {
            $this->salas_gerais = explode(',', str_replace(array('{', "}"), '', $this->salas_gerais));
        }

        if (is_string($this->banheiros)) {
            $this->banheiros = explode(',', str_replace(array('{', "}"), '', $this->banheiros));
        }

        if (is_string($this->laboratorios)) {
            $this->laboratorios = explode(',', str_replace(array('{', "}"), '', $this->laboratorios));
        }

        if (is_string($this->salas_atividades)) {
            $this->salas_atividades = explode(',', str_replace(array('{', "}"), '', $this->salas_atividades));
        }

        if (is_string($this->dormitorios)) {
            $this->dormitorios = explode(',', str_replace(array('{', "}"), '', $this->dormitorios));
        }

        if (is_string($this->areas_externas)) {
            $this->areas_externas = explode(',', str_replace(array('{', "}"), '', $this->areas_externas));
        }

        if (is_string($this->recursos_acessibilidade)) {
            $this->recursos_acessibilidade = explode(',', str_replace(array('{', "}"), '', $this->recursos_acessibilidade));
        }

        if (is_string($this->mantenedora_escola_privada)) {
            $this->mantenedora_escola_privada = explode(',', str_replace(array('{', "}"), '', $this->mantenedora_escola_privada));
        }

        if (is_string($this->orgao_vinculado_escola)) {
            $this->orgao_vinculado_escola = explode(',', str_replace(array('{', "}"), '', $this->orgao_vinculado_escola));
        }

        if (is_string($this->equipamentos)) {
            $this->equipamentos = explode(',', str_replace(array('{', "}"), '', $this->equipamentos));
        }

        if (is_string($this->uso_internet)) {
            $this->uso_internet = explode(',', str_replace(array('{', "}"), '', $this->uso_internet));
        }

        if (is_string($this->rede_local)) {
            $this->rede_local = explode(',', str_replace(array('{', "}"), '', $this->rede_local));
        }

        if (is_string($this->equipamentos_acesso_internet)) {
            $this->equipamentos_acesso_internet = explode(',', str_replace(array('{', "}"), '', $this->equipamentos_acesso_internet));
        }

        if (is_string($this->organizacao_ensino)) {
            $this->organizacao_ensino = explode(',', str_replace(array('{', "}"), '', $this->organizacao_ensino));
        }

        if (is_string($this->instrumentos_pedagogicos)) {
            $this->instrumentos_pedagogicos = explode(',', str_replace(array('{', "}"), '', $this->instrumentos_pedagogicos));
        }

        if (is_string($this->orgaos_colegiados)) {
            $this->orgaos_colegiados = explode(',', str_replace(array('{', "}"), '', $this->orgaos_colegiados));
        }

        if (is_string($this->reserva_vagas_cotas)) {
            $this->reserva_vagas_cotas = explode(',', str_replace(array('{', "}"), '', $this->reserva_vagas_cotas));
        }

        if (is_string($this->codigo_lingua_indigena)) {
            $this->codigo_lingua_indigena = explode(',', str_replace(array('{', "}"), '', $this->codigo_lingua_indigena));
        }

        $this->url_cancelar = ($retorno == "Editar") ? "educar_escola_det.php?cod_escola={$registro["cod_escola"]}" : "educar_escola_lst.php";

        $this->breadcrumb('Escola', ['educar_index.php' => 'Escola']);
        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    public function Gerar()
    {
        // assets
        $scripts = array(
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/Escola.js',
            '/modules/Cadastro/Assets/Javascripts/Addresses.js',
            '/modules/Cadastro/Assets/Javascripts/SchoolManagersModal.js',
        );
        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
        $styles = array('/modules/Cadastro/Assets/Stylesheets/Escola.css');
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

        $obj_permissoes = new clsPermissoes();

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();

        $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

        if (!$this->sem_cnpj && !$this->com_cnpj) {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(1);
            $parametros->setPessoa('J');
            $parametros->setPessoaCampo('sem_cnpj');
            $parametros->setPessoaNovo("S");
            $parametros->setPessoaCPF("N");
            $parametros->setPessoaTela('frame');
            $this->campoOculto("sem_cnpj", "");
            $parametros->setCodSistema(13);
            $parametros->adicionaCampoTexto("cnpj", "cnpj");
            $this->campoCnpjPesq("cnpj", "CNPJ", $this->cnpj, "pesquisa_pessoa_lst.php", $parametros->serializaCampos(), true);
            $this->acao_enviar = false;
            $this->url_cancelar = false;
            $this->array_botao = array("Continuar", "Cancelar");
            $this->array_botao_url_script = array("obj = document.getElementById('cnpj');if(obj.value != '' ) { acao(); } else { acao(); }", "go('educar_escola_lst.php');");
        } else {
            $this->inputsHelper()->integer('escola_inep_id', array('label' => 'Código INEP', 'placeholder' => 'INEP', 'required' => $obrigarCamposCenso, 'max_length' => 8, 'label_hint' => 'Somente números'));

            if ($_POST) {
                foreach ($_POST as $campo => $val) {
                    if ($campo != 'tipoacao' && $campo != 'sem_cnpj') {
                        $this->$campo = ($this->$campo) ? $this->$campo : $val;
                    }
                }
            }

            if ($this->sem_cnpj) {
                $this->campoOculto("sem_cnpj", $this->sem_cnpj);
                $this->p_ddd_telefone_1 = ($this->p_ddd_telefone_1 == null) ? "" : $this->p_ddd_telefone_1;
                $this->p_ddd_telefone_fax = ($this->p_ddd_telefone_fax == null) ? "" : $this->p_ddd_telefone_fax;

                if ($this->ref_idpes) {
                    $objTemp = new clsPessoaJuridica($this->ref_idpes);
                    $detalhe = $objTemp->detalhe();
                }
                $this->campoOculto("cod_escola", $this->cod_escola);
                $this->campoTexto("fantasia", "Escola", $this->fantasia, 30, 255, true);
                $this->campoTexto("sigla", "Sigla", $this->sigla, 30, 255, true);
                $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

                if ($nivel == 1) {
                    $cabecalhos[] = "Instituicao";
                    $objInstituicao = new clsPmieducarInstituicao();
                    $opcoes = array("" => "Selecione");
                    $objInstituicao->setOrderby("nm_instituicao ASC");
                    $lista = $objInstituicao->lista();

                    if (is_array($lista)) {
                        foreach ($lista as $linha) {
                            $opcoes[$linha["cod_instituicao"]] = $linha["nm_instituicao"];
                        }
                    }

                    $this->campoLista("ref_cod_instituicao", "Instituição", $opcoes, $this->ref_cod_instituicao);
                } else {
                    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

                    if ($this->ref_cod_instituicao) {
                        $this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
                    } else {
                        die("Usuário não é do nivel poli-institucional e não possui uma instituição");
                    }
                }

                $opcoes = array("" => "Selecione");

                // EDITAR
                $script = "javascript:showExpansivelIframe(520, 120, 'educar_escola_rede_ensino_cad_pop.php');";

                if ($this->ref_cod_instituicao) {
                    $objTemp = new clsPmieducarEscolaRedeEnsino();
                    $lista = $objTemp->lista(null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao);

                    if (is_array($lista) && count($lista)) {
                        foreach ($lista as $registro) {
                            $opcoes["{$registro['cod_escola_rede_ensino']}"] = "{$registro['nm_rede']}";
                        }
                    }

                    $script = "<img id='img_rede_ensino' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
                } else {
                    $script = "<img id='img_rede_ensino' style='display: none;'  src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
                }

                $this->campoLista("ref_cod_escola_rede_ensino", "Rede Ensino", $opcoes, $this->ref_cod_escola_rede_ensino, "", false, "", $script);

                $zonas = App_Model_ZonaLocalizacao::getInstance();
                $zonas = [null => 'Selecione'] + $zonas->getEnums();

                $options = array(
                    'label' => 'Zona localização',
                    'value' => $this->zona_localizacao,
                    'resources' => $zonas,
                    'required' => true,
                );

                $this->inputsHelper()->select('zona_localizacao', $options);

                $this->campoTexto("p_ddd_telefone_1", "DDD Telefone 1", $this->p_ddd_telefone_1, "2", "2", false);
                $this->campoTexto("p_telefone_1", "Telefone 1", $this->p_telefone_1, "10", "15", false);
                $this->campoTexto("p_ddd_telefone_fax", "DDD Fax", $this->p_ddd_telefone_fax, "2", "2", false);
                $this->campoTexto("p_telefone_fax", "Fax", $this->p_telefone_fax, "10", "15", false);
                $this->campoTexto("p_email", "E-mail", $this->p_email, "50", "100", false);
            }

            if ($this->com_cnpj) {
                $this->campoOculto("com_cnpj", $this->com_cnpj);

                if (!$this->cod_escola) {
                    $this->cnpj = urldecode($_POST['cnpj']);
                    $this->cnpj = idFederal2int($this->cnpj);
                    $this->cnpj = int2IdFederal($this->cnpj);
                }

                $objJuridica = new clsPessoaJuridica(false, idFederal2int($this->cnpj));
                $det = $objJuridica->detalhe();
                $this->ref_idpes = $det["idpes"];

                if (!$this->fantasia) {
                    $this->fantasia = $det["fantasia"];
                }

                if ($this->passou) {
                    $this->cnpj = (is_numeric($this->cnpj)) ? $this->cnpj : idFederal2int($this->cnpj);
                    $this->cnpj = int2IdFederal($this->cnpj);
                }

                $this->campoRotulo("cnpj_", "CNPJ", $this->cnpj);
                $this->campoOculto("cnpj", idFederal2int($this->cnpj));
                $this->campoOculto("ref_idpes", $this->ref_idpes);
                $this->campoOculto("cod_escola", $this->cod_escola);
                $this->campoTexto("fantasia", "Escola", $this->fantasia, 30, 255, true);
                $this->campoTexto("sigla", "Sigla", $this->sigla, 30, 20, true);
                $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

                if ($nivel == 1) {
                    $cabecalhos[] = "Instituicao";
                    $objInstituicao = new clsPmieducarInstituicao();
                    $opcoes = array("" => "Selecione");
                    $objInstituicao->setOrderby("nm_instituicao ASC");
                    $lista = $objInstituicao->lista();

                    if (is_array($lista)) {
                        foreach ($lista as $linha) {
                            $opcoes[$linha["cod_instituicao"]] = $linha["nm_instituicao"];
                        }
                    }

                    $this->campoLista("ref_cod_instituicao", "Instituicao", $opcoes, $this->ref_cod_instituicao);
                } else {
                    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

                    if ($this->ref_cod_instituicao) {
                        $this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
                    } else {
                        die("Usuário não é do nivel poli-institucional e não possui uma instituição");
                    }
                }

                $opcoes = array("" => "Selecione");

                // EDITAR
                $script = "javascript:showExpansivelIframe(520, 120, 'educar_escola_rede_ensino_cad_pop.php');";
                if ($this->ref_cod_instituicao) {
                    $objTemp = new clsPmieducarEscolaRedeEnsino();
                    $lista = $objTemp->lista(null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao);

                    if (is_array($lista) && count($lista)) {
                        foreach ($lista as $registro) {
                            $opcoes["{$registro['cod_escola_rede_ensino']}"] = "{$registro['nm_rede']}";
                        }
                    }

                    $script = "<img id='img_rede_ensino' style='display:\'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
                } else {
                    $script = "<img id='img_rede_ensino' style='display: none;'  src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
                }

                $this->campoLista("ref_cod_escola_rede_ensino", "Rede Ensino", $opcoes, $this->ref_cod_escola_rede_ensino, "", false, "", $script);
                $opcoes = array("" => "Selecione");

                $zonas = App_Model_ZonaLocalizacao::getInstance();
                $zonas = [null => 'Selecione'] + $zonas->getEnums();

                $options = array(
                    'label' => 'Zona localização',
                    'value' => $this->zona_localizacao,
                    'resources' => $zonas,
                    'required' => true,
                );

                $this->inputsHelper()->select('zona_localizacao', $options);

                $resources = SelectOptions::localizacoesDiferenciadasEscola();
                $options = array('label' => 'Localização diferenciada da escola', 'resources' => $resources, 'value' => $this->localizacao_diferenciada, 'required' => $obrigarCamposCenso, 'size' => 70);
                $this->inputsHelper()->select('localizacao_diferenciada', $options);

                $this->viewAddress();

                $this->inputsHelper()->simpleSearchDistrito('district', [
                    'required' => $obrigarCamposCenso,
                    'label' => 'Distrito',
                ], [
                    'objectName' => 'district',
                    'hiddenInputOptions' => [
                        'options' => [
                            'value' => $this->iddis ?? $this->district_id,
                        ],
                    ],
                ]);

                $this->inputTelefone('1', 'Telefone 1');
                $this->inputTelefone('2', 'Telefone 2');
                $this->inputTelefone('mov', 'Celular');
                $this->inputTelefone('fax', 'Fax');
                $this->campoTexto("p_email", "E-mail", $this->p_email, "50", "100", false);
                $this->campoTexto("p_http", "Site/Blog/Rede social", $this->p_http, "50", "255", false);
                $this->passou = true;
                $this->campoOculto("passou", $this->passou);
            }

            $this->inputsHelper()->numeric('latitude', array('max_length' => '20', 'size' => '20', 'required' => false, 'value' => $this->latitude, 'label_hint' => 'São aceito somente números, ponto "." e hífen "-"'));
            $this->inputsHelper()->numeric('longitude', array('max_length' => '20', 'size' => '20', 'required' => false, 'value' => $this->longitude, 'label_hint' => 'São aceito somente números, ponto "." e hífen "-"'));

            $this->campoCheck("bloquear_lancamento_diario_anos_letivos_encerrados", "Bloquear lançamento no diário para anos letivos encerrados", $this->bloquear_lancamento_diario_anos_letivos_encerrados);
            $this->campoCheck("utiliza_regra_diferenciada", "Utiliza regra diferenciada", dbBool($this->utiliza_regra_diferenciada), '', false, false, false, 'Se marcado, utilizará regra de avaliação diferenciada informada na Série');

            $resources = SelectOptions::situacoesFuncionamentoEscola();
            $options = array('label' => 'Situação de funcionamento', 'resources' => $resources, 'value' => $this->situacao_funcionamento);
            $this->inputsHelper()->select('situacao_funcionamento', $options);

            $resources = SelectOptions::dependenciasAdministrativasEscola();
            $options = array('label' => 'Dependência administrativa', 'resources' => $resources, 'value' => $this->dependencia_administrativa);
            $this->inputsHelper()->select('dependencia_administrativa', $options);

            $orgaos = OrgaoVinculadoEscola::getDescriptiveValues();
            $helperOptions = ['objectName'  => 'orgao_vinculado_escola'];
            $options = [
                'label' => 'Órgão que a escola pública está vinculada',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->orgao_vinculado_escola,
                    'all_values' => $orgaos
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $resources = [
                null => 'Selecione',
                0 => 'Não',
                1 => 'Sim',
                2 => 'Em tramitação'
            ];
            $options = [
                'label' => 'Regulamentação/Autorização no conselho ou órgão público de educação',
                'resources' => $resources,
                'value' => $this->regulamentacao,
                'size' => 70,
                'required' => false
            ];
            $this->inputsHelper()->select('regulamentacao', $options);

            $resources = SelectOptions::esferasAdministrativasEscola();
            $options = [
                'label' => 'Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização',
                'resources' => $resources,
                'value' => $this->esfera_administrativa,
                'required' => false,
            ];
            $this->inputsHelper()->select('esfera_administrativa', $options);

            $options = array('label' => 'Ato de criação', 'value' => $this->ato_criacao, 'size' => 70, 'required' => false);
            $this->inputsHelper()->text('ato_criacao', $options);

            $options = array('label' => 'Ato autorizativo', 'value' => $this->ato_autorizativo, 'size' => 70, 'required' => false);
            $this->inputsHelper()->text('ato_autorizativo', $options);

            $mantenedoras = MantenedoraDaEscolaPrivada::getDescriptiveValues();
            $helperOptions = ['objectName' => 'mantenedora_escola_privada'];
            $options = [
                'label' => 'Mantenedora da escola privada',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->mantenedora_escola_privada,
                    'all_values' => $mantenedoras
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $resources = array('' => 'Selecione',
                1 => 'Particular',
                2 => 'Comunitária',
                3 => 'Confessional',
                4 => 'Filantrópica');
            $options = array('label' => 'Categoria da escola privada',
                'resources' => $resources,
                'value' => $this->categoria_escola_privada,
                'required' => false,
                'size' => 70);
            $this->inputsHelper()->select('categoria_escola_privada', $options);

            $resources = array('' => 'Selecione',
                1 => 'Estadual',
                2 => 'Municipal',
                3 => 'Estadual e Municipal');
            $options = array('label' => 'Conveniada com poder público',
                'resources' => $resources,
                'value' => $this->conveniada_com_poder_publico,
                'required' => false,
                'size' => 70);
            $this->inputsHelper()->select('conveniada_com_poder_publico', $options);

            $this->campoCnpj("cnpj_mantenedora_principal", "CNPJ da mantenedora principal da escola privada", $this->cnpj_mantenedora_principal);

            $hiddenInputOptions = array('options' => array('value' => $this->secretario_id));
            $helperOptions = array('objectName' => 'secretario', 'hiddenInputOptions' => $hiddenInputOptions);
            $options = array('label' => 'Secretário escolar',
                'size' => 50,
                'required' => false);
            $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

            $resources = SelectOptions::esferasAdministrativasEscola();
            $options = [
                'label' => 'Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização',
                'resources' => $resources,
                'value' => $this->esfera_administrativa,
                'required' => false,
            ];
            $this->inputsHelper()->select('esfera_administrativa', $options);

            $this->campoQuebra();
            $this->addSchoolManagersTable();

            if ($_POST["escola_curso"]) {
                $this->escola_curso = unserialize(urldecode($_POST["escola_curso"]));
            }

            if ($_POST["escola_curso_autorizacao"]) {
                $this->escola_curso_autorizacao = unserialize(urldecode($_POST["escola_curso_autorizacao"]));
            }

            if ($_POST["escola_curso_anos_letivos"]) {
                $this->escola_curso_anos_letivos = unserialize(urldecode($_POST["escola_curso_anos_letivos"]));
            }

            if (is_numeric($this->cod_escola) && !$_POST) {
                $obj = new clsPmieducarEscolaCurso($this->cod_escola);
                $registros = $obj->lista($this->cod_escola);
                if ($registros) {
                    foreach ($registros as $campo) {
                        $this->escola_curso[$campo["ref_cod_curso"]] = $campo["ref_cod_curso"];
                        $this->escola_curso_autorizacao[$campo["ref_cod_curso"]] = $campo["autorizacao"];
                        $this->escola_curso_anos_letivos[$campo["ref_cod_curso"]] = json_decode($campo["anos_letivos"]);
                    }
                }
            }

            if ($_POST["ref_cod_curso"]) {
                $this->escola_curso[$_POST["ref_cod_curso"]] = $_POST["ref_cod_curso"];

                if ($this->autorizacao) {
                    $this->escola_curso_autorizacao[$_POST["ref_cod_curso"]] = $this->autorizacao;
                }

                if ($this->adicionar_anos_letivos) {
                    $this->escola_curso_anos_letivos[$_POST["ref_cod_curso"]] = $this->adicionar_anos_letivos;
                }

                unset($this->ref_cod_curso);
            }

            $this->campoQuebra();
            $this->campoOculto("excluir_curso", "");
            unset($aux);

            if ($this->escola_curso) {
                foreach ($this->escola_curso as $curso) {
                    if ($this->excluir_curso == $curso) {
                        unset($this->escola_curso[$curso]);
                        $this->escola_curso_autorizacao[$curso] = null;
                        $this->excluir_curso = null;
                    } else {
                        $obj_curso = new clsPmieducarCurso($curso);
                        $obj_curso_det = $obj_curso->detalhe();
                        $nm_curso = $obj_curso_det["nm_curso"];
                        $nm_autorizacao = $this->escola_curso_autorizacao[$curso];
                        $anosLetivos = $this->escola_curso_anos_letivos[$curso] ?: [];
                        $this->campoTextoInv("ref_cod_curso_{$curso}", "", $nm_curso, 50, 255, false, false, true);
                        $this->campoTextoInv("autorizacao_{$curso}", "", $nm_autorizacao, 20, 255);
                        $this->campoTextoInv("anos_letivos_{$curso}", "", 'Anos: '.implode(',', $anosLetivos), 20, 255, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_curso').value = '{$curso}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
                        $aux[$curso] = $curso;
                        $aux_autorizacao[$curso] = $nm_autorizacao;
                        $auxAnosLetivos[$curso] = $anosLetivos;
                    }
                }

                unset($this->escola_curso);
                $this->escola_curso = $aux;
                $this->escola_curso_autorizacao = $aux_autorizacao;
                $this->escola_curso_anos_letivos = $auxAnosLetivos;
            }

            $this->campoOculto("escola_curso", serialize($this->escola_curso));
            $this->campoOculto("escola_curso_autorizacao", serialize($this->escola_curso_autorizacao));
            $this->campoOculto("escola_curso_anos_letivos", serialize($this->escola_curso_anos_letivos));
            $opcoes = array("" => "Selecione");

            // EDITAR
            if ($this->cod_escola || $this->ref_cod_instituicao) {
                $objTemp = new clsPmieducarCurso();
                $objTemp->setOrderby("nm_curso");
                $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 1, null, $this->ref_cod_instituicao);

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $opcoes["{$registro['cod_curso']}"] = "{$registro['nm_curso']}";
                    }
                }
            }

            if ($aux) {
                $this->campoLista("ref_cod_curso", "Curso", $opcoes, $this->ref_cod_curso, "", false, "", "<a href='#' onclick=\"getElementById('incluir_curso').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>", false, false);
            } else {
                $this->campoLista("ref_cod_curso", "Curso", $opcoes, $this->ref_cod_curso, "", false, "", "<a href='#' onclick=\"getElementById('incluir_curso').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
            }

            $this->campoTexto("autorizacao", "Autorização", "", 30, 255, false);

            $helperOptions = [
                'objectName' => 'adicionar_anos_letivos'
            ];

            $options = [
                'label' => 'Anos letivos',
                'required' => FALSE,
                'size' => 50,
                'value' => '',
                'options' => [
                    'all_values' => $this->sugestaoAnosLetivos()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $this->campoOculto("incluir_curso", "");
            $this->campoQuebra();

            $helperOptions = array('objectName' => 'local_funcionamento');
            $options = [
                'label' => 'Local de funcionamento',
                'options' => [
                    'values' => $this->local_funcionamento,
                    'all_values' => SelectOptions::locaisFuncionamentoEscola(),
                ],
                'size' => 70,
                'required' => $obrigarCamposCenso
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            // Os campos: Forma de ocupação do prédio e Código da escola que compartilha o prédio
            // serão desabilitados quando local de funcionamento for diferente de 3 (Prédio escolar)
            $disabled = !in_array(LocalFuncionamento::PREDIO_ESCOLAR, $this->local_funcionamento);
            $resources = array(NULL => 'Selecione',
                1 => 'Próprio',
                2 => 'Alugado',
                3 => 'Cedido');
            $options = array('disabled' => $disabled, 'label' => 'Forma de ocupação do prédio', 'resources' => $resources, 'value' => $this->condicao, 'size' => 70, 'required' => false);
            $this->inputsHelper()->select('condicao', $options);

            $resources = [
                null => 'Selecione',
                0 => 'Não',
                1 => 'Sim',
            ];
            $options = [
                'disabled' => $disabled,
                'label' => 'Prédio compartilhado com outra escola',
                'resources' => $resources,
                'value' => $this->predio_compartilhado_outra_escola,
                'size' => 70,
                'required' => false
            ];
            $this->inputsHelper()->select('predio_compartilhado_outra_escola', $options);

            $this->geraCamposCodigoInepEscolaCompartilhada();

            $resources = [
                null => 'Selecione',
                0 => 'Não',
                1 => 'Sim'
            ];
            $options = [
                'label' => 'Fornecimento de água potável para consumo',
                'resources' => $resources,
                'value' => $this->agua_potavel_consumo,
                'required' => $obrigarCamposCenso,
                'size' => 70
            ];
            $this->inputsHelper()->select('agua_potavel_consumo', $options);

            $helperOptions = array('objectName' => 'abastecimento_agua');
            $options = array('label' => 'Abastecimento de água',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => array('values' => $this->abastecimento_agua,
                    'all_values' => array(1 => 'Rede pública',
                        2 => 'Poço artesiano',
                        3 => 'Cacimba/cisterna/poço',
                        4 => 'Fonte/rio/igarapé/riacho/córrego',
                        5 => 'Não há abastecimento de água')));
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = array('objectName' => 'abastecimento_energia');
            $options = array('label' => 'Fonte de energia elétrica',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => array('values' => $this->abastecimento_energia,
                    'all_values' => array(1 => 'Rede pública',
                        2 => 'Gerador movido a combustível fóssil',
                        3 => 'Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, outras)',
                        4 => 'Não há energia elétrica')));
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = array('objectName' => 'esgoto_sanitario');
            $options = array('label' => 'Esgotamento sanitário',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => array('values' => $this->esgoto_sanitario,
                    'all_values' => array(1 => 'Rede pública',
                        2 => 'Fossa séptica',
                        4 => 'Fossa rudimentar/comum',
                        3 => 'Não há esgotamento sanitário')));
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = array('objectName' => 'destinacao_lixo');
            $options = array('label' => 'Destinação do lixo',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => array('values' => $this->destinacao_lixo,
                    'all_values' => array(1 => 'Serviço de coleta',
                        2 => 'Queima',
                        7 => 'Enterra',
                        5 => 'Leva a uma destinação final licenciada pelo poder público',
                        3 => 'Descarta em outra área',)));
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'tratamento_lixo'];
            $options = [
                'label' => 'Tratamento do lixo/resíduos que a escola realiza',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->tratamento_lixo,
                    'all_values' => TratamentoLixo::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $options = [
                'label' => 'Alimentação escolar para os alunos(as)',
                'value' => $this->alimentacao_escolar_alunos,
                'required' => $obrigarCamposCenso,
                'prompt' => 'Selecione',
                'size' => 70
            ];
            $this->inputsHelper()->booleanSelect('alimentacao_escolar_alunos', $options);

            $options = [
                'label' => 'Escola compartilha espaços para atividades de integração escola-comunidade',
                'value' => $this->compartilha_espacos_atividades_integracao,
                'required' => false,
                'prompt' => 'Selecione',
                'size' => 70
            ];
            $this->inputsHelper()->booleanSelect('compartilha_espacos_atividades_integracao', $options);

            $options = [
                'label' => 'Escola usa espaços e equipamentos do entorno escolar para atividades regulares com os alunos(as)',
                'value' => $this->usa_espacos_equipamentos_atividades_regulares,
                'required' => false,
                'prompt' => 'Selecione',
                'size' => 70
            ];
            $this->inputsHelper()->booleanSelect('usa_espacos_equipamentos_atividades_regulares', $options);

            $options = [
                'label' => 'Possui dependências',
                'label_hint' => 'Preencha com: Sim, para exportar os campos de dependências no arquivo do Censo escolar',
                'value' => $this->possui_dependencias,
                'required' => $obrigarCamposCenso,
                'prompt' => 'Selecione',
                'size' => 40
            ];
            $this->inputsHelper()->booleanSelect('possui_dependencias', $options);

            $helperOptions = ['objectName' => 'salas_gerais'];
            $options = [
                'label' => 'Salas gerais',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_gerais,
                    'all_values' => SalasGerais::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'salas_funcionais'];
            $options = [
                'label' => 'Salas funcionais',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_funcionais,
                    'all_values' => SalasFuncionais::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'banheiros'];
            $options = [
                'label' => 'Banheiros',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->banheiros,
                    'all_values' => Banheiros::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'laboratorios'];
            $options = [
                'label' => 'Laboratórios',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->laboratorios,
                    'all_values' => Laboratorios::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'salas_atividades'];
            $options = [
                'label' => 'Salas de atividades',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_atividades,
                    'all_values' => SalasAtividades::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'dormitorios'];
            $options = [
                'label' => 'Dormitórios',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->dormitorios,
                    'all_values' => Dormitorios::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'areas_externas'];
            $options = [
                'label' => 'Áreas externas',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->areas_externas,
                    'all_values' => AreasExternas::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'recursos_acessibilidade'];
            $options = [
                'label' => 'Recursos de acessibilidade',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->recursos_acessibilidade,
                    'all_values' => RecursosAcessibilidade::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $options = array('label' => 'Número de salas de aula utilizadas na escola dentro do prédio escolar', 'resources' => $resources, 'value' => $this->numero_salas_utilizadas_dentro_predio, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4);
            $this->inputsHelper()->integer('numero_salas_utilizadas_dentro_predio', $options);

            $options = array('label' => 'Número de salas de aula utilizadas na escola fora do prédio escolar', 'resources' => $resources, 'value' => $this->numero_salas_utilizadas_fora_predio, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4);
            $this->inputsHelper()->integer('numero_salas_utilizadas_fora_predio', $options);

            $options = array('label' => 'Número de salas de aula climatizadas', 'resources' => $resources, 'value' => $this->numero_salas_climatizadas, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4);
            $this->inputsHelper()->integer('numero_salas_climatizadas', $options);

            $options = array('label' => 'Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida', 'resources' => $resources, 'value' => $this->numero_salas_acessibilidade, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4);
            $this->inputsHelper()->integer('numero_salas_acessibilidade', $options);

            $helperOptions = ['objectName' => 'equipamentos'];
            $options = [
                'label' => 'Equipamentos da escola',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->equipamentos,
                    'all_values' => Equipamentos::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'uso_internet'];
            $options = [
                'label' => 'Acesso à internet',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->uso_internet,
                    'all_values' => UsoInternet::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $options = array(
                'label' => 'Possui internet banda larga',
                'value' => $this->acesso_internet,
                'required' => false,
                'prompt' => 'Selecione',
            );
            $this->inputsHelper()->booleanSelect('acesso_internet', $options);

            $helperOptions = ['objectName' => 'rede_local'];
            $options = [
                'label' => 'Rede local de interligação de computadores',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->rede_local,
                    'all_values' => RedeLocal::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'equipamentos_acesso_internet'];
            $options = [
                'label' => 'Equipamentos que os aluno(a)s usam para acessar a internet da escola',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->equipamentos_acesso_internet,
                    'all_values' => EquipamentosAcessoInternet::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $this->campoRotulo(
                'quantidade_computadores_alunos',
                '<b>Quantidade de computadores de uso dos alunos</b>'
            );

            $options = array('label' => 'Computadores de mesa (desktop)', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_mesa, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('quantidade_computadores_alunos_mesa', $options);

            $options = array('label' => 'Computadores portáteis', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_portateis, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('quantidade_computadores_alunos_portateis', $options);

            $options = array('label' => 'Tablets', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_tablets, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('quantidade_computadores_alunos_tablets', $options);

            $this->campoRotulo(
                'equipamentos_aprendizagem',
                '<b>Quantidade de equipamentos para ensino/aprendizagem</b>'
            );

            $options = array('label' => 'Aparelho de Televisão', 'resources' => $resources, 'value' => $this->televisoes, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('televisoes', $options);

            $options = array('label' => 'Projetor Multimídia (Data show)', 'resources' => $resources, 'value' => $this->projetores_digitais, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('projetores_digitais', $options);

            $options = array('label' => 'Aparelho de som', 'resources' => $resources, 'value' => $this->aparelhos_de_som, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('aparelhos_de_som', $options);

            $options = array('label' => 'Aparelho de DVD/Blu-ray', 'resources' => $resources, 'value' => $this->dvds, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('dvds', $options);

            $options = array('label' => 'Lousa digital', 'resources' => $resources, 'value' => $this->lousas_digitais, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
            $this->inputsHelper()->integer('lousas_digitais', $options);

            $this->campoRotulo(
                'quantidade_profissionais',
                '<b>Quantidade de profissionais</b>'
            );

            foreach ($this->inputsRecursos as $key => $label) {
                $options = array('label' => $label, 'value' => $this->{$key}, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => '');
                $this->inputsHelper()->integer($key, $options);
            }

            $resources = array(NULL => 'Selecione',
                0 => 'Não oferece',
                1 => 'Não exclusivamente',
                2 => 'Exclusivamente');
            $options = array('label' => 'Atendimento educacional especializado - AEE', 'resources' => $resources, 'value' => $this->atendimento_aee, 'required' => false, 'size' => 70);
            $this->inputsHelper()->select('atendimento_aee', $options);

            $habilitaFundamentalCiclo = false;
            if ($this->cod_escola) {
                $objEscola = new clsPmieducarEscola($this->cod_escola);
                $habilitaFundamentalCiclo = dbBool($objEscola->possuiTurmasDoEnsinoFundamentalEmCiclos());
            }

            $options = array(
                'label' => 'Ensino fundamental organizado em ciclos',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->fundamental_ciclo,
                'required' => false,
                'disabled' => !$habilitaFundamentalCiclo
            );
            $this->inputsHelper()->booleanSelect('fundamental_ciclo', $options);

            $obrigarOrganizacaoEnsino = false;
            if ($this->cod_escola) {
                $obrigarOrganizacaoEnsino = new HasDifferentStepsOfChildEducationValidator($this->cod_escola);
                $obrigarOrganizacaoEnsino = $obrigarOrganizacaoEnsino->isValid();
            }

            $helperOptions = ['objectName' => 'organizacao_ensino'];
            $options = [
                'label' => 'Forma(s) de organização do ensino',
                'size' => 50,
                'required' => $obrigarCamposCenso && $obrigarOrganizacaoEnsino,
                'options' => [
                    'values' => $this->organizacao_ensino,
                    'all_values' => OrganizacaoEnsino::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'instrumentos_pedagogicos'];
            $options = [
                'label' => 'Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino aprendizagem',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->instrumentos_pedagogicos,
                    'all_values' => InstrumentosPedagogicos::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $helperOptions = ['objectName' => 'orgaos_colegiados'];
            $options = [
                'label' => 'Órgãos colegiados em funcionamento na escola',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->orgaos_colegiados,
                    'all_values' => OrgaosColegiados::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $options = array(
                'label' => 'Escola faz exame de seleção para ingresso de seus aluno(a)s',
                'label_hint' => 'Avaliação por prova e /ou analise curricular',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->exame_selecao_ingresso,
                'required' => false,
            );
            $this->inputsHelper()->booleanSelect('exame_selecao_ingresso', $options);

            $helperOptions = ['objectName' => 'reserva_vagas_cotas'];
            $options = [
                'label' => 'Reserva de vagas por sistema de cotas para grupos específicos de alunos(as)',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->reserva_vagas_cotas,
                    'all_values' => ReservaVagasCotas::getDescriptiveValues()
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $resources = [
                null => 'Selecione',
                0 => 'Não sei',
                1 => 'Sim',
                2 => 'A escola não possui projeto político pedagógico/proposta pedagógica'
            ];
            $options = array(
                'resources' => $resources,
                'label' => 'Projeto político pedagógico ou a proposta pedagógica da escola atualizado nos últimos 12 meses até a data de referência',
                'label_hint' => '(conforme art. 12 da LDB)',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->projeto_politico_pedagogico,
                'required' => false,
            );
            $this->inputsHelper()->select('projeto_politico_pedagogico', $options);

            $resources = SelectOptions::localizacoesDiferenciadasEscola();
            $options = array('label' => 'Localização diferenciada da escola', 'resources' => $resources, 'value' => $this->localizacao_diferenciada, 'required' => $obrigarCamposCenso, 'size' => 70);
            $this->inputsHelper()->select('localizacao_diferenciada', $options);

            $resources = array(NULL => 'Selecione',
                1 => 'Não utiliza',
                2 => 'Quilombola',
                3 => 'Indígena');

            $options = [
                'label' => 'Educação escolar indígena',
                'value' => $this->educacao_indigena,
                'required' => false,
                'prompt' => 'Selecione',
            ];
            $this->inputsHelper()->booleanSelect('educacao_indigena', $options);

            $resources = [
                null => 'Selecione',
                1 => 'Língua Portuguesa',
                2 => 'Língua Indígena'
            ];
            $habilitaLiguaMinistrada = $this->educacao_indigena == 1;
            $options = array('label' => 'Língua em que o ensino é ministrado',
                'resources' => $resources,
                'value' => $this->lingua_ministrada,
                'required' => false,
                'disabled' => !$habilitaLiguaMinistrada,
                'size' => 70);
            $this->inputsHelper()->select('lingua_ministrada', $options);

            $resources_ = Portabilis_Utils_Database::fetchPreparedQuery('SELECT * FROM modules.lingua_indigena_educacenso');

            foreach ($resources_ as $reg) {
                $resources[$reg['id']] = $reg['lingua'];
            }

            $helperOptions = ['objectName' => 'codigo_lingua_indigena'];
            $options = [
                'label' => 'Línguas indígenas',
                'size' => 70,
                'required' => false,
                'options' => [
                    'values' => $this->codigo_lingua_indigena,
                    'all_values' => $resources
                ]
            ];
            $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

            $resources = SelectOptions::unidadesVinculadasEscola();
            $options = [
                'label' => 'Unidade vinculada à Escola de Educação Básica ou Unidade Ofertante de Educação Superior',
                'resources' => $resources,
                'value' => $this->unidade_vinculada_outra_instituicao,
                'size' => 70,
                'required' => false
            ];
            $this->inputsHelper()->select('unidade_vinculada_outra_instituicao', $options);

            $this->campoTexto("inep_escola_sede", "Código da escola sede", $this->inep_escola_sede, 10, 8, false);

            $options = [
                'label' => 'Código da IES',
                'required' => false
            ];
            $helperOptions = [
                'objectName' => 'codigo_ies',
                'hiddenInputOptions' => [
                    'options' => ['value' => $this->codigo_ies]
                ]
            ];
            $this->inputsHelper()->simpleSearchIes(null, $options, $helperOptions);
        }
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 3, "educar_escola_lst.php");
        $orgao_vinculado_escola = implode(',', $this->orgao_vinculado_escola);
        $mantenedora_escola_privada = implode(',', $this->mantenedora_escola_privada);
        $local_funcionamento = implode(',', $this->local_funcionamento);
        $abastecimento_agua = implode(',', $this->abastecimento_agua);
        $abastecimento_energia = implode(',', $this->abastecimento_energia);
        $esgoto_sanitario = implode(',', $this->esgoto_sanitario);
        $destinacao_lixo = implode(',', $this->destinacao_lixo);
        $tratamento_lixo = implode(',', $this->tratamento_lixo);
        $salas_funcionais = implode(',', $this->salas_funcionais);
        $salas_gerais = implode(',', $this->salas_gerais);
        $banheiros = implode(',', $this->banheiros);
        $laboratorios = implode(',', $this->laboratorios);
        $salas_atividades = implode(',', $this->salas_atividades);
        $dormitorios = implode(',', $this->dormitorios);
        $areas_externas = implode(',', $this->areas_externas);
        $recursos_acessibilidade = implode(',', $this->recursos_acessibilidade);
        $equipamentos = implode(',', $this->equipamentos);
        $uso_internet = implode(',', $this->uso_internet);
        $rede_local = implode(',', $this->rede_local);
        $equipamentos_acesso_internet = implode(',', $this->equipamentos_acesso_internet);
        $organizacao_ensino = implode(',', $this->organizacao_ensino);
        $instrumentos_pedagogicos = implode(',', $this->instrumentos_pedagogicos);
        $orgaos_colegiados = implode(',', $this->orgaos_colegiados);
        $reserva_vagas_cotas = implode(',', $this->reserva_vagas_cotas);
        $codigo_lingua_indigena = implode(',', $this->codigo_lingua_indigena);

        if (!$this->validaDigitosInepEscola($this->escola_inep_id, 'Código INEP')) {
            return false;
        }

        if (!$this->validaDadosTelefones()) {
            return false;
        }

        if (!$this->validaCampoPossuiDependencias()) {
            return false;
        }

        if (!$this->validaCamposCenso()) {
            return false;
        }

        $this->validateManagersRules();

        for ( $i = 1; $i <= 6; $i++) {
            $seq = $i == 1 ? '' : $i;
            $campo = 'codigo_inep_escola_compartilhada'.$seq;
            $ret = $this->validaDigitosInepEscola($this->$campo, 'Código da escola que compartilha o prédio '.$i);
            if (!$ret) {
                return false;
            }
        }

        if (!$this->validaOpcoesUnicasMultipleSearch()) {
            return false;
        }

        $this->bloquear_lancamento_diario_anos_letivos_encerrados = is_null($this->bloquear_lancamento_diario_anos_letivos_encerrados) ? 0 : 1;
        $this->utiliza_regra_diferenciada = !is_null($this->utiliza_regra_diferenciada);

        if ($this->com_cnpj) {
            $objPessoa = new clsPessoa_(false, $this->fantasia, $this->pessoa_logada, $this->p_http, "J", false, false, $this->p_email);
            $this->ref_idpes = $objPessoa->cadastra();

            if ($this->ref_idpes) {
                $obj_pes_juridica = new clsJuridica($this->ref_idpes, $this->cnpj, $this->fantasia, false, false, $this->pessoa_logada);
                $cadastrou = $obj_pes_juridica->cadastra();

                if ($cadastrou) {
                    $obj = new clsPmieducarEscola(null, $this->pessoa_logada, null, $this->ref_cod_instituicao, $this->zona_localizacao, $this->ref_cod_escola_rede_ensino, $this->ref_idpes, $this->sigla, null, null, 1, null, $this->bloquear_lancamento_diario_anos_letivos_encerrados);
                    $obj->situacao_funcionamento = $this->situacao_funcionamento;
                    $obj->dependencia_administrativa = $this->dependencia_administrativa;
                    $obj->orgao_vinculado_escola = $orgao_vinculado_escola;
                    $obj->latitude = $this->latitude;
                    $obj->longitude = $this->longitude;
                    $obj->regulamentacao = $this->regulamentacao;
                    $obj->ref_idpes_gestor = $this->gestor_id;
                    $obj->cargo_gestor = $this->cargo_gestor;
                    $obj->email_gestor = $this->email_gestor;
                    $obj->local_funcionamento = $local_funcionamento;
                    $obj->condicao = $this->condicao;
                    $obj->predio_compartilhado_outra_escola = $this->predio_compartilhado_outra_escola;
                    $obj->codigo_inep_escola_compartilhada = $this->codigo_inep_escola_compartilhada;
                    $obj->codigo_inep_escola_compartilhada2 = $this->codigo_inep_escola_compartilhada2;
                    $obj->codigo_inep_escola_compartilhada3 = $this->codigo_inep_escola_compartilhada3;
                    $obj->codigo_inep_escola_compartilhada4 = $this->codigo_inep_escola_compartilhada4;
                    $obj->codigo_inep_escola_compartilhada5 = $this->codigo_inep_escola_compartilhada5;
                    $obj->codigo_inep_escola_compartilhada6 = $this->codigo_inep_escola_compartilhada6;
                    $obj->agua_potavel_consumo = $this->agua_potavel_consumo;
                    $obj->abastecimento_agua = $abastecimento_agua;
                    $obj->abastecimento_energia = $abastecimento_energia;
                    $obj->esgoto_sanitario = $esgoto_sanitario;
                    $obj->destinacao_lixo = $destinacao_lixo;
                    $obj->tratamento_lixo = $tratamento_lixo;
                    $obj->alimentacao_escolar_alunos = $this->alimentacao_escolar_alunos;
                    $obj->compartilha_espacos_atividades_integracao = $this->compartilha_espacos_atividades_integracao;
                    $obj->usa_espacos_equipamentos_atividades_regulares = $this->usa_espacos_equipamentos_atividades_regulares;
                    $obj->salas_funcionais = $salas_funcionais;
                    $obj->salas_gerais = $salas_gerais;
                    $obj->banheiros = $banheiros;
                    $obj->laboratorios = $laboratorios;
                    $obj->salas_atividades = $salas_atividades;
                    $obj->dormitorios = $dormitorios;
                    $obj->areas_externas = $areas_externas;
                    $obj->recursos_acessibilidade = $recursos_acessibilidade;
                    $obj->possui_dependencias = $this->possui_dependencias;
                    $obj->numero_salas_utilizadas_dentro_predio = $this->numero_salas_utilizadas_dentro_predio;
                    $obj->numero_salas_utilizadas_fora_predio = $this->numero_salas_utilizadas_fora_predio;
                    $obj->numero_salas_climatizadas = $this->numero_salas_climatizadas;
                    $obj->numero_salas_acessibilidade = $this->numero_salas_acessibilidade;
                    $obj->total_funcionario = $this->total_funcionario;
                    $obj->atendimento_aee = $this->atendimento_aee;
                    $obj->fundamental_ciclo = $this->fundamental_ciclo;
                    $obj->organizacao_ensino = $organizacao_ensino;
                    $obj->instrumentos_pedagogicos = $instrumentos_pedagogicos;
                    $obj->orgaos_colegiados = $orgaos_colegiados;
                    $obj->exame_selecao_ingresso = $this->exame_selecao_ingresso;
                    $obj->reserva_vagas_cotas = $reserva_vagas_cotas;
                    $obj->projeto_politico_pedagogico = $this->projeto_politico_pedagogico;
                    $obj->localizacao_diferenciada = $this->localizacao_diferenciada;
                    $obj->educacao_indigena = $this->educacao_indigena;
                    $obj->lingua_ministrada = $this->lingua_ministrada;
                    $obj->codigo_lingua_indigena = $this->codigo_lingua_indigena;
                    $obj->espaco_brasil_aprendizado = $this->espaco_brasil_aprendizado;
                    $obj->abre_final_semana = $this->abre_final_semana;
                    $obj->codigo_lingua_indigena = $codigo_lingua_indigena;
                    $obj->proposta_pedagogica = $this->proposta_pedagogica;
                    $obj->equipamentos = $equipamentos;
                    $obj->uso_internet = $uso_internet;
                    $obj->rede_local = $rede_local;
                    $obj->equipamentos_acesso_internet = $equipamentos_acesso_internet;
                    $obj->quantidade_computadores_alunos_mesa = $this->quantidade_computadores_alunos_mesa;
                    $obj->quantidade_computadores_alunos_portateis = $this->quantidade_computadores_alunos_portateis;
                    $obj->quantidade_computadores_alunos_tablets = $this->quantidade_computadores_alunos_tablets;
                    $obj->lousas_digitais = $this->lousas_digitais;
                    $obj->televisoes = $this->televisoes;
                    $obj->dvds = $this->dvds;
                    $obj->aparelhos_de_som = $this->aparelhos_de_som;
                    $obj->projetores_digitais = $this->projetores_digitais;
                    $obj->acesso_internet = $this->acesso_internet;
                    $obj->ato_criacao = $this->ato_criacao;
                    $obj->ato_autorizativo = $this->ato_autorizativo;
                    $obj->ref_idpes_secretario_escolar = $this->secretario_id;
                    $obj->unidade_vinculada_outra_instituicao = $this->unidade_vinculada_outra_instituicao;
                    $obj->inep_escola_sede = $this->inep_escola_sede;
                    $obj->codigo_ies = $this->codigo_ies_id;
                    $obj->categoria_escola_privada = $this->categoria_escola_privada;
                    $obj->conveniada_com_poder_publico = $this->conveniada_com_poder_publico;
                    $obj->mantenedora_escola_privada = $mantenedora_escola_privada;
                    $obj->cnpj_mantenedora_principal = idFederal2int($this->cnpj_mantenedora_principal);
                    $obj->esfera_administrativa = $this->esfera_administrativa;
                    $obj->iddis = (int)$this->district_id;
                    foreach ($this->inputsRecursos as $key => $value) {
                        $obj->{$key} = $this->{$key};
                    }

                    $cod_escola = $cadastrou1 = $obj->cadastra();

                    if ($cadastrou1) {
                        $escola = new clsPmieducarEscola($cod_escola);
                        $escola = $escola->detalhe();
                        $auditoria = new clsModulesAuditoriaGeral("escola", $this->pessoa_logada, $cod_escola);
                        $auditoria->inclusao($escola);

                        $objTelefone = new clsPessoaTelefone($this->ref_idpes);
                        $objTelefone->excluiTodos();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 1, str_replace("-", "", $this->p_telefone_1), $this->p_ddd_telefone_1);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 2, str_replace("-", "", $this->p_telefone_2), $this->p_ddd_telefone_2);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 3, str_replace("-", "", $this->p_telefone_mov), $this->p_ddd_telefone_mov);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 4, str_replace("-", "", $this->p_telefone_fax), $this->p_ddd_telefone_fax);
                        $objTelefone->cadastra();

                        $this->saveAddress($this->ref_idpes);

                        //-----------------------CADASTRA CURSO------------------------//
                        $this->escola_curso = unserialize(urldecode($this->escola_curso));
                        $this->escola_curso_autorizacao = unserialize(urldecode($this->escola_curso_autorizacao));
                        $this->escola_curso_anos_letivos = unserialize(urldecode($this->escola_curso_anos_letivos));

                        if ($this->escola_curso) {
                            foreach ($this->escola_curso as $campo) {
                                $curso_escola = new clsPmieducarEscolaCurso($cadastrou1, $campo, null, $this->pessoa_logada, null, null, 1, $this->escola_curso_autorizacao[$campo], $this->escola_curso_anos_letivos[$campo]);
                                $cadastrou_ = $curso_escola->cadastra();

                                if (!$cadastrou_) {
                                    $this->mensagem = "Cadastro não realizado.<br>";

                                    return false;
                                }
                            }

                            $this->storeManagers($cod_escola);
                        }
                        //-----------------------FIM CADASTRA CURSO------------------------//
                    } else {
                        $this->mensagem = "Cadastro não realizado (clsPmieducarEscola).<br>";
                        return false;
                    }
                } else {
                    $this->mensagem = "Cadastro não realizado (clsJuridica).<br>";
                    return false;
                }

                $this->saveInep($escola['cod_escola']);

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

                throw new HttpResponseException(
                    new RedirectResponse('educar_escola_lst.php')
                );
            } else {
                $this->mensagem = "Cadastro não realizado (clsPessoa_).<br>";
                return false;
            }
        } elseif ($this->sem_cnpj) {
            $obj = new clsPmieducarEscola(null, $this->pessoa_logada, null, $this->ref_cod_instituicao, $this->zona_localizacao, $this->ref_cod_escola_rede_ensino, null, $this->sigla, null, null, 1, null, $this->bloquear_lancamento_diario_anos_letivos_encerrados, $this->utiliza_regra_diferenciada);
            $obj->dependencia_administrativa = $this->dependencia_administrativa;
            $obj->orgao_vinculado_escola = $orgao_vinculado_escola;
            $obj->latitude = $this->latitude;
            $obj->longitude = $this->longitude;
            $obj->regulamentacao = $this->regulamentacao;
            $obj->situacao_funcionamento = $this->situacao_funcionamento;
            $obj->ref_idpes_gestor = $this->gestor_id;
            $obj->cargo_gestor = $this->cargo_gestor;
            $obj->email_gestor = $this->email_gestor;
            $obj->local_funcionamento = $local_funcionamento;
            $obj->condicao = $this->condicao;
            $obj->predio_compartilhado_outra_escola = $this->predio_compartilhado_outra_escola;
            $obj->codigo_inep_escola_compartilhada = $this->codigo_inep_escola_compartilhada;
            $obj->codigo_inep_escola_compartilhada2 = $this->codigo_inep_escola_compartilhada2;
            $obj->codigo_inep_escola_compartilhada3 = $this->codigo_inep_escola_compartilhada3;
            $obj->codigo_inep_escola_compartilhada4 = $this->codigo_inep_escola_compartilhada4;
            $obj->codigo_inep_escola_compartilhada5 = $this->codigo_inep_escola_compartilhada5;
            $obj->codigo_inep_escola_compartilhada6 = $this->codigo_inep_escola_compartilhada6;
            $obj->agua_potavel_consumo = $this->agua_potavel_consumo;
            $obj->abastecimento_agua = $abastecimento_agua;
            $obj->abastecimento_energia = $abastecimento_energia;
            $obj->esgoto_sanitario = $esgoto_sanitario;
            $obj->destinacao_lixo = $destinacao_lixo;
            $obj->tratamento_lixo = $tratamento_lixo;
            $obj->alimentacao_escolar_alunos = $this->alimentacao_escolar_alunos;
            $obj->compartilha_espacos_atividades_integracao = $this->compartilha_espacos_atividades_integracao;
            $obj->usa_espacos_equipamentos_atividades_regulares = $this->usa_espacos_equipamentos_atividades_regulares;
            $obj->salas_funcionais = $salas_funcionais;
            $obj->salas_gerais = $salas_gerais;
            $obj->banheiros = $banheiros;
            $obj->laboratorios = $laboratorios;
            $obj->salas_atividades = $salas_atividades;
            $obj->dormitorios = $dormitorios;
            $obj->areas_externas = $areas_externas;
            $obj->recursos_acessibilidade = $recursos_acessibilidade;
            $obj->possui_dependencias = $this->possui_dependencias;
            $obj->numero_salas_utilizadas_dentro_predio = $this->numero_salas_utilizadas_dentro_predio;
            $obj->numero_salas_utilizadas_fora_predio = $this->numero_salas_utilizadas_fora_predio;
            $obj->numero_salas_climatizadas = $this->numero_salas_climatizadas;
            $obj->numero_salas_acessibilidade = $this->numero_salas_acessibilidade;
            $obj->total_funcionario = $this->total_funcionario;
            $obj->atendimento_aee = $this->atendimento_aee;
            $obj->fundamental_ciclo = $this->fundamental_ciclo;
            $obj->organizacao_ensino = $this->organizacao_ensino;
            $obj->instrumentos_pedagogicos = $this->instrumentos_pedagogicos;
            $obj->orgaos_colegiados = $orgaos_colegiados;
            $obj->exame_selecao_ingresso = $this->exame_selecao_ingresso;
            $obj->reserva_vagas_cotas = $reserva_vagas_cotas;
            $obj->projeto_politico_pedagogico = $this->projeto_politico_pedagogico;
            $obj->localizacao_diferenciada = $this->localizacao_diferenciada;
            $obj->educacao_indigena = $this->educacao_indigena;
            $obj->lingua_ministrada = $this->lingua_ministrada;
            $obj->codigo_lingua_indigena = $this->codigo_lingua_indigena;
            $obj->espaco_brasil_aprendizado = $this->espaco_brasil_aprendizado;
            $obj->abre_final_semana = $this->abre_final_semana;
            $obj->codigo_lingua_indigena = $codigo_lingua_indigena;
            $obj->proposta_pedagogica = $this->proposta_pedagogica;
            $obj->equipamentos = $equipamentos;
            $obj->uso_internet = $uso_internet;
            $obj->rede_local = $rede_local;
            $obj->equipamentos_acesso_internet = $equipamentos_acesso_internet;
            $obj->quantidade_computadores_alunos_mesa = $this->quantidade_computadores_alunos_mesa;
            $obj->quantidade_computadores_alunos_portateis = $this->quantidade_computadores_alunos_portateis;
            $obj->quantidade_computadores_alunos_tablets = $this->quantidade_computadores_alunos_tablets;
            $obj->lousas_digitais = $this->lousas_digitais;
            $obj->televisoes = $this->televisoes;
            $obj->dvds = $this->dvds;
            $obj->aparelhos_de_som = $this->aparelhos_de_som;
            $obj->projetores_digitais = $this->projetores_digitais;
            $obj->acesso_internet = $this->acesso_internet;
            $obj->ato_criacao = $this->ato_criacao;
            $obj->ato_autorizativo = $this->ato_autorizativo;
            $obj->ref_idpes_secretario_escolar = $this->secretario_id;
            $obj->unidade_vinculada_outra_instituicao = $this->unidade_vinculada_outra_instituicao;
            $obj->inep_escola_sede = $this->inep_escola_sede;
            $obj->codigo_ies = $this->codigo_ies_id;
            $obj->categoria_escola_privada = $this->categoria_escola_privada;
            $obj->conveniada_com_poder_publico = $this->conveniada_com_poder_publico;
            $obj->mantenedora_escola_privada = $mantenedora_escola_privada;
            $obj->cnpj_mantenedora_principal = idFederal2int($this->cnpj_mantenedora_principal);
            $obj->esfera_administrativa = $this->esfera_administrativa;
            $obj->iddis = (int)$this->district_id;
            foreach ($this->inputsRecursos as $key => $value) {
                $obj->{$key} = $this->{$key};
            }

            $cod_escola = $cadastrou = $obj->cadastra();

            if ($cadastrou) {
                $escola = new clsPmieducarEscola($cod_escola);
                $escola = $escola->detalhe();
                $auditoria = new clsModulesAuditoriaGeral("escola", $this->pessoa_logada, $cod_escola);
                $auditoria->inclusao($escola);

                    //-----------------------CADASTRA CURSO------------------------//
                    $this->escola_curso = unserialize(urldecode($this->escola_curso));
                    $this->escola_curso_autorizacao = unserialize(urldecode($this->escola_curso_autorizacao));
                    $this->escola_curso_anos_letivos = unserialize(urldecode($this->escola_curso_anos_letivos));

                    if ($this->escola_curso) {
                        foreach ($this->escola_curso as $campo) {
                            $curso_escola = new clsPmieducarEscolaCurso($cadastrou, $campo, null, $this->pessoa_logada, null, null, 1, $this->escola_curso_autorizacao[$campo], $this->escola_curso_anos_letivos[$campo]);
                            $cadastrou_ = $curso_escola->cadastra();

                            if (!$cadastrou_) {
                                $this->mensagem = "Cadastro não realizado.<br>";

                                return false;
                            }
                        }

                        $this->storeManagers($cod_escola);
                    }
                    $this->saveInep($escola['cod_escola']);
                    //-----------------------FIM CADASTRA CURSO------------------------//

                    $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

                    throw new HttpResponseException(
                        new RedirectResponse('educar_escola_lst.php')
                    );

            } else {
                $this->mensagem = "Cadastro não realizado (clsPmieducarEscola).<br>";
                return false;
            }
        }
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7, "educar_escola_lst.php");

        if (!$this->validaDigitosInepEscola($this->escola_inep_id, 'Código INEP')) {
            return false;
        }

        if (!$this->validaDadosTelefones()) {
            return false;
        }

        if (!$this->validaCampoPossuiDependencias()) {
            return false;
        }

        if (!$this->validaCamposCenso()) {
            return false;
        }

        $this->validateManagersRules();

        for ( $i = 1; $i <= 6; $i++) {
            $seq = $i == 1 ? '' : $i;
            $campo = 'codigo_inep_escola_compartilhada'.$seq;
            $ret = $this->validaDigitosInepEscola($this->$campo, 'Código da escola que compartilha o prédio '.$i);
            if (!$ret) {
                return false;
            }
        }

        $orgao_vinculado_escola = implode(',', $this->orgao_vinculado_escola);
        $mantenedora_escola_privada = implode(',', $this->mantenedora_escola_privada);
        $local_funcionamento = implode(',', $this->local_funcionamento);
        $abastecimento_agua = implode(',', $this->abastecimento_agua);
        $abastecimento_energia = implode(',', $this->abastecimento_energia);
        $esgoto_sanitario = implode(',', $this->esgoto_sanitario);
        $destinacao_lixo = implode(',', $this->destinacao_lixo);
        $tratamento_lixo = implode(',', $this->tratamento_lixo);
        $salas_funcionais = implode(',', $this->salas_funcionais);
        $salas_gerais = implode(',', $this->salas_gerais);
        $banheiros = implode(',', $this->banheiros);
        $laboratorios = implode(',', $this->laboratorios);
        $salas_atividades = implode(',', $this->salas_atividades);
        $dormitorios = implode(',', $this->dormitorios);
        $areas_externas = implode(',', $this->areas_externas);
        $recursos_acessibilidade = implode(',', $this->recursos_acessibilidade);
        $equipamentos = implode(',', $this->equipamentos);
        $uso_internet = implode(',', $this->uso_internet);
        $rede_local = implode(',', $this->rede_local);
        $equipamentos_acesso_internet = implode(',', $this->equipamentos_acesso_internet);
        $organizacao_ensino = implode(',', $this->organizacao_ensino);
        $instrumentos_pedagogicos = implode(',', $this->instrumentos_pedagogicos);
        $orgaos_colegiados = implode(',', $this->orgaos_colegiados);
        $reserva_vagas_cotas = implode(',', $this->reserva_vagas_cotas);
        $codigo_lingua_indigena = implode(',', $this->codigo_lingua_indigena);

        if (!$this->validaOpcoesUnicasMultipleSearch()){
            return false;
        }

        $this->bloquear_lancamento_diario_anos_letivos_encerrados = is_null($this->bloquear_lancamento_diario_anos_letivos_encerrados) ? 0 : 1;
        $this->utiliza_regra_diferenciada = !is_null($this->utiliza_regra_diferenciada);
        $obj = new clsPmieducarEscola($this->cod_escola);
        $escolaDetAntigo = $obj->detalhe();

        if ($this->cod_escola) {
            $obj = new clsPmieducarEscola($this->cod_escola, null, $this->pessoa_logada, $this->ref_cod_instituicao, $this->zona_localizacao, $this->ref_cod_escola_rede_ensino, $this->ref_idpes, $this->sigla, null, null, 1, $this->bloquear_lancamento_diario_anos_letivos_encerrados, $this->utiliza_regra_diferenciada);
            $obj->dependencia_administrativa = $this->dependencia_administrativa;
            $obj->orgao_vinculado_escola = $orgao_vinculado_escola;
            $obj->latitude = $this->latitude;
            $obj->longitude = $this->longitude;
            $obj->regulamentacao = $this->regulamentacao;
            $obj->situacao_funcionamento = $this->situacao_funcionamento;
            $obj->ref_idpes_gestor = $this->gestor_id;
            $obj->cargo_gestor = $this->cargo_gestor;
            $obj->email_gestor = $this->email_gestor;
            $obj->local_funcionamento = $local_funcionamento;
            $obj->condicao = $this->condicao;
            $obj->predio_compartilhado_outra_escola = $this->predio_compartilhado_outra_escola;
            $obj->codigo_inep_escola_compartilhada = $this->codigo_inep_escola_compartilhada;
            $obj->codigo_inep_escola_compartilhada2 = $this->codigo_inep_escola_compartilhada2;
            $obj->codigo_inep_escola_compartilhada3 = $this->codigo_inep_escola_compartilhada3;
            $obj->codigo_inep_escola_compartilhada4 = $this->codigo_inep_escola_compartilhada4;
            $obj->codigo_inep_escola_compartilhada5 = $this->codigo_inep_escola_compartilhada5;
            $obj->codigo_inep_escola_compartilhada6 = $this->codigo_inep_escola_compartilhada6;
            $obj->agua_potavel_consumo = $this->agua_potavel_consumo;
            $obj->abastecimento_agua = $abastecimento_agua;
            $obj->abastecimento_energia = $abastecimento_energia;
            $obj->esgoto_sanitario = $esgoto_sanitario;
            $obj->destinacao_lixo = $destinacao_lixo;
            $obj->tratamento_lixo = $tratamento_lixo;
            $obj->alimentacao_escolar_alunos = $this->alimentacao_escolar_alunos;
            $obj->compartilha_espacos_atividades_integracao = $this->compartilha_espacos_atividades_integracao;
            $obj->usa_espacos_equipamentos_atividades_regulares = $this->usa_espacos_equipamentos_atividades_regulares;
            $obj->salas_funcionais = $salas_funcionais;
            $obj->salas_gerais = $salas_gerais;
            $obj->banheiros = $banheiros;
            $obj->laboratorios = $laboratorios;
            $obj->salas_atividades = $salas_atividades;
            $obj->dormitorios = $dormitorios;
            $obj->areas_externas = $areas_externas;
            $obj->recursos_acessibilidade = $recursos_acessibilidade;
            $obj->possui_dependencias = $this->possui_dependencias;
            $obj->numero_salas_utilizadas_dentro_predio = $this->numero_salas_utilizadas_dentro_predio;
            $obj->numero_salas_utilizadas_fora_predio = $this->numero_salas_utilizadas_fora_predio;
            $obj->numero_salas_climatizadas = $this->numero_salas_climatizadas;
            $obj->numero_salas_acessibilidade = $this->numero_salas_acessibilidade;
            $obj->total_funcionario = $this->total_funcionario;
            $obj->atendimento_aee = $this->atendimento_aee;
            $obj->fundamental_ciclo = $this->fundamental_ciclo;
            $obj->organizacao_ensino = $organizacao_ensino;
            $obj->instrumentos_pedagogicos = $instrumentos_pedagogicos;
            $obj->orgaos_colegiados = $orgaos_colegiados;
            $obj->exame_selecao_ingresso = $this->exame_selecao_ingresso;
            $obj->reserva_vagas_cotas = $reserva_vagas_cotas;
            $obj->projeto_politico_pedagogico = $this->projeto_politico_pedagogico;
            $obj->localizacao_diferenciada = $this->localizacao_diferenciada;
            $obj->educacao_indigena = $this->educacao_indigena;
            $obj->lingua_ministrada = $this->lingua_ministrada;
            $obj->codigo_lingua_indigena = $this->codigo_lingua_indigena;
            $obj->espaco_brasil_aprendizado = $this->espaco_brasil_aprendizado;
            $obj->abre_final_semana = $this->abre_final_semana;
            $obj->codigo_lingua_indigena = $codigo_lingua_indigena;
            $obj->proposta_pedagogica = $this->proposta_pedagogica;
            $obj->equipamentos = $equipamentos;
            $obj->uso_internet = $uso_internet;
            $obj->rede_local = $rede_local;
            $obj->equipamentos_acesso_internet = $equipamentos_acesso_internet;
            $obj->quantidade_computadores_alunos_mesa = $this->quantidade_computadores_alunos_mesa;
            $obj->quantidade_computadores_alunos_portateis = $this->quantidade_computadores_alunos_portateis;
            $obj->quantidade_computadores_alunos_tablets = $this->quantidade_computadores_alunos_tablets;
            $obj->lousas_digitais = $this->lousas_digitais;
            $obj->televisoes = $this->televisoes;
            $obj->dvds = $this->dvds;
            $obj->aparelhos_de_som = $this->aparelhos_de_som;
            $obj->projetores_digitais = $this->projetores_digitais;
            $obj->acesso_internet = $this->acesso_internet;
            $obj->ato_criacao = $this->ato_criacao;
            $obj->ato_autorizativo = $this->ato_autorizativo;
            $obj->ref_idpes_secretario_escolar = $this->secretario_id;
            $obj->unidade_vinculada_outra_instituicao = $this->unidade_vinculada_outra_instituicao;
            $obj->inep_escola_sede = $this->inep_escola_sede;
            $obj->codigo_ies = $this->codigo_ies_id;
            $obj->categoria_escola_privada = $this->categoria_escola_privada;
            $obj->conveniada_com_poder_publico = $this->conveniada_com_poder_publico;
            $obj->mantenedora_escola_privada = $mantenedora_escola_privada;
            $obj->cnpj_mantenedora_principal = idFederal2int($this->cnpj_mantenedora_principal);
            $obj->esfera_administrativa = $this->esfera_administrativa;
            $obj->iddis = (int)$this->district_id;
            foreach ($this->inputsRecursos as $key => $value) {
                $obj->{$key} = $this->{$key};
            }

            $editou = $obj->edita();

            if ($editou) {
                $escolaDetAtual = $obj->detalhe();
                $auditoria = new clsModulesAuditoriaGeral("escola", $this->pessoa_logada, $this->cod_escola);
                $auditoria->alteracao($escolaDetAntigo, $escolaDetAtual);
            }
        } else {
            $obj = new clsPmieducarEscola(null, $this->pessoa_logada, null, $this->ref_cod_instituicao, $this->zona_localizacao, $this->ref_cod_escola_rede_ensino, $this->ref_idpes, $this->sigla, null, null, 1, $this->bloquear_lancamento_diario_anos_letivos_encerrados, $this->utiliza_regra_diferenciada);
            $obj->situacao_funcionamento = $this->situacao_funcionamento;
            $obj->dependencia_administrativa = $this->dependencia_administrativa;
            $obj->orgao_vinculado_escola = $orgao_vinculado_escola;
            $obj->latitude = $this->latitude;
            $obj->longitude = $this->longitude;
            $obj->regulamentacao = $this->regulamentacao;
            $obj->ref_idpes_gestor = $this->gestor_id;
            $obj->cargo_gestor = $this->cargo_gestor;
            $obj->email_gestor = $this->email_gestor;
            $obj->local_funcionamento = $local_funcionamento;
            $obj->condicao = $this->condicao;
            $obj->predio_compartilhado_outra_escola = $this->predio_compartilhado_outra_escola;
            $obj->codigo_inep_escola_compartilhada = $this->codigo_inep_escola_compartilhada;
            $obj->codigo_inep_escola_compartilhada2 = $this->codigo_inep_escola_compartilhada2;
            $obj->codigo_inep_escola_compartilhada3 = $this->codigo_inep_escola_compartilhada3;
            $obj->codigo_inep_escola_compartilhada4 = $this->codigo_inep_escola_compartilhada4;
            $obj->codigo_inep_escola_compartilhada5 = $this->codigo_inep_escola_compartilhada5;
            $obj->codigo_inep_escola_compartilhada6 = $this->codigo_inep_escola_compartilhada6;
            $obj->agua_potavel_consumo = $this->agua_potavel_consumo;
            $obj->abastecimento_agua = $abastecimento_agua;
            $obj->abastecimento_energia = $abastecimento_energia;
            $obj->esgoto_sanitario = $esgoto_sanitario;
            $obj->destinacao_lixo = $destinacao_lixo;
            $obj->tratamento_lixo = $tratamento_lixo;
            $obj->alimentacao_escolar_alunos = $this->alimentacao_escolar_alunos;
            $obj->compartilha_espacos_atividades_integracao = $this->compartilha_espacos_atividades_integracao;
            $obj->usa_espacos_equipamentos_atividades_regulares = $this->usa_espacos_equipamentos_atividades_regulares;
            $obj->salas_funcionais = $salas_funcionais;
            $obj->salas_gerais = $salas_gerais;
            $obj->banheiros = $banheiros;
            $obj->laboratorios = $laboratorios;
            $obj->salas_atividades = $salas_atividades;
            $obj->dormitorios = $dormitorios;
            $obj->areas_externas = $areas_externas;
            $obj->recursos_acessibilidade = $recursos_acessibilidade;
            $obj->possui_dependencias = $this->possui_dependencias;
            $obj->numero_salas_utilizadas_dentro_predio = $this->numero_salas_utilizadas_dentro_predio;
            $obj->numero_salas_utilizadas_fora_predio = $this->numero_salas_utilizadas_fora_predio;
            $obj->numero_salas_climatizadas = $this->numero_salas_climatizadas;
            $obj->numero_salas_acessibilidade = $this->numero_salas_acessibilidade;
            $obj->total_funcionario = $this->total_funcionario;
            $obj->atendimento_aee = $this->atendimento_aee;
            $obj->fundamental_ciclo = $this->fundamental_ciclo;
            $obj->organizacao_ensino = $organizacao_ensino;
            $obj->instrumentos_pedagogicos = $instrumentos_pedagogicos;
            $obj->orgaos_colegiados = $orgaos_colegiados;
            $obj->exame_selecao_ingresso = $this->exame_selecao_ingresso;
            $obj->reserva_vagas_cotas = $reserva_vagas_cotas;
            $obj->projeto_politico_pedagogico = $this->projeto_politico_pedagogico;
            $obj->localizacao_diferenciada = $this->localizacao_diferenciada;
            $obj->educacao_indigena = $this->educacao_indigena;
            $obj->lingua_ministrada = $this->lingua_ministrada;
            $obj->codigo_lingua_indigena = $this->codigo_lingua_indigena;
            $obj->espaco_brasil_aprendizado = $this->espaco_brasil_aprendizado;
            $obj->abre_final_semana = $this->abre_final_semana;
            $obj->codigo_lingua_indigena = $codigo_lingua_indigena;
            $obj->proposta_pedagogica = $this->proposta_pedagogica;
            $obj->equipamentos = $equipamentos;
            $obj->uso_internet = $uso_internet;
            $obj->rede_local = $rede_local;
            $obj->equipamentos_acesso_internet = $equipamentos_acesso_internet;
            $obj->quantidade_computadores_alunos_mesa = $this->quantidade_computadores_alunos_mesa;
            $obj->quantidade_computadores_alunos_portateis = $this->quantidade_computadores_alunos_portateis;
            $obj->quantidade_computadores_alunos_tablets = $this->quantidade_computadores_alunos_tablets;
            $obj->lousas_digitais = $this->lousas_digitais;
            $obj->televisoes = $this->televisoes;
            $obj->dvds = $this->dvds;
            $obj->aparelhos_de_som = $this->aparelhos_de_som;
            $obj->projetores_digitais = $this->projetores_digitais;
            $obj->acesso_internet = $this->acesso_internet;
            $obj->ato_criacao = $this->ato_criacao;
            $obj->ato_autorizativo = $this->ato_autorizativo;
            $obj->ref_idpes_secretario_escolar = $this->secretario_id;
            $obj->unidade_vinculada_outra_instituicao = $this->unidade_vinculada_outra_instituicao;
            $obj->inep_escola_sede = $this->inep_escola_sede;
            $obj->codigo_ies = $this->codigo_ies_id;
            $obj->categoria_escola_privada = $this->categoria_escola_privada;
            $obj->conveniada_com_poder_publico = $this->conveniada_com_poder_publico;
            $obj->mantenedora_escola_privada = $mantenedora_escola_privada;
            $obj->cnpj_mantenedora_principal = idFederal2int($this->cnpj_mantenedora_principal);
            $obj->esfera_administrativa = $this->esfera_administrativa;
            $obj->iddis = (int)$this->district_id;
            foreach ($this->inputsRecursos as $key => $value) {
                $obj->{$key} = $this->{$key};
            }

            $this->cod_escola = $editou = $obj->cadastra();

            if ($this->cod_escola) {
                $obj = new clsPmieducarEscola($this->cod_escola);
                $escolaDetAtual = $obj->detalhe();
                $auditoria = new clsModulesAuditoriaGeral("escola", $this->pessoa_logada, $this->cod_escola);
                $auditoria->inclusao($escolaDetAtual);
            }
        }

        if ($editou) {
            if ($this->com_cnpj) {
                $objPessoa = new clsPessoa_($this->ref_idpes, null, false, $this->p_http, false, $this->pessoa_logada, date("Y-m-d H:i:s", time()), $this->p_email);
                $editou1 = $objPessoa->edita();

                if ($editou1) {
                    $obj_pes_juridica = new clsJuridica($this->ref_idpes, $this->cnpj, $this->fantasia, false, false, false, $this->pessoa_logada);
                    $editou2 = $obj_pes_juridica->edita();

                    if ($editou2) {
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes);
                        $objTelefone->excluiTodos();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 1, str_replace("-", "", $this->p_telefone_1), $this->p_ddd_telefone_1);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 2, str_replace("-", "", $this->p_telefone_2), $this->p_ddd_telefone_2);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 3, str_replace("-", "", $this->p_telefone_mov), $this->p_ddd_telefone_mov);
                        $objTelefone->cadastra();
                        $objTelefone = new clsPessoaTelefone($this->ref_idpes, 4, str_replace("-", "", $this->p_telefone_fax), $this->p_ddd_telefone_fax);
                        $objTelefone->cadastra();

                        $this->saveAddress($this->ref_idpes);

                        //-----------------------EDITA CURSO------------------------//
                        $this->escola_curso = unserialize(urldecode($this->escola_curso));
                        $this->escola_curso_autorizacao = unserialize(urldecode($this->escola_curso_autorizacao));
                        $this->escola_curso_anos_letivos = unserialize(urldecode($this->escola_curso_anos_letivos));
                        $obj = new clsPmieducarEscolaCurso($this->cod_escola);
                        $excluiu = $obj->excluirTodos();

                        if ($excluiu) {
                            if ($this->escola_curso) {
                                foreach ($this->escola_curso as $campo) {
                                    $obj = new clsPmieducarEscolaCurso($this->cod_escola, $campo, null, $this->pessoa_logada, null, null, 1, $this->escola_curso_autorizacao[$campo], $this->escola_curso_anos_letivos[$campo]);
                                    $cadastrou_ = $obj->cadastra();

                                    if (!$cadastrou_) {
                                        $this->mensagem = "Edição não realizada.<br>";

                                        return false;
                                    }
                                }
                            }
                        }

                        $this->storeManagers($this->cod_escola);

                        $this->saveInep($this->cod_escola);
                        //-----------------------FIM EDITA CURSO------------------------//
                        $this->mensagem .= "Edição efetuada com sucesso.<br>";

                        throw new HttpResponseException(
                            new RedirectResponse('educar_escola_lst.php')
                        );
                    }
                }
            } elseif ($this->sem_cnpj) {
                    //-----------------------EDITA CURSO------------------------//
                    $this->escola_curso = unserialize(urldecode($this->escola_curso));
                    $this->escola_curso_autorizacao = unserialize(urldecode($this->escola_curso_autorizacao));
                    $this->escola_curso_anos_letivos = unserialize(urldecode($this->escola_curso_anos_letivos));
                    $obj = new clsPmieducarEscolaCurso($this->cod_escola);
                    $excluiu = $obj->excluirTodos();

                    if ($excluiu) {
                        if ($this->escola_curso) {
                            foreach ($this->escola_curso as $campo) {
                                $obj = new clsPmieducarEscolaCurso($this->cod_escola, $campo, null, $this->pessoa_logada, null, null, 1, $this->escola_curso_autorizacao[$campo], $this->escola_curso_anos_letivos[$campo]);
                                $cadastrou_ = $obj->cadastra();
                                if (!$cadastrou_) {
                                    $this->mensagem = "Edição não realizada.<br>";

                                    return false;
                                }
                            }
                        }
                    }

                    $this->storeManagers($this->cod_escola);

                    $this->saveInep($this->cod_escola);
                    //-----------------------FIM EDITA CURSO------------------------//
                    $this->mensagem .= "Edição efetuada com sucesso.<br>";

                    throw new HttpResponseException(
                        new RedirectResponse('educar_escola_lst.php')
                    );
            }
        }

        $this->mensagem = "Edição não realizada.<br>";

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 3, "educar_escola_lst.php");
        $obj = new clsPmieducarEscola($this->cod_escola, null, $this->pessoa_logada, null, null, null, null, null, null, null, 0);
        $escola = $obj->detalhe();
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $auditoria = new clsModulesAuditoriaGeral("escola", $this->pessoa_logada, $this->cod_escola);
            $auditoria->exclusao($escola);
            $this->mensagem .= "Exclusão efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_escola_lst.php')
            );
        }

        $this->mensagem = "Exclusão não realizada.<br>";

        return false;
    }
    protected function inputTelefone($type, $typeLabel = '')
    {
        if (!$typeLabel) {
            $typeLabel = "Telefone {$type}";
        }

        // ddd
        $options = array(
            'required' => false,
            'label' => "(DDD) / {$typeLabel}",
            'placeholder' => 'DDD',
            'value' => $this->{"p_ddd_telefone_{$type}"},
            'max_length' => 3,
            'size' => 3,
            'inline' => true,
        );
        $this->inputsHelper()->integer("p_ddd_telefone_{$type}", $options);

        // telefone
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"p_telefone_{$type}"},
            'max_length' => 9,
        );
        $this->inputsHelper()->integer("p_telefone_{$type}", $options);
    }

    protected function validaCamposCenso()
    {
        if (!$this->validarCamposObrigatoriosCenso()) {
            return true;
        }
        return $this->validaEscolaPrivada() &&
                $this->validaOcupacaoPredio() &&
                $this->validaLocalizacaoDiferenciada() &&
                $this->validaEsferaAdministrativa() &&
                $this->validaDigitosInepEscola($this->inep_escola_sede, 'Código escola sede') &&
                $this->inepEscolaSedeDiferenteDaEscolaPrincipal() &&
                $this->validateCensusManagerRules() &&
                $this->validaEscolaCompartilhaPredio() &&
                $this->validaSalasUtilizadasDentroEscola() &&
                $this->validaSalasUtilizadasForaEscola() &&
                $this->validaSalasClimatizadas() &&
                $this->validaSalasAcessibilidade() &&
                $this->validaEquipamentosAcessoInternet() &&
                $this->validaRecursos() &&
                $this->validaQuantidadeComputadoresAlunos() &&
                $this->validaQuantidadeEquipamentosEnsino() &&
                $this->validaLinguasIndigenas();
    }

    protected function validaOcupacaoPredio()
    {
        if (in_array(LocalFuncionamento::PREDIO_ESCOLAR, $this->local_funcionamento) && empty($this->condicao)) {
            $this->mensagem = 'O campo: Forma de ocupação do prédio, deve ser informado quando o Local de funcionamento for prédio escolar.';
            return FALSE;
        }
        return TRUE;
    }

    protected function validaLocalizacaoDiferenciada()
    {
        if ($this->localizacao_diferenciada == LocalizacaoDiferenciadaEscola::AREA_ASSENTAMENTO &&
            $this->zona_localizacao == App_Model_ZonaLocalizacao::URBANA) {
            $this->mensagem = 'O campo: Localização diferenciada da escola não pode ser preenchido com Área de assentamento quando o campo: Zona localização for Urbana';
            return false;
        }

        return true;
    }

    protected function validaEsferaAdministrativa()
    {
        if ($this->regulamentacao == Regulamentacao::NAO) {
            return true;
        }

        $esferaAdministrativa = $this->esfera_administrativa;
        $dependenciaAdministrativa = $this->dependencia_administrativa;
        $mensagem = 'O campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização, foi preenchido com um valor incorreto';

        if ($this->regulamentacao != Regulamentacao::NAO && empty($esferaAdministrativa)) {
            $this->mensagem = $mensagem;
            return false;
        }

        /**
         * Se o campo "dependência administrativa" for:
         * 2 (Estadual) este campo também deve ser 2
         */
        if ($dependenciaAdministrativa == DependenciaAdministrativaEscola::ESTADUAL) {
            if ($esferaAdministrativa != EsferaAdministrativa::ESTADUAL) {
                $this->mensagem = $mensagem;
                return false;
            }
        }
        /**
         * Se o campo "dependência administrativa" for:
         * 1 (Federal) este campo deve ser 1 ou 2
         */
        if ($dependenciaAdministrativa == DependenciaAdministrativaEscola::FEDERAL) {
            if (
                $esferaAdministrativa != EsferaAdministrativa::FEDERAL &&
                $esferaAdministrativa != EsferaAdministrativa::ESTADUAL
            ) {
                $this->mensagem = $mensagem;
                return false;
            }

        }
        /**
         * Se o campo "dependência administrativa" for:
         * 3 (Municipal) este campo deve ser 2 ou 3
         */
        if ($dependenciaAdministrativa == DependenciaAdministrativaEscola::MUNICIPAL) {
            if (
                $esferaAdministrativa != EsferaAdministrativa::ESTADUAL &&
                $esferaAdministrativa != EsferaAdministrativa::MUNICIPAL
            ) {
                $this->mensagem = $mensagem;
                return false;
            }
        }

        return true;
    }

    protected function validaEscolaPrivada()
    {
        if ($this->dependencia_administrativa != "4" || $this->situacao_funcionamento != 1) {
            return TRUE;
        }
        if (empty($this->categoria_escola_privada)) {
            $this->mensagem = "O campo categoria da escola privada é obrigatório para escolas em atividade de administração privada.";
            return FALSE;
        }
        if (empty($this->conveniada_com_poder_publico)) {
            $this->mensagem = "O campo conveniada com poder público é obrigatório para escolas em atividade de administração privada.";
            return FALSE;
        }
        if (empty($this->mantenedora_escola_privada) ||
            (is_array($this->mantenedora_escola_privada) &&
            count($this->mantenedora_escola_privada) == 1 &&
            empty($this->mantenedora_escola_privada[0]))) {
            $this->mensagem = "O campo mantenedora da escola privada é obrigatório para escolas em atividade de administração privada.";
            return FALSE;
        }
        return TRUE;
    }

    protected function validaDadosTelefones()
    {
        return $this->validaDDDTelefone($this->p_ddd_telefone_1, $this->p_telefone_1, 'Telefone 1') &&
            $this->validaTelefone($this->p_telefone_1, 'Telefone 1') &&
            $this->validaDDDTelefone($this->p_ddd_telefone_2, $this->p_telefone_2, 'Telefone 2') &&
            $this->validaTelefone($this->p_telefone_2, 'Telefone 2') &&
            $this->validaDDDTelefone($this->p_ddd_telefone_mov, $this->p_telefone_mov, 'Celular') &&
            $this->validaDDDTelefone($this->p_ddd_telefone_fax, $this->p_telefone_fax, 'Fax') &&
            $this->validaTelefones($this->p_telefone_1, $this->p_telefone_2);
    }

    protected function validaTelefones($telefone1, $telefone2)
    {
        if (empty($telefone1) && empty($telefone2)) {
            return true;
        }

        if ($telefone1 == $telefone2) {
            $this->mensagem = 'O campo: Telefone 2 não pode ter o mesmo valor do campo: Telefone 1';
            return false;
        }

        return true;
    }

    protected function validaDDDTelefone($valorDDD = null, $valorTelefone = null, $nomeCampo)
    {
        $msgRequereTelefone = "O campo: {$nomeCampo}, deve ser preenchido quando o DDD estiver preenchido.";
        $msgRequereDDD = "O campo: DDD, deve ser preenchido quando o {$nomeCampo} estiver preenchido.";

        if (!empty($valorDDD) && empty($valorTelefone)) {
            $this->mensagem = $msgRequereTelefone;
            return false;
        }

        if (empty($valorDDD) && !empty($valorTelefone)) {
            $this->mensagem = $msgRequereDDD;
            return false;
        }

        return true;
    }

    protected function validaTelefone($telefone, $nomeCampo)
    {
        if (empty($telefone)) {
            return true;
        }

        $telefoneValidator = new Telefone($nomeCampo, $telefone);
        if (!$telefoneValidator->isValid()) {
            $this->mensagem = implode('<br>', $telefoneValidator->getMessage());
            return false;
        }

        return true;
    }

    protected function validaDigitosInepEscola($inep, $nomeCampo)
    {
        if (!empty($inep) && strlen($inep) != 8) {
            $this->mensagem = "O campo: {$nomeCampo} deve conter 8 dígitos.";
            return false;
        }
        return true;
    }

    protected function inepEscolaSedeDiferenteDaEscolaPrincipal()
    {
        if ($this->inep_escola_sede == $this->escola_inep_id) {
            $this->mensagem = "O campo: Código da escola sede deve ser diferente do campo: Código INEP";
            return false;
        }

        return true;
    }

    protected function geraCamposCodigoInepEscolaCompartilhada()
    {
        $options = array('label_hint' => 'Caso compartilhe o prédio escolar com outra escola preencha com o código INEP',
                        'required' => false, 'size' => 8, 'max_length' => 8, 'placeholder' => '');

        for ( $i = 1; $i <= 6; $i++){
            $seq = $i == 1 ? '' : $i;
            $options['label'] = 'Código da escola que compartilha o prédio '.$i;
            $campo = 'codigo_inep_escola_compartilhada'.$seq;
            $options['value'] = $this->$campo;
            $this->inputsHelper()->integer('codigo_inep_escola_compartilhada'.$seq, $options);
        }

    }


    /**
     * Cria tabela dinâmica com gestores da escola
     */
    protected function addSchoolManagersTable()
    {
        /** @var SchoolManagerService $schoolService */
        $schoolService = app(SchoolManagerService::class);
        $managers = $schoolService->getSchoolManagers($this->cod_escola ?: 0);

        if (old('servidor_id')) {
            foreach (old('servidor_id') as $key => $value) {
                $rows[] = [
                    old('managers_inep_id')[$key],
                    old('managers_individual_nome')[$key],
                    old('managers_role_id')[$key],
                    null,
                    old('managers_chief')[$key],
                    old('servidor_id')[$key],
                    old('managers_access_criteria_id')[$key],
                    old('managers_link_type_id')[$key],
                    old('managers_email')[$key],
                ];
            }
        } else {
            $rows = [];
            foreach ($managers as $key => $manager) {
                $rows[] = $this->makeRowManagerTable($key, $manager);
            }
        }

        $this->campoTabelaInicio('gestores', 'Gestores escolares',
            [
                'INEP',
                'Nome do(a) gestor(a)',
                'Cargo do(a) gestor(a)',
                'Detalhes',
                'Principal',
            ],
            $rows
        );

        $this->campoTexto('managers_inep_id', null, null, null, 12);

        $this->inputsHelper()->simpleSearchServidor(null, ['required' => false]);
        $options = [
            'resources' => SelectOptions::schoolManagerRoles(),
            'required' => false,
        ];
        $this->inputsHelper()->select('managers_role_id', $options);
        $this->campoRotulo('detalhes', 'Detalhes', '<a class="btn-detalhes" onclick="modalOpen(this)">Dados adicionais do(a) gestor(a)</a>');
        $this->campoOculto('managers_access_criteria_id', null);
        $this->campoOculto('managers_link_type_id', null);
        $this->campoOculto('managers_email', null);

        $resources = [
                0 => 'Não',
                1 => 'Sim',
            ];
        $options =
            [
                'resources' => $resources,
                'required' => false
            ];
        $this->inputsHelper()->select('managers_chief', $options);

        $this->campoTabelaFim();
    }

    /**
     * @param SchoolManager $schoolManager
     * @return array
     */
    protected function makeRowManagerTable($key, $schoolManager)
    {
        return [
            $this->managers_inep_id[$key] ?? $schoolManager->employee->inep->number,
            $this->managers_individual_nome[$key] ?? $schoolManager->individual->real_name,
            $this->managers_role_id[$key] ?? $schoolManager->role_id,
            null,
            $this->managers_chief[$key] ?? (int)$schoolManager->chief,
            $this->servidor_id[$key] ?? $schoolManager->employee_id,
            $this->managers_access_criteria_id[$key] ?? $schoolManager->access_criteria_id,
            $this->managers_link_type_id[$key] ?? $schoolManager->link_type_id,
            $this->managers_email[$key] ?? $schoolManager->individual->person->email,
        ];
    }

    /**
     * Salva os gestores da escola
     * @param $schoolId
     */
    protected function storeManagers($schoolId)
    {
        /** @var SchoolManagerService $schoolService */
        $schoolService = app(SchoolManagerService::class);
        $schoolService->deleteAllManagers($schoolId);
        foreach($this->servidor_id as $key => $employeeId) {
            if (empty($employeeId)) {
                continue;
            }

            $valueObject = new SchoolManagerValueObject();
            $valueObject->employeeId = $employeeId;
            $valueObject->schoolId = $schoolId;
            $valueObject->roleId = $this->managers_role_id[$key] ?: null;
            $valueObject->accessCriteriaId = $this->managers_access_criteria_id[$key] ?: null;
            $valueObject->linkTypeId = $this->managers_link_type_id[$key] ?: null;
            $valueObject->isChief = $this->managers_chief[$key];
            $schoolService->storeManager($valueObject);

            if ($this->managers_email[$key]){
                $this->storeManagerEmail($employeeId, $this->managers_email[$key]);
            }

            if ($this->managers_inep_id[$key]) {
                $this->storeInepCode($employeeId, $this->managers_inep_id[$key]);
            }
        }
    }

    protected function storeManagerEmail($employeeId, $email)
    {
        $person = LegacyPerson::find($employeeId);
        $person->email = $email;
        $person->save();
    }

    protected function storeInepCode($employeeId, $inepCode)
    {
        $employeeInep = EmployeeInep::firstOrNew(
            ['cod_servidor' => $employeeId]
        );
        $employeeInep->cod_docente_inep = $inepCode;
        $employeeInep->save();
    }

    /**
     * Valida as regras gerais dos gestores da escola
     */
    protected function validateManagersRules()
    {
        request()->validate(
            [
                'servidor_id' => ['max:3', new SchoolManagerUniqueIndividuals()],
                'managers_chief' => new SchoolManagerAtLeastOneChief(),
                'managers_inep_id.*' => 'nullable|size:12',
            ],
            [
                'servidor_id.max' => 'Informe no máximo 3 Gestores escolares',
                'managers_inep_id.*.size' => 'O campo: Código INEP do gestor(a) deve conter 12 dígitos'
            ]);
    }

    /**
     * Valida as regras do censo referentes aos gestores da escola
     * @return bool
     */
    protected function validateCensusManagerRules()
    {
        $managers = [];
        foreach ($this->servidor_id as $key => $value) {
            $valueObject = new SchoolManagerValueObject();
            $valueObject->employeeId = $this->servidor_id[$key];
            $valueObject->inepId = $this->managers_inep_id[$key];
            $valueObject->roleId = $this->managers_role_id[$key];
            $valueObject->accessCriteriaId = $this->managers_access_criteria_id[$key];
            $valueObject->linkTypeId = $this->managers_link_type_id[$key];
            $valueObject->isChief = $this->managers_chief[$key];
            $managers[] = $valueObject;
        }

        $managersValidator = new SchoolManagers($managers, $this->dependencia_administrativa, $this->situacao_funcionamento);

        if (!$managersValidator->isValid()) {
            $this->mensagem = implode('<br>', $managersValidator->getMessage());
            return false;
        }

        return true;
    }

    protected function validaEscolaCompartilhaPredio()
    {
        $arrayCampos = [
            $this->codigo_inep_escola_compartilhada,
            $this->codigo_inep_escola_compartilhada2,
            $this->codigo_inep_escola_compartilhada3,
            $this->codigo_inep_escola_compartilhada4,
            $this->codigo_inep_escola_compartilhada5,
            $this->codigo_inep_escola_compartilhada6,
        ];

        if (in_array($this->escola_inep_id, $arrayCampos)) {
            $this->mensagem = "O campo: Código da escola que compartilha o prédio 1, 2, 3, 4, 5 ou 6, deve ser diferente do Código INEP da escola atual.";
            return false;
        }

        $arrayCamposSemNulos = array_filter($arrayCampos);
        if (count(array_unique($arrayCamposSemNulos)) < count($arrayCamposSemNulos)) {
            $this->mensagem = "Os códigos Inep's das escolas compartilhadas devem ser diferentes entre si.";
            return false;
        }

        return true;
    }

    protected function validaCampoPossuiDependencias()
    {
        if ($this->possui_dependencias != 1) {
            return true;
        }

        $arrayCampos = array_filter(
            [
                $this->salas_gerais,
                $this->salas_funcionais,
                $this->banheiros,
                $this->laboratorios,
                $this->salas_atividades,
                $this->dormitorios,
                $this->areas_externas,
            ]
        );

        if (count($arrayCampos) == 0) {
            $this->mensagem = 'Preencha pelo menos um dos campos de Salas gerais à Áreas externas';
            return false;
        }

        return true;
    }

    protected function validaSalasUtilizadasDentroEscola()
    {
        if ($this->numero_salas_utilizadas_dentro_predio == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->local_funcionamento != LocalFuncionamento::PREDIO_ESCOLAR) {
            return true;
        }

        if ((int)$this->numero_salas_utilizadas_fora_predio <= 0 && (int)$this->numero_salas_utilizadas_dentro_predio <= 0) {
            $this->mensagem = 'O campo: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> deve ser preenchido quando o campo: <b>Local de funcionamento</b> for: <b>Prédio escolar</b> e o campo: <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b> não for preenchido';
            return false;
        }

        return true;
    }

    protected function validaSalasUtilizadasForaEscola()
    {
        if ($this->numero_salas_utilizadas_fora_predio == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b> não pode ser preenchido com 0';
            return false;
        }

        if ((int)$this->numero_salas_utilizadas_fora_predio <= 0 && (int)$this->numero_salas_utilizadas_dentro_predio <= 0) {
            $this->mensagem = 'O campo: <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b> deve ser preenchido quando o campo: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> não for preenchido';
            return false;
        }

        return true;
    }

    protected function validaSalasClimatizadas()
    {
        if ($this->numero_salas_climatizadas == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula climatizadas</b> não pode ser preenchido com 0';
            return false;
        }

        $totalSalas = (int)$this->numero_salas_utilizadas_dentro_predio + (int)$this->numero_salas_utilizadas_fora_predio;
        if ((int)$this->numero_salas_climatizadas > $totalSalas) {
            $this->mensagem = 'O campo: <b>Número de salas de aula climatizadas</b> não pode ser maior que a soma dos campos: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> e <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b>';
            return false;
        }

        return true;
    }

    protected function validaSalasAcessibilidade()
    {
        if ($this->numero_salas_acessibilidade == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser preenchido com 0';
            return false;
        }

        $totalSalas = (int)$this->numero_salas_utilizadas_dentro_predio + (int)$this->numero_salas_utilizadas_fora_predio;
        if ((int)$this->numero_salas_acessibilidade > $totalSalas) {
            $this->mensagem = 'O campo: <b>Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser maior que a soma dos campos: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> e <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b>';
            return false;
        }

        return true;
    }

    protected function validaOpcoesUnicasMultipleSearch()
    {
        if (in_array(5, $this->abastecimento_agua) && count($this->abastecimento_agua) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Abastecimento de água</b>, quando a opção: <b>Não há abastecimento de água</b> estiver selecionada.';
            return false;
        }

        if (in_array(4, $this->abastecimento_energia) && count($this->abastecimento_energia) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Fonte de energia elétrica</b>, quando a opção: <b>Não há energia elétrica</b> estiver selecionada.';
            return false;
        }

        if (in_array(3, $this->esgoto_sanitario) && count($this->esgoto_sanitario) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Esgotamento sanitário</b>, quando a opção: <b>Não há esgotamento sanitário</b> estiver selecionada.';
            return false;
        }

        if (in_array(TratamentoLixo::NAO_FAZ, $this->tratamento_lixo) && count($this->tratamento_lixo) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Tratamento do lixo/resíduos que a escola realiza</b>, quando a opção: <b>Não faz tratamento</b> estiver selecionada';
            return false;
        }

        if (in_array(RecursosAcessibilidade::NENHUM, $this->recursos_acessibilidade) && count($this->recursos_acessibilidade) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Recursos de acessibilidade</b>, quando a opção: <b>Nenhum dos recursos de acessibilidade</b> estiver selecionada.';
            return false;
        }

        if (in_array(UsoInternet::NAO_POSSUI, $this->uso_internet) && count($this->uso_internet) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Acesso à internet</b>, quando a opção: <b>Não possui acesso à internet</b> estiver selecionada.';
            return false;
        }
        if (in_array(5, $this->abastecimento_agua) && count($this->abastecimento_agua) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Abastecimento de água</b>, quando a opção: <b>Não há abastecimento de água</b> estiver selecionada.';
            return false;
        }

        if (in_array(4, $this->abastecimento_energia) && count($this->abastecimento_energia) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Fonte de energia elétrica</b>, quando a opção: <b>Não há energia elétrica</b> estiver selecionada.';
            return false;
        }

        if (in_array(3, $this->esgoto_sanitario) && count($this->esgoto_sanitario) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Esgotamento sanitário</b>, quando a opção: <b>Não há esgotamento sanitário</b> estiver selecionada.';
            return false;
        }

        if (in_array(TratamentoLixo::NAO_FAZ, $this->tratamento_lixo) && count($this->tratamento_lixo) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Tratamento do lixo/resíduos que a escola realiza</b>, quando a opção: <b>Não faz tratamento</b> estiver selecionada';
            return false;
        }

        if (in_array(RecursosAcessibilidade::NENHUM, $this->recursos_acessibilidade) && count($this->recursos_acessibilidade) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Recursos de acessibilidade</b>, quando a opção: <b>Nenhum dos recursos de acessibilidade</b> estiver selecionada.';
            return false;
        }

        if (in_array(UsoInternet::NAO_POSSUI, $this->uso_internet) && count($this->uso_internet) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Acesso à internet</b>, quando a opção: <b>Não possui acesso à internet</b> estiver selecionada.';
            return false;
        }

        if (in_array(RedeLocal::NENHUMA, $this->rede_local) && count($this->rede_local) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Rede local de interligação de computadores</b>, quando a opção: <b>Não há rede local interligando computadores</b> estiver selecionada.';
            return false;
        }

        if (in_array(OrgaosColegiados::NENHUM, $this->orgaos_colegiados) && count($this->orgaos_colegiados) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Órgãos colegiados em funcionamento na escola</b>, quando a opção: <b>Não há órgãos colegiados em funcionamento</b> estiver selecionada.';
            return false;
        }

        if (in_array(ReservaVagasCotas::NAO_POSSUI, $this->reserva_vagas_cotas) && count($this->reserva_vagas_cotas) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Reserva de vagas por sistema de cotas para grupos específicos de alunos(as)</b>, quando a opção: <b>Sem reservas de vagas para sistema de cotas (ampla concorrência)</b> estiver selecionada.';
            return false;
        }

        return true;
    }

    protected function validaEquipamentosAcessoInternet()
    {
        if(in_array(2, $this->equipamentos_acesso_internet) && !in_array(3, $this->rede_local)) {
            $this->mensagem = "O campo: <b>Equipamentos que os aluno(a)s usam para acessar a internet da escola</b> não deve ser preenchido com a opção: <b>Dispositivos pessoais (computadores portáteis, celulares, tablets, etc.)</b> quando o campo: <b>Rede local de interligação de computadores</b> não possuir a opção: <b>Wireless</b> selecionada.";
            return false;
        }

        return true;
    }

    protected function validaRecursos()
    {
        $algumCampoPreenchido = false;
        foreach ($this->inputsRecursos as $key => $label) {
            if ($this->{$key} == '0') {
                $this->mensagem = "O campo: <b>{$label}</b> não pode ser preenchido com 0";
                return false;
            } elseif ((int) $this->{$key} > 0) {
                $algumCampoPreenchido = true;
            }
        }

        if ($algumCampoPreenchido) {
            return true;
        }

        $this->mensagem = 'Preencha pelo menos um dos campos <b>da seção</b> Quantidade de profissionais da aba Recursos.';
        return false;
    }

    protected function validaQuantidadeComputadoresAlunos()
    {
        $quantidadesNaoPreenchidas = (
            $this->quantidade_computadores_alunos_mesa == '' &&
            $this->quantidade_computadores_alunos_portateis == '' &&
            $this->quantidade_computadores_alunos_tablets == ''
        );

        if ($this->quantidade_computadores_alunos_mesa == '0') {
            $this->mensagem = 'O campo: <b>Computadores de mesa</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->quantidade_computadores_alunos_portateis == '0') {
            $this->mensagem = 'O campo: <b>Computadores portáteis</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->quantidade_computadores_alunos_tablets == '0') {
            $this->mensagem = 'O campo: <b>Tablets</b> não pode ser preenchido com 0';
            return false;
        }

        if (in_array(EquipamentosAcessoInternet::COMPUTADOR_MESA, $this->equipamentos_acesso_internet) && $quantidadesNaoPreenchidas) {
            $this->mensagem = 'Preencha pelo menos um dos campos da seção <b>Quantidade de computadores de uso dos alunos</b> quando o campo <b>Equipamentos que os aluno(a)s usam para acessar a internet da escola</b> for preenchido com <b>Computadores de mesa, portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula, etc.)</b>.';
            return false;
        }

        return true;
    }


    protected function validaQuantidadeEquipamentosEnsino()
    {
        if ($this->televisoes == '0') {
            $this->mensagem = 'O campo: <b>Aparelho de Televisão</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->dvds == '0') {
            $this->mensagem = 'O campo: <b>Aparelho de DVD/Blu-ray</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->aparelhos_de_som == '0') {
            $this->mensagem = 'O campo: <b>Aparelho de som</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->projetores_digitais == '0') {
            $this->mensagem = 'O campo: <b>Projetor Multimídia (Data show)</b> não pode ser preenchido com 0';
            return false;
        }

        if ($this->lousas_digitais == '0') {
            $this->mensagem = 'O campo: <b>Lousa digital</b> não pode ser preenchido com 0';
            return false;
        }

        return true;
    }

    private function saveInep($schoolId)
    {
        DB::table('modules.educacenso_cod_escola')->where('cod_escola', $schoolId)
            ->delete();
        if (!empty($this->escola_inep_id)) {
            $data = [
                'cod_escola' => $schoolId,
                'cod_escola_inep' => $this->escola_inep_id,
                'fonte' => 'fonte',
                'nome_inep' => '-',
                'created_at' => 'NOW()',
            ];

            DB::table('modules.educacenso_cod_escola')->insert($data);
        }
    }

    protected function validaLinguasIndigenas()
    {
        if (count($this->codigo_lingua_indigena) > 3) {
            $this->mensagem = 'O campo: <b>Línguas indígenas</b>, não pode ter mais que 3 opções';
            return false;
        }

        return true;
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
