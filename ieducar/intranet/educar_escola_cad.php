<?php

use App\Models\City;
use App\Models\EmployeeInep;
use App\Models\LegacyPerson;
use App\Models\SchoolManager;
use App\Models\SchoolSpace;
use App\Rules\SchoolManagerAtLeastOneChief;
use App\Rules\SchoolManagerUniqueIndividuals;
use App\Services\SchoolManagerService;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use iEducar\Modules\Educacenso\Model\AbastecimentoAgua;
use iEducar\Modules\Educacenso\Model\AcoesAmbientais;
use iEducar\Modules\Educacenso\Model\AreasExternas;
use iEducar\Modules\Educacenso\Model\Banheiros;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\Dormitorios;
use iEducar\Modules\Educacenso\Model\Equipamentos;
use iEducar\Modules\Educacenso\Model\EquipamentosAcessoInternet;
use iEducar\Modules\Educacenso\Model\EsgotamentoSanitario;
use iEducar\Modules\Educacenso\Model\FonteEnergia;
use iEducar\Modules\Educacenso\Model\InstrumentosPedagogicos;
use iEducar\Modules\Educacenso\Model\Laboratorios;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaEscola;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\OrgaosColegiados;
use iEducar\Modules\Educacenso\Model\OrgaoVinculadoEscola;
use iEducar\Modules\Educacenso\Model\PoderPublicoConveniado;
use iEducar\Modules\Educacenso\Model\RecursosAcessibilidade;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use iEducar\Modules\Educacenso\Model\ReservaVagasCotas;
use iEducar\Modules\Educacenso\Model\SalasAtividades;
use iEducar\Modules\Educacenso\Model\SalasFuncionais;
use iEducar\Modules\Educacenso\Model\SalasGerais;
use iEducar\Modules\Educacenso\Model\TratamentoLixo;
use iEducar\Modules\Educacenso\Model\UsoInternet;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;
use iEducar\Modules\Educacenso\Validator\School\HasDifferentStepsOfChildEducationValidator;
use iEducar\Modules\Educacenso\Validator\SchoolManagers;
use iEducar\Modules\Educacenso\Validator\Telefone;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;
use iEducar\Support\View\SelectOptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
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

    public $acao_area_ambiental;

    public $acoes_area_ambiental;

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

    public $qtd_agronomos_horticultores;

    public $qtd_nutricionistas;

    public $qtd_profissionais_preparacao;

    public $qtd_bombeiro;

    public $qtd_psicologo;

    public $qtd_fonoaudiologo;

    public $qtd_vice_diretor;

    public $qtd_orientador_comunitario;

    public $qtd_tradutor_interprete_libras_outro_ambiente;

    public $qtd_revisor_braile;

    public $iddis;

    public $pessoaj_idpes;

    public $pessoaj_id;

    public bool $pesquisaPessoaJuridica = true;

    public $poder_publico_parceria_convenio;

    public $nao_ha_funcionarios_para_funcoes;

    public $formas_contratacao_parceria_escola_secretaria_estadual;

    public $formas_contratacao_parceria_escola_secretaria_municipal;

    public $espaco_escolares;

    public $espaco_escolar_id;

    public $espaco_escolar_nome;

    public $espaco_escolar_tamanho;

    public $inputsRecursos = [
        'qtd_secretario_escolar' => 'Secretário(a) escolar',
        'qtd_auxiliar_administrativo' => 'Auxiliares de secretaria ou auxiliares administrativos, atendentes',
        'qtd_apoio_pedagogico' => 'Profissionais de apoio e supervisão pedagógica: pedagogo(a), coordenador(a) pedagógico(a), orientador(a) educacional, supervisor(a) escolar e coordenador(a) de área de ensino',
        'qtd_coordenador_turno' => 'Coordenador(a) de turno/disciplina',
        'qtd_tecnicos' => 'Técnicos(as), monitores(as), supervisores(as) ou auxiliares de laboratório(s), de apoio a tecnologias educacionais ou em multimeios/multimídias eletrônico-digitais',
        'qtd_bibliotecarios' => 'Bibliotecário(a), auxiliar de biblioteca ou monitor(a) da sala de leitura',
        'qtd_segurancas' => 'Seguranças, guarda ou segurança patrimonial',
        'qtd_auxiliar_servicos_gerais' => 'Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), jardineiro(a)',
        'qtd_agronomos_horticultores' => 'Agrônomos(as), horticultores(as), técnicos ou monitores(as) responsáveis pela gestão da área de horta, plantio e/ou produção agrícola',
        'qtd_nutricionistas' => 'Nutricionista',
        'qtd_profissionais_preparacao' => 'Profissionais de preparação e segurança alimentar, cozinheiro(a), merendeira e auxiliar de cozinha',
        'qtd_bombeiro' => 'Bombeiro(a) brigadista, profissionais de assistência a saúde (urgência e emergência), Enfermeiro(a), Técnico(a) de enfermagem e socorrista',
        'qtd_psicologo' => 'Psicólogo(a) Escolar',
        'qtd_fonoaudiologo' => 'Fonoaudiólogo(a)',
        'qtd_vice_diretor' => 'Vice-diretor(a) ou diretor(a) adjunto(a), profissionais responsáveis pela gestão administrativa e/ou financeira',
        'qtd_orientador_comunitario' => 'Orientador(a) comunitário(a) ou assistente social',
        'qtd_tradutor_interprete_libras_outro_ambiente' => 'Tradutor e Intérprete de Libras para atendimento em outros ambientes da escola que não seja sala de aula',
        'qtd_revisor_braile' => 'Revisor de texto Braille, assistente vidente (assistente de revisão do texto em Braille)',
    ];

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_escola_lst.php');

        $this->cod_escola = $this->getQueryString('cod_escola');

        $this->sem_cnpj = false;
        $this->pesquisaPessoaJuridica = true;
        $this->espaco_escolares = [];

        if (is_numeric($_POST['pessoaj_id']) && !$this->cod_escola) {
            $pessoaJuridicaId = (int) $_POST['pessoaj_id'];
            if (!$this->pessoaJuridicaContemEscola($pessoaJuridicaId)) {
                return false;
            }

            $this->pesquisaPessoaJuridica = false;
            $this->sem_cnpj = true;
            $this->pessoaj_idpes = $pessoaJuridicaId;
            $this->pessoaj_id = $pessoaJuridicaId;
            $this->ref_idpes = $pessoaJuridicaId;

            $this->loadAddress($this->pessoaj_id);
            $this->carregaDadosContato($this->ref_idpes);

            $retorno = 'Novo';
        }

        if (is_numeric($this->cod_escola)) {
            $obj = new clsPmieducarEscola($this->cod_escola);
            $registro = $obj->detalhe();

            if ($registro === false) {
                throw new HttpResponseException(
                    new RedirectResponse('educar_escola_lst.php')
                );
            }

            $this->pesquisaPessoaJuridica = false;

            $this->carregaCamposComDadosDaEscola($registro);

            $objJuridica = (new clsPessoaJuridica($this->ref_idpes))->detalhe();

            if (validaCNPJ($objJuridica['cnpj'])) {
                $this->cnpj = int2CNPJ($objJuridica['cnpj']);
            }

            $this->fexcluir = is_numeric($this->cod_escola) && $obj_permissoes->permissao_excluir(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);

            $this->loadAddress($this->ref_idpes);
            $this->carregaDadosContato($this->ref_idpes);

            $espacoEscolares = SchoolSpace::query()
                ->where('school_id', $this->cod_escola)
                ->orderBy('created_at')
                ->get();

            foreach ($espacoEscolares as $key => $espacoEscolar) {
                $this->espaco_escolares[$key][] = $espacoEscolar->name;
                $this->espaco_escolares[$key][] = $espacoEscolar->size;
                $this->espaco_escolares[$key][] = $espacoEscolar->getKey();
            }

            $retorno = 'Editar';
        }

        $this->url_cancelar = ($retorno == 'Editar') ? "educar_escola_det.php?cod_escola={$registro['cod_escola']}" : 'educar_escola_lst.php';

        $this->breadcrumb(currentPage: 'Escola', breadcrumbs: ['educar_index.php' => 'Escola']);
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    private function carregaCamposComDadosDaEscola($registro)
    {
        $this->pessoaj_id = $registro['ref_idpes'];

        foreach ($registro as $campo => $val) {
            // passa todos os valores obtidos no registro para atributos do objeto
            $this->$campo = $val;
        }

        $this->gestor_id = $registro['ref_idpes_gestor'];
        $this->secretario_id = $registro['ref_idpes_secretario_escolar'];
        $this->fantasia = $registro['nome'];
    }

    private function carregaDadosContato($idpes)
    {
        $objPessoa = new clsPessoaFj($idpes);
        [
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
        ] = $objPessoa->queryRapida(
            $idpes,
            'ddd_1',
            'fone_1',
            'ddd_2',
            'fone_2',
            'ddd_mov',
            'fone_mov',
            'ddd_fax',
            'fone_fax',
            'email',
            'url',
            'tipo'
        );
    }

    private function carregaDadosDoPost()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                if ($campo !== 'tipoacao' && $campo !== 'sem_cnpj') {
                    $this->$campo = ($this->$campo) ?: $val;
                }
            }
        }
    }

    private function inicializaDados()
    {
        if ($this->cnpj_mantenedora_principal) {
            $this->cnpj_mantenedora_principal = int2CNPJ($this->cnpj_mantenedora_principal);
        }

        if (is_string($this->local_funcionamento)) {
            $this->local_funcionamento = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->local_funcionamento));
        }

        if (is_string($this->abastecimento_agua)) {
            $this->abastecimento_agua = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->abastecimento_agua));
        }

        if (is_string($this->abastecimento_energia)) {
            $this->abastecimento_energia = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->abastecimento_energia));
        }

        if (is_string($this->esgoto_sanitario)) {
            $this->esgoto_sanitario = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->esgoto_sanitario));
        }

        if (is_string($this->destinacao_lixo)) {
            $this->destinacao_lixo = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->destinacao_lixo));
        }

        if (is_string($this->tratamento_lixo)) {
            $this->tratamento_lixo = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->tratamento_lixo));
        }

        if (is_string($this->salas_funcionais)) {
            $this->salas_funcionais = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->salas_funcionais));
        }

        if (is_string($this->salas_gerais)) {
            $this->salas_gerais = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->salas_gerais));
        }

        if (is_string($this->banheiros)) {
            $this->banheiros = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->banheiros));
        }

        if (is_string($this->laboratorios)) {
            $this->laboratorios = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->laboratorios));
        }

        if (is_string($this->salas_atividades)) {
            $this->salas_atividades = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->salas_atividades));
        }

        if (is_string($this->dormitorios)) {
            $this->dormitorios = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->dormitorios));
        }

        if (is_string($this->areas_externas)) {
            $this->areas_externas = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->areas_externas));
        }

        if (is_string($this->recursos_acessibilidade)) {
            $this->recursos_acessibilidade = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->recursos_acessibilidade));
        }

        if (is_string($this->mantenedora_escola_privada)) {
            $this->mantenedora_escola_privada = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->mantenedora_escola_privada));
        }

        if (is_string($this->orgao_vinculado_escola)) {
            $this->orgao_vinculado_escola = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->orgao_vinculado_escola));
        }

        if (is_string($this->equipamentos)) {
            $this->equipamentos = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->equipamentos));
        }

        if (is_string($this->uso_internet)) {
            $this->uso_internet = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->uso_internet));
        }

        if (is_string($this->rede_local)) {
            $this->rede_local = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->rede_local));
        }

        if (is_string($this->equipamentos_acesso_internet)) {
            $this->equipamentos_acesso_internet = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->equipamentos_acesso_internet));
        }

        if (is_string($this->organizacao_ensino)) {
            $this->organizacao_ensino = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->organizacao_ensino));
        }

        if (is_string($this->instrumentos_pedagogicos)) {
            $this->instrumentos_pedagogicos = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->instrumentos_pedagogicos));
        }

        if (is_string($this->orgaos_colegiados)) {
            $this->orgaos_colegiados = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->orgaos_colegiados));
        }

        if (is_string($this->reserva_vagas_cotas)) {
            $this->reserva_vagas_cotas = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->reserva_vagas_cotas));
        }

        if (is_string($this->acoes_area_ambiental)) {
            $this->acoes_area_ambiental = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->acoes_area_ambiental));
        }

        if (is_string($this->codigo_lingua_indigena)) {
            $this->codigo_lingua_indigena = explode(separator: ',', string: str_replace(search: ['{', '}'], replace: '', subject: $this->codigo_lingua_indigena));
        }

        $this->poder_publico_parceria_convenio = transformStringFromDBInArray($this->poder_publico_parceria_convenio);
        $this->formas_contratacao_parceria_escola_secretaria_estadual = transformStringFromDBInArray($this->formas_contratacao_parceria_escola_secretaria_estadual);
        $this->formas_contratacao_parceria_escola_secretaria_municipal = transformStringFromDBInArray($this->formas_contratacao_parceria_escola_secretaria_municipal);
    }

    private function pessoaJuridicaContemEscola($pessoaj_id)
    {
        $escola = (new clsPmieducarEscola())->lista(int_ref_idpes: $pessoaj_id);

        if (is_array($escola) && count($escola) > 0) {
            $current = current($escola);

            if (is_array($current) &&
                array_key_exists(key: 'cod_escola', array: $current) &&
                is_numeric($current['cod_escola'])) {
                $this->mensagem = "Escola criada, para<a href=\"educar_escola_cad.php?cod_escola={$current['cod_escola']}\"> editar clique aqui.</a>";

                return false;
            }
        }

        return true;
    }

    public function Gerar()
    {
        $this->inicializaDados();

        // assets
        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/Escola.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/Addresses.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/SchoolManagersModal.js',
        ];
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/Escola.css'];
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();

        $this->campoOculto(nome: 'obrigar_campos_censo', valor: (int) $obrigarCamposCenso);
        $this->campoOculto(nome: 'pessoaj_id_oculto', valor: $this->pessoaj_id);
        $this->campoOculto(nome: 'pessoaj_id', valor: $this->pessoaj_id);

        if ($this->pesquisaPessoaJuridica) {
            $this->inputsHelper()->simpleSearchPessoaj(attrName: 'idpes', inputOptions: ['label' => 'Pessoa Jurídica']);
            $this->acao_enviar = false;
            $this->url_cancelar = false;
            $this->array_botao = ['Continuar', 'Cancelar'];
            $this->array_botao_url_script = ['obj = document.getElementById(\'pessoaj_idpes\');if(obj.value != \'\' ) {
                document.getElementById(\'tipoacao\').value = \'\'; acao(); } else { acao(); }', 'go(\'educar_escola_lst.php\');'];
        } else {
            $obj_permissoes = new clsPermissoes();
            $this->fexcluir = is_numeric($this->cod_escola) && $obj_permissoes->permissao_excluir(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
            $this->inputsHelper()->integer(attrName: 'escola_inep_id', inputOptions: ['label' => 'Código INEP', 'placeholder' => 'INEP', 'required' => $obrigarCamposCenso, 'max_length' => 8, 'label_hint' => 'Somente números']);

            $this->carregaDadosDoPost();

            $objTemp = new clsPessoaJuridica($this->ref_idpes);
            $objTemp->detalhe();

            $this->campoOculto(nome: 'cod_escola', valor: $this->cod_escola);
            $this->campoTexto(nome: 'fantasia', campo: 'Escola', valor: $this->fantasia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
            $this->campoTexto(nome: 'sigla', campo: 'Sigla', valor: $this->sigla, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
            $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

            if ($nivel === 1) {
                $cabecalhos[] = 'Instituicao';
                $objInstituicao = new clsPmieducarInstituicao();
                $opcoes = ['' => 'Selecione'];
                $objInstituicao->setOrderby('nm_instituicao ASC');
                $lista = $objInstituicao->lista();

                if (is_array($lista)) {
                    foreach ($lista as $linha) {
                        $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                    }
                }

                $this->campoLista(nome: 'ref_cod_instituicao', campo: 'Instituição', valor: $opcoes, default: $this->ref_cod_instituicao);
            } else {
                $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

                if ($this->ref_cod_instituicao) {
                    $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
                } else {
                    exit('Usuário não é do nivel poli-institucional e não possui uma instituição');
                }
            }

            $zonas = App_Model_ZonaLocalizacao::getInstance();
            $zonas = [null => 'Selecione'] + $zonas->getEnums();

            $options = [
                'label' => 'Zona localização',
                'value' => $this->zona_localizacao,
                'resources' => $zonas,
                'required' => true,
            ];

            $this->inputsHelper()->select(attrName: 'zona_localizacao', inputOptions: $options);

            $this->campoOculto(nome: 'com_cnpj', valor: $this->com_cnpj);

            if (!$this->cod_escola) {
                $this->cnpj = urldecode($_POST['cnpj']);
                $this->cnpj = idFederal2int($this->cnpj);
                $this->cnpj = empty($this->cnpj) ? $this->cnpj : int2IdFederal($this->cnpj);
            }

            if (empty($this->cnpj) && $objTemp->cnpj) {
                $this->cnpj = $objTemp->cnpj;
            }

            $objJuridica = new clsPessoaJuridica($this->pessoaj_id);

            $det = $objJuridica->detalhe();
            $this->ref_idpes = $det['idpes'];

            if (!$this->fantasia) {
                $this->fantasia = $det['fantasia'];
            }

            if ($this->cnpj) {
                $this->cnpj = (is_numeric($this->cnpj)) ? int2CNPJ($this->cnpj) : int2CNPJ(idFederal2int($this->cnpj));
            }

            $this->campoRotulo(nome: 'cnpj_', campo: 'CNPJ', valor: $this->cnpj);
            $this->campoOculto(nome: 'cnpj', valor: idFederal2int($this->cnpj));
            $this->campoOculto(nome: 'ref_idpes', valor: $this->ref_idpes);
            $this->campoOculto(nome: 'cod_escola', valor: $this->cod_escola);
            $this->campoTexto(nome: 'fantasia', campo: 'Escola', valor: $this->fantasia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
            $this->campoTexto(nome: 'sigla', campo: 'Sigla', valor: $this->sigla, tamanhovisivel: 30, tamanhomaximo: 20, obrigatorio: true);
            $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

            if ($nivel == 1) {
                $cabecalhos[] = 'Instituicao';
                $objInstituicao = new clsPmieducarInstituicao();
                $opcoes = ['' => 'Selecione'];
                $objInstituicao->setOrderby('nm_instituicao ASC');
                $lista = $objInstituicao->lista();

                if (is_array($lista)) {
                    foreach ($lista as $linha) {
                        $opcoes[$linha['cod_instituicao']] = $linha['nm_instituicao'];
                    }
                }

                $this->campoLista(nome: 'ref_cod_instituicao', campo: 'Instituicao', valor: $opcoes, default: $this->ref_cod_instituicao);
            } else {
                $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

                if ($this->ref_cod_instituicao) {
                    $this->campoOculto(nome: 'ref_cod_instituicao', valor: $this->ref_cod_instituicao);
                } else {
                    exit('Usuário não é do nivel poli-institucional e não possui uma instituição');
                }
            }

            $zonas = App_Model_ZonaLocalizacao::getInstance();
            $zonas = [null => 'Selecione'] + $zonas->getEnums();

            $options = [
                'label' => 'Zona localização',
                'value' => $this->zona_localizacao,
                'resources' => $zonas,
                'required' => true,
            ];

            $this->inputsHelper()->select(attrName: 'zona_localizacao', inputOptions: $options);

            $resources = SelectOptions::localizacoesDiferenciadasEscola();
            $options = ['label' => 'Localização diferenciada da escola', 'resources' => $resources, 'value' => $this->localizacao_diferenciada, 'required' => $obrigarCamposCenso, 'size' => 70];
            $this->inputsHelper()->select(attrName: 'localizacao_diferenciada', inputOptions: $options);

            $this->viewAddress();

            $this->inputsHelper()->simpleSearchDistrito(attrName: 'district', inputOptions: [
                'required' => $obrigarCamposCenso,
                'label' => 'Distrito',
            ], helperOptions: [
                'objectName' => 'district',
                'hiddenInputOptions' => [
                    'options' => [
                        'value' => $this->iddis ?? $this->district_id,
                    ],
                ],
            ]);

            $this->inputTelefone(type: '1', typeLabel: 'Telefone 1');
            $this->inputTelefone(type: '2', typeLabel: 'Telefone 2');
            $this->inputTelefone(type: 'mov', typeLabel: 'Celular');
            $this->inputTelefone(type: 'fax', typeLabel: 'Fax');
            $this->campoRotulo(nome: 'p_email', campo: 'E-mail', valor: $this->p_email);
            $this->campoTexto(nome: 'p_http', campo: 'Site/Blog/Rede social', valor: $this->p_http, tamanhovisivel: '50', tamanhomaximo: '255');
            $this->passou = true;
            $this->campoOculto(nome: 'passou', valor: $this->passou);

            $this->inputsHelper()->numeric(attrName: 'latitude', inputOptions: ['max_length' => '20', 'size' => '20', 'required' => false, 'value' => $this->latitude, 'label_hint' => 'São aceito somente números, ponto "." e hífen "-"']);
            $this->inputsHelper()->numeric(attrName: 'longitude', inputOptions: ['max_length' => '20', 'size' => '20', 'required' => false, 'value' => $this->longitude, 'label_hint' => 'São aceito somente números, ponto "." e hífen "-"']);

            $this->campoCheck(nome: 'bloquear_lancamento_diario_anos_letivos_encerrados', campo: 'Bloquear lançamento no diário para anos letivos encerrados', valor: $this->bloquear_lancamento_diario_anos_letivos_encerrados);
            $this->campoCheck(nome: 'utiliza_regra_diferenciada', campo: 'Utiliza regra alternativa', valor: dbBool($this->utiliza_regra_diferenciada), dica: 'Se marcado a escola utilizará a regra de avaliação alternativa informada na Série');

            $resources = SelectOptions::situacoesFuncionamentoEscola();
            $options = ['label' => 'Situação de funcionamento', 'resources' => $resources, 'value' => $this->situacao_funcionamento];
            $this->inputsHelper()->select(attrName: 'situacao_funcionamento', inputOptions: $options);

            $resources = SelectOptions::dependenciasAdministrativasEscola();
            $options = ['label' => 'Dependência administrativa', 'resources' => $resources, 'value' => $this->dependencia_administrativa];
            $this->inputsHelper()->select(attrName: 'dependencia_administrativa', inputOptions: $options);

            $orgaos = OrgaoVinculadoEscola::getDescriptiveValues();
            $helperOptions = ['objectName' => 'orgao_vinculado_escola'];
            $options = [
                'label' => 'Órgão ao qual a escola pública está vinculada',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->orgao_vinculado_escola,
                    'all_values' => $orgaos,
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $resources = [
                null => 'Selecione',
                0 => 'Não',
                1 => 'Sim',
                2 => 'Em tramitação',
            ];
            $options = [
                'label' => 'Regulamentação/Autorização no conselho ou órgão público de educação',
                'resources' => $resources,
                'value' => $this->regulamentacao,
                'size' => 70,
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'regulamentacao', inputOptions: $options);

            $resources = SelectOptions::esferasAdministrativasEscola();
            $options = [
                'label' => 'Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização',
                'resources' => $resources,
                'value' => $this->esfera_administrativa,
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'esfera_administrativa', inputOptions: $options);

            $options = ['label' => 'Ato de criação', 'value' => $this->ato_criacao, 'size' => 70, 'required' => false];
            $this->inputsHelper()->text(attrNames: 'ato_criacao', inputOptions: $options);

            $options = ['label' => 'Ato autorizativo', 'value' => $this->ato_autorizativo, 'size' => 70, 'required' => false];
            $this->inputsHelper()->text(attrNames: 'ato_autorizativo', inputOptions: $options);

            $mantenedoras = MantenedoraDaEscolaPrivada::getDescriptiveValues();
            $helperOptions = ['objectName' => 'mantenedora_escola_privada'];
            $options = [
                'label' => 'Mantenedora da escola privada',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->mantenedora_escola_privada,
                    'all_values' => $mantenedoras,
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $resources = [
                '' => 'Selecione',
                1 => 'Particular',
                2 => 'Comunitária',
                3 => 'Confessional',
                4 => 'Filantrópica',
            ];

            $options = [
                'label' => 'Categoria da escola privada',
                'resources' => $resources,
                'value' => $this->categoria_escola_privada,
                'required' => false,
                'size' => 70,
            ];

            $this->inputsHelper()->select(attrName: 'categoria_escola_privada', inputOptions: $options);

            $helperOptions = ['objectName' => 'poder_publico_parceria_convenio'];
            $resources = [
                1 => 'Secretaria estadual',
                2 => 'Secretaria municipal',
                3 => 'Não possui parceria ou convênio',
            ];

            $options = [
                'label' => 'Poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->poder_publico_parceria_convenio,
                    'all_values' => $resources,
                ],
            ];

            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'formas_contratacao_parceria_escola_secretaria_estadual'];
            $resources = [
                1 => 'Termo de colaboração (Lei nº 13.019/2014)',
                2 => 'Termo de fomento (Lei nº 13.019/2014)',
                3 => 'Acordo de cooperação (Lei nº 13.019/2014)',
                4 => 'Contrato de prestação de serviço',
                5 => 'Termo de cooperação técnica e financeira',
                6 => 'Contrato de consórcio público/Convênio de cooperação',
            ];

            $options = [
                'label' => 'Forma(s) de contratação da parceria ou convênio entre a escola e a <strong>Secretaria estadual</strong> de educação',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->formas_contratacao_parceria_escola_secretaria_estadual,
                    'all_values' => $resources,
                ],
            ];

            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'formas_contratacao_parceria_escola_secretaria_municipal'];

            $options = [
                'label' => 'Forma(s) de contratação da parceria ou convênio entre a escola e a <strong>Secretaria municipal</strong> de educação',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->formas_contratacao_parceria_escola_secretaria_municipal,
                    'all_values' => $resources,
                ],
            ];

            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $resources = [
                '' => 'Selecione',
                1 => 'Estadual',
                2 => 'Municipal',
                3 => 'Estadual e Municipal',
            ];

            $this->campoCnpj(nome: 'cnpj_mantenedora_principal', campo: 'CNPJ da mantenedora principal da escola privada', valor: $this->cnpj_mantenedora_principal);

            $hiddenInputOptions = ['options' => ['value' => $this->secretario_id]];
            $helperOptions = ['objectName' => 'secretario', 'hiddenInputOptions' => $hiddenInputOptions];
            $options = [
                'label' => 'Secretário escolar',
                'size' => 50,
                'required' => false,
            ];
            $this->inputsHelper()->simpleSearchPessoa(attrName: 'nome', inputOptions: $options, helperOptions: $helperOptions);

            $resources = SelectOptions::esferasAdministrativasEscola();
            $options = [
                'label' => 'Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização',
                'resources' => $resources,
                'value' => $this->esfera_administrativa,
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'esfera_administrativa', inputOptions: $options);

            $this->campoQuebra();
            $this->addSchoolManagersTable();

            if ($_POST['escola_curso']) {
                $this->escola_curso = unserialize(data: urldecode($_POST['escola_curso']), options: ['stdclass']);
            }

            if ($_POST['escola_curso_autorizacao']) {
                $this->escola_curso_autorizacao = unserialize(data: urldecode($_POST['escola_curso_autorizacao']), options: ['stdclass']);
            }

            if ($_POST['escola_curso_anos_letivos']) {
                $this->escola_curso_anos_letivos = unserialize(data: urldecode($_POST['escola_curso_anos_letivos']), options: ['stdclass']);
            }

            if (is_numeric($this->cod_escola) && !$_POST) {
                $obj = new clsPmieducarEscolaCurso($this->cod_escola);
                $registros = $obj->lista($this->cod_escola);
                if ($registros) {
                    foreach ($registros as $campo) {
                        $this->escola_curso[$campo['ref_cod_curso']] = $campo['ref_cod_curso'];
                        $this->escola_curso_autorizacao[$campo['ref_cod_curso']] = $campo['autorizacao'];
                        $this->escola_curso_anos_letivos[$campo['ref_cod_curso']] = json_decode($campo['anos_letivos']);
                    }
                }
            }

            if ($_POST['ref_cod_curso']) {
                $this->escola_curso[$_POST['ref_cod_curso']] = $_POST['ref_cod_curso'];

                if ($this->autorizacao) {
                    $this->escola_curso_autorizacao[$_POST['ref_cod_curso']] = $this->autorizacao;
                }

                if ($this->adicionar_anos_letivos) {
                    $this->escola_curso_anos_letivos[$_POST['ref_cod_curso']] = $this->adicionar_anos_letivos;
                }

                unset($this->ref_cod_curso);
            }

            $this->campoQuebra();
            $this->campoOculto(nome: 'excluir_curso', valor: '');
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
                        $nm_curso = empty($obj_curso_det['descricao']) ? $obj_curso_det['nm_curso'] : "{$obj_curso_det['nm_curso']} ({$obj_curso_det['descricao']})";
                        $nm_autorizacao = $this->escola_curso_autorizacao[$curso];
                        $anosLetivos = $this->escola_curso_anos_letivos[$curso] ?: [];
                        $this->campoTextoInv(nome: "ref_cod_curso_{$curso}", campo: '', valor: $nm_curso, tamanhovisivel: 50, tamanhomaximo: 255, duplo: true);
                        $this->campoTextoInv(nome: "autorizacao_{$curso}", campo: '', valor: $nm_autorizacao, tamanhovisivel: 20, tamanhomaximo: 255);
                        $this->campoTextoInv(nome: "anos_letivos_{$curso}", campo: '', valor: 'Anos: ' . implode(separator: ',', array: $anosLetivos), tamanhovisivel: 20, tamanhomaximo: 255, descricao2: "<a href='#' onclick=\"getElementById('excluir_curso').value = '{$curso}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>");
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

            $this->campoOculto(nome: 'escola_curso', valor: serialize($this->escola_curso));
            $this->campoOculto(nome: 'escola_curso_autorizacao', valor: serialize($this->escola_curso_autorizacao));
            $this->campoOculto(nome: 'escola_curso_anos_letivos', valor: serialize($this->escola_curso_anos_letivos));
            $opcoes = ['' => 'Selecione'];

            // EDITAR
            if ($this->cod_escola || $this->ref_cod_instituicao) {
                $objTemp = new clsPmieducarCurso();
                $objTemp->setOrderby('nm_curso');
                $lista = $objTemp->lista(int_ativo: 1, int_ref_cod_instituicao: $this->ref_cod_instituicao);

                if (is_array($lista) && count($lista)) {
                    foreach ($lista as $registro) {
                        $nm_curso = empty($registro['descricao']) ? $registro['nm_curso'] : "{$registro['nm_curso']} ({$registro['descricao']})";
                        $opcoes[$registro['cod_curso']] = $nm_curso;
                    }
                }
            }

            if ($aux) {
                $this->campoLista(nome: 'ref_cod_curso', campo: 'Curso', valor: $opcoes, default: $this->ref_cod_curso, complemento: "<a href='#' onclick=\"getElementById('incluir_curso').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>", obrigatorio: false);
            } else {
                $this->campoLista(nome: 'ref_cod_curso', campo: 'Curso', valor: $opcoes, default: $this->ref_cod_curso, complemento: "<a href='#' onclick=\"getElementById('incluir_curso').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
            }

            $this->campoTexto(nome: 'autorizacao', campo: 'Autorização', valor: '', tamanhovisivel: 30, tamanhomaximo: 255);

            $helperOptions = [
                'objectName' => 'adicionar_anos_letivos',
            ];

            $options = [
                'label' => 'Anos letivos',
                'required' => false,
                'size' => 50,
                'value' => '',
                'options' => [
                    'all_values' => $this->sugestaoAnosLetivos(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $this->campoOculto(nome: 'incluir_curso', valor: '');
            $this->campoQuebra();

            $helperOptions = ['objectName' => 'local_funcionamento'];
            $options = [
                'label' => 'Local de funcionamento',
                'options' => [
                    'values' => $this->local_funcionamento,
                    'all_values' => SelectOptions::locaisFuncionamentoEscola(),
                ],
                'size' => 70,
                'required' => $obrigarCamposCenso,
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $localFuncionamento = is_array($this->local_funcionamento) ? $this->local_funcionamento : [];

            // Os campos: Forma de ocupação do prédio e Código da escola que compartilha o prédio
            // serão desabilitados quando local de funcionamento for diferente de 3 (Prédio escolar)
            $disabled = !in_array(needle: LocalFuncionamento::PREDIO_ESCOLAR, haystack: $localFuncionamento);
            $resources = [null => 'Selecione',
                1 => 'Próprio',
                2 => 'Alugado',
                3 => 'Cedido'];
            $options = ['disabled' => $disabled, 'label' => 'Forma de ocupação do prédio', 'resources' => $resources, 'value' => $this->condicao, 'size' => 70, 'required' => false];
            $this->inputsHelper()->select(attrName: 'condicao', inputOptions: $options);

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
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'predio_compartilhado_outra_escola', inputOptions: $options);

            $this->geraCamposCodigoInepEscolaCompartilhada();

            $resources = [
                null => 'Selecione',
                0 => 'Não',
                1 => 'Sim',
            ];
            $options = [
                'label' => 'Fornecimento de água potável para consumo',
                'resources' => $resources,
                'value' => $this->agua_potavel_consumo,
                'required' => $obrigarCamposCenso,
                'size' => 70,
            ];
            $this->inputsHelper()->select(attrName: 'agua_potavel_consumo', inputOptions: $options);

            $helperOptions = ['objectName' => 'abastecimento_agua'];
            $options = ['label' => 'Abastecimento de água',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->abastecimento_agua,
                    'all_values' => [
                        1 => 'Rede pública',
                        2 => 'Poço artesiano',
                        3 => 'Cacimba/cisterna/poço',
                        4 => 'Fonte/rio/igarapé/riacho/córrego',
                        5 => 'Não há abastecimento de água',
                        6 => 'Carro-pipa',
                    ],
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'abastecimento_energia'];
            $options = ['label' => 'Fonte de energia elétrica',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => ['values' => $this->abastecimento_energia,
                    'all_values' => [1 => 'Rede pública',
                        2 => 'Gerador movido a combustível fóssil',
                        3 => 'Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, outras)',
                        4 => 'Não há energia elétrica']]];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'esgoto_sanitario'];
            $options = ['label' => 'Esgotamento sanitário',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => ['values' => $this->esgoto_sanitario,
                    'all_values' => [1 => 'Rede pública',
                        2 => 'Fossa séptica',
                        4 => 'Fossa rudimentar/comum',
                        3 => 'Não há esgotamento sanitário']]];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'destinacao_lixo'];
            $options = ['label' => 'Destinação do lixo',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => ['values' => $this->destinacao_lixo,
                    'all_values' => [1 => 'Serviço de coleta',
                        2 => 'Queima',
                        7 => 'Enterra',
                        5 => 'Leva a uma destinação final licenciada pelo poder público',
                        3 => 'Descarta em outra área', ]]];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'tratamento_lixo'];
            $options = [
                'label' => 'Tratamento do lixo/resíduos que a escola realiza',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->tratamento_lixo,
                    'all_values' => TratamentoLixo::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $options = [
                'label' => 'Alimentação escolar para os alunos(as)',
                'value' => $this->alimentacao_escolar_alunos,
                'required' => $obrigarCamposCenso,
                'prompt' => 'Selecione',
                'size' => 70,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'alimentacao_escolar_alunos', inputOptions: $options);

            $options = [
                'label' => 'Escola compartilha espaços para atividades de integração escola-comunidade',
                'value' => $this->compartilha_espacos_atividades_integracao,
                'required' => false,
                'prompt' => 'Selecione',
                'size' => 70,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'compartilha_espacos_atividades_integracao', inputOptions: $options);

            $options = [
                'label' => 'Escola usa espaços e equipamentos do entorno escolar para atividades regulares com os alunos(as)',
                'value' => $this->usa_espacos_equipamentos_atividades_regulares,
                'required' => false,
                'prompt' => 'Selecione',
                'size' => 70,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'usa_espacos_equipamentos_atividades_regulares', inputOptions: $options);

            $options = [
                'label' => 'Possui dependências',
                'label_hint' => 'Preencha com: Sim, para exportar os campos de dependências no arquivo do Censo escolar',
                'value' => $this->possui_dependencias,
                'required' => $obrigarCamposCenso,
                'prompt' => 'Selecione',
                'size' => 40,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'possui_dependencias', inputOptions: $options);

            $helperOptions = ['objectName' => 'salas_gerais'];
            $options = [
                'label' => 'Salas gerais',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_gerais,
                    'all_values' => SalasGerais::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'salas_funcionais'];
            $options = [
                'label' => 'Salas funcionais',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_funcionais,
                    'all_values' => SalasFuncionais::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'banheiros'];
            $options = [
                'label' => 'Banheiros',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->banheiros,
                    'all_values' => Banheiros::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'laboratorios'];
            $options = [
                'label' => 'Laboratórios',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->laboratorios,
                    'all_values' => Laboratorios::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'salas_atividades'];
            $options = [
                'label' => 'Salas de atividades',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->salas_atividades,
                    'all_values' => SalasAtividades::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'dormitorios'];
            $options = [
                'label' => 'Dormitórios',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->dormitorios,
                    'all_values' => Dormitorios::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'areas_externas'];
            $options = [
                'label' => 'Áreas externas',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->areas_externas,
                    'all_values' => AreasExternas::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'recursos_acessibilidade'];
            $options = [
                'label' => 'Recursos de acessibilidade',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->recursos_acessibilidade,
                    'all_values' => RecursosAcessibilidade::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $options = ['label' => 'Número de salas de aula utilizadas na escola dentro do prédio escolar', 'resources' => $resources, 'value' => $this->numero_salas_utilizadas_dentro_predio, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4];
            $this->inputsHelper()->integer(attrName: 'numero_salas_utilizadas_dentro_predio', inputOptions: $options);

            $options = ['label' => 'Número de salas de aula utilizadas na escola fora do prédio escolar', 'resources' => $resources, 'value' => $this->numero_salas_utilizadas_fora_predio, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4];
            $this->inputsHelper()->integer(attrName: 'numero_salas_utilizadas_fora_predio', inputOptions: $options);

            $options = ['label' => 'Número de salas de aula climatizadas', 'resources' => $resources, 'value' => $this->numero_salas_climatizadas, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4];
            $this->inputsHelper()->integer(attrName: 'numero_salas_climatizadas', inputOptions: $options);

            $options = ['label' => 'Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida', 'resources' => $resources, 'value' => $this->numero_salas_acessibilidade, 'required' => false, 'size' => 5, 'placeholder' => '', 'max_length' => 4];
            $this->inputsHelper()->integer(attrName: 'numero_salas_acessibilidade', inputOptions: $options);

            $helperOptions = ['objectName' => 'equipamentos'];
            $options = [
                'label' => 'Equipamentos da escola',
                'size' => 50,
                'required' => $this->validarCamposObrigatoriosCenso(),
                'options' => [
                    'values' => $this->equipamentos,
                    'all_values' => Equipamentos::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'uso_internet'];
            $options = [
                'label' => 'Acesso à internet',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->uso_internet,
                    'all_values' => UsoInternet::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $options = [
                'label' => 'Possui internet banda larga',
                'value' => $this->acesso_internet,
                'required' => false,
                'prompt' => 'Selecione',
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'acesso_internet', inputOptions: $options);

            $helperOptions = ['objectName' => 'rede_local'];
            $options = [
                'label' => 'Rede local de interligação de computadores',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->rede_local,
                    'all_values' => RedeLocal::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'equipamentos_acesso_internet'];
            $options = [
                'label' => 'Equipamentos que os aluno(a)s usam para acessar a internet da escola',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->equipamentos_acesso_internet,
                    'all_values' => EquipamentosAcessoInternet::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $this->campoRotulo(
                nome: 'quantidade_computadores_alunos',
                campo: '<b>Quantidade de computadores de uso dos alunos</b>'
            );

            $options = ['label' => 'Computadores de mesa (desktop)', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_mesa, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'quantidade_computadores_alunos_mesa', inputOptions: $options);

            $options = ['label' => 'Computadores portáteis', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_portateis, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'quantidade_computadores_alunos_portateis', inputOptions: $options);

            $options = ['label' => 'Tablets', 'resources' => $resources, 'value' => $this->quantidade_computadores_alunos_tablets, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'quantidade_computadores_alunos_tablets', inputOptions: $options);

            $this->campoRotulo(
                nome: 'equipamentos_aprendizagem',
                campo: '<b>Quantidade de equipamentos para ensino/aprendizagem</b>'
            );

            $options = ['label' => 'Aparelho de Televisão', 'resources' => $resources, 'value' => $this->televisoes, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'televisoes', inputOptions: $options);

            $options = ['label' => 'Projetor Multimídia (Data show)', 'resources' => $resources, 'value' => $this->projetores_digitais, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'projetores_digitais', inputOptions: $options);

            $options = ['label' => 'Aparelho de som', 'resources' => $resources, 'value' => $this->aparelhos_de_som, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'aparelhos_de_som', inputOptions: $options);

            $options = ['label' => 'Aparelho de DVD/Blu-ray', 'resources' => $resources, 'value' => $this->dvds, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'dvds', inputOptions: $options);

            $options = ['label' => 'Lousa digital', 'resources' => $resources, 'value' => $this->lousas_digitais, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
            $this->inputsHelper()->integer(attrName: 'lousas_digitais', inputOptions: $options);

            $this->campoCheck(nome: 'nao_ha_funcionarios_para_funcoes', campo: 'Não há funcionários para as funções listadas', valor: $this->nao_ha_funcionarios_para_funcoes);

            $this->campoRotulo(
                nome: 'quantidade_profissionais',
                campo: '<b>Quantidade de profissionais</b>'
            );

            foreach ($this->inputsRecursos as $key => $label) {
                $options = ['label' => $label, 'value' => $this->{$key}, 'required' => false, 'size' => 4, 'max_length' => 4, 'placeholder' => ''];
                $this->inputsHelper()->integer(attrName: $key, inputOptions: $options);
            }

            $resources = [null => 'Selecione',
                0 => 'Não oferece',
                1 => 'Não exclusivamente',
                2 => 'Exclusivamente'];
            $options = ['label' => 'Atendimento educacional especializado - AEE', 'resources' => $resources, 'value' => $this->atendimento_aee, 'required' => false, 'size' => 70];
            $this->inputsHelper()->select(attrName: 'atendimento_aee', inputOptions: $options);

            $habilitaFundamentalCiclo = false;
            if ($this->cod_escola) {
                $objEscola = new clsPmieducarEscola($this->cod_escola);
                $habilitaFundamentalCiclo = dbBool($objEscola->possuiTurmasDoEnsinoFundamentalEmCiclos());
            }

            $options = [
                'label' => 'Ensino fundamental organizado em ciclos',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->fundamental_ciclo,
                'required' => false,
                'disabled' => !$habilitaFundamentalCiclo,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'fundamental_ciclo', inputOptions: $options);

            $obrigarOrganizacaoEnsino = false;
            if ($this->cod_escola) {
                $obrigarOrganizacaoEnsino = new HasDifferentStepsOfChildEducationValidator($this->cod_escola);
                $obrigarOrganizacaoEnsino = $obrigarOrganizacaoEnsino->isValid();
            }

            $helperOptions = ['objectName' => 'instrumentos_pedagogicos'];
            $options = [
                'label' => 'Instrumentos e materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino-aprendizagem',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->instrumentos_pedagogicos,
                    'all_values' => InstrumentosPedagogicos::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $helperOptions = ['objectName' => 'orgaos_colegiados'];
            $options = [
                'label' => 'Órgãos colegiados em funcionamento na escola',
                'size' => 50,
                'required' => $obrigarCamposCenso,
                'options' => [
                    'values' => $this->orgaos_colegiados,
                    'all_values' => OrgaosColegiados::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $options = [
                'label' => 'A escola desenvolve ações na área de educação ambiental',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->acao_area_ambiental,
                'required' => true,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'acao_area_ambiental', inputOptions: $options);

            $helperOptions = ['objectName' => 'acoes_area_ambiental'];
            $options = [
                'label' => 'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->acoes_area_ambiental,
                    'all_values' => AcoesAmbientais::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $options = [
                'label' => 'Escola faz exame de seleção para ingresso de seus aluno(a)s',
                'label_hint' => 'Avaliação por prova e /ou analise curricular',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->exame_selecao_ingresso,
                'required' => false,
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'exame_selecao_ingresso', inputOptions: $options);

            $helperOptions = ['objectName' => 'reserva_vagas_cotas'];
            $options = [
                'label' => 'Reserva de vagas por sistema de cotas para grupos específicos de alunos(as)',
                'size' => 50,
                'required' => false,
                'options' => [
                    'values' => $this->reserva_vagas_cotas,
                    'all_values' => ReservaVagasCotas::getDescriptiveValues(),
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $resources = [
                null => 'Selecione',
                0 => 'Não sei',
                1 => 'Sim',
                2 => 'A escola não possui projeto político pedagógico/proposta pedagógica',
            ];
            $options = [
                'resources' => $resources,
                'label' => 'Projeto político pedagógico ou a proposta pedagógica da escola atualizado nos últimos 12 meses até a data de referência',
                'label_hint' => '(conforme art. 12 da LDB)',
                'placeholder' => 'Selecione',
                'prompt' => 'Selecione',
                'value' => $this->projeto_politico_pedagogico,
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'projeto_politico_pedagogico', inputOptions: $options);

            $resources = SelectOptions::localizacoesDiferenciadasEscola();
            $options = ['label' => 'Localização diferenciada da escola', 'resources' => $resources, 'value' => $this->localizacao_diferenciada, 'required' => $obrigarCamposCenso, 'size' => 70];
            $this->inputsHelper()->select(attrName: 'localizacao_diferenciada', inputOptions: $options);

            $resources = [null => 'Selecione',
                1 => 'Não utiliza',
                2 => 'Quilombola',
                3 => 'Indígena'];

            $options = [
                'label' => 'Escola indígena',
                'value' => $this->educacao_indigena,
                'required' => false,
                'prompt' => 'Selecione',
            ];
            $this->inputsHelper()->booleanSelect(attrName: 'educacao_indigena', inputOptions: $options);

            $resources = [
                null => 'Selecione',
                1 => 'Língua Portuguesa',
                2 => 'Língua Indígena',
            ];
            $habilitaLiguaMinistrada = $this->educacao_indigena == 1;
            $options = ['label' => 'Língua em que o ensino é ministrado',
                'resources' => $resources,
                'value' => $this->lingua_ministrada,
                'required' => false,
                'disabled' => !$habilitaLiguaMinistrada,
                'size' => 70];
            $this->inputsHelper()->select(attrName: 'lingua_ministrada', inputOptions: $options);

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
                    'all_values' => $resources,
                ],
            ];
            $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

            $resources = SelectOptions::unidadesVinculadasEscola();
            $options = [
                'label' => 'Unidade vinculada à Escola de Educação Básica ou Unidade Ofertante de Educação Superior',
                'resources' => $resources,
                'value' => $this->unidade_vinculada_outra_instituicao,
                'size' => 70,
                'required' => false,
            ];
            $this->inputsHelper()->select(attrName: 'unidade_vinculada_outra_instituicao', inputOptions: $options);

            $this->campoTexto(nome: 'inep_escola_sede', campo: 'Código da escola sede', valor: $this->inep_escola_sede, tamanhovisivel: 10, tamanhomaximo: 8);

            $options = [
                'label' => 'Código da IES',
                'required' => false,
            ];
            $helperOptions = [
                'objectName' => 'codigo_ies',
                'hiddenInputOptions' => [
                    'options' => ['value' => $this->codigo_ies],
                ],
            ];
            $this->inputsHelper()->simpleSearchIes(attrName: null, inputOptions: $options, helperOptions: $helperOptions);

            $this->campoTabelaInicio('espacos', 'Espaços Escolares', [
                'Espaço Escolar',
                'Tamanho do espaço<br><font size=-1; color=gray>Em metros quadrados</font>',
            ], $this->espaco_escolares);
            $this->campoTexto(nome: 'espaco_escolar_nome', campo: 'Espaço Escolar', valor: $this->espaco_escolar_nome);
            $this->campoNumero(nome: 'espaco_escolar_tamanho', campo: 'Tamanho do espaço', valor: $this->espaco_escolar_tamanho, tamanhovisivel: 4, tamanhomaximo: 6);
            if (!$_POST) {
                $this->campoOculto(nome: 'espaco_escolar_id', valor: $this->espaco_escolar_id);
            }
            $this->campoTabelaFim();

            $this->breadcrumb(currentPage: 'Escola', breadcrumbs: ['educar_index.php' => 'Escola']);
            $this->url_cancelar = (!empty($this->cod_escola)) ? "educar_escola_det.php?cod_escola={$this->cod_escola}" : 'educar_escola_lst.php';
            $this->nome_url_cancelar = 'Cancelar';
        }
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_escola_lst.php');
        $this->pesquisaPessoaJuridica = false;

        if (!$this->validaCaracteresPermitidosComplemento()) {
            return false;
        }

        if (!$this->validaCnpjMantenedora()) {
            return false;
        }

        if (!$this->validaDigitosInepEscola(inep: $this->escola_inep_id, nomeCampo: 'Código INEP')) {
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

        if ($this->nao_ha_funcionarios_para_funcoes === null &&
            $this->validaRecursos() === false) {
            return false;
        }

        $this->validateManagersRules();

        if (!$this->validaDigitosInepEscolaCompartilhada()) {
            return false;
        }

        if (!$this->validaOpcoesUnicasMultipleSearch()) {
            return false;
        }

        if (!isset($this->pessoaj_id_oculto) ||
            !is_int((int) $this->pessoaj_id_oculto)
        ) {
            $this->mensagem = 'Erro ao selecionar a pessoa jurídica';

            return false;
        }

        $this->preparaDados();

        $pessoaJuridica = (new clsJuridica((int) $this->pessoaj_id_oculto))->detalhe();

        if ($pessoaJuridica === false) {
            throw new \iEducar\Support\Exceptions\Exception('Pessoa jurídica não encontrada');
        }

        $this->bloquear_lancamento_diario_anos_letivos_encerrados = is_null($this->bloquear_lancamento_diario_anos_letivos_encerrados) ? 0 : 1;
        $this->utiliza_regra_diferenciada = !is_null($this->utiliza_regra_diferenciada);

        DB::beginTransaction();

        $cod_escola = $this->cadastraEscola((int) $this->pessoaj_id_oculto);

        if ($cod_escola === false) {
            return false;
        }

        $this->processaTelefones($this->pessoaj_id_oculto);

        $this->saveAddress($this->ref_idpes);

        if (!$this->cadastraEscolaCurso(cod_escola: $cod_escola, excluirEscolaCursos: false)) {
            return false;
        }

        $this->saveInep($cod_escola);

        $this->atualizaNomePessoaJuridica($this->ref_idpes);

        $this->atualizaEspacoEscolares($cod_escola);

        DB::commit();

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';

        throw new HttpResponseException(
            new RedirectResponse('educar_escola_lst.php')
        );
    }

    private function validaDigitosInepEscolaCompartilhada()
    {
        for ($i = 1; $i <= 6; $i++) {
            $seq = $i == 1 ? '' : $i;
            $campo = 'codigo_inep_escola_compartilhada' . $seq;
            $ret = $this->validaDigitosInepEscola(inep: $this->$campo, nomeCampo: 'Código da escola que compartilha o prédio ' . $i);
            if (!$ret) {
                return false;
            }
        }

        return true;
    }

    public function validaCnpjMantenedora(): bool
    {
        if ((int) $this->dependencia_administrativa === DependenciaAdministrativaEscola::PRIVADA &&
            !empty($this->cnpj_mantenedora_principal) &&
            !validaCNPJ($this->cnpj_mantenedora_principal)) {
            $this->mensagem = 'O CNPJ da mantenedora principal é inválido. Favor verificar.';

            return false;
        }

        return true;
    }

    private function cadastraEscolaCurso($cod_escola, $excluirEscolaCursos = false)
    {
        if ($excluirEscolaCursos === true) {
            (new clsPmieducarEscolaCurso($this->cod_escola))->excluirTodos();
        }

        $this->escola_curso = unserialize(data: urldecode($this->escola_curso), options: ['stdclass']);
        $this->escola_curso_autorizacao = unserialize(data: urldecode($this->escola_curso_autorizacao), options: ['stdclass']);
        $this->escola_curso_anos_letivos = unserialize(data: urldecode($this->escola_curso_anos_letivos), options: ['stdclass']);

        if ($this->escola_curso) {
            foreach ($this->escola_curso as $campo) {
                $curso_escola = new clsPmieducarEscolaCurso(ref_cod_escola: $cod_escola, ref_cod_curso: $campo, ref_usuario_exc: null, ref_usuario_cad: $this->pessoa_logada, data_cadastro: null, data_exclusao: null, ativo: 1, autorizacao: $this->escola_curso_autorizacao[$campo], anos_letivos: $this->escola_curso_anos_letivos[$campo]);
                $cadastrou_ = $curso_escola->cadastra();

                if (!$cadastrou_) {
                    $this->mensagem = 'Cadastro não realizado.<br>';

                    return false;
                }
            }

            $this->storeManagers($cod_escola);
        }

        return true;
    }

    private function constroiObjetoEscola($pessoaj_id_oculto, $escola = null)
    {
        if ($escola instanceof clsPmieducarEscola) {
            $obj = $escola;
        } else {
            $obj = new clsPmieducarEscola(cod_escola: null, ref_usuario_cad: $this->pessoa_logada, ref_usuario_exc: null, ref_cod_instituicao: $this->ref_cod_instituicao, zona_localizacao: $this->zona_localizacao, ref_idpes: $pessoaj_id_oculto, sigla: $this->sigla, data_cadastro: null, data_exclusao: null, ativo: 1, bloquear_lancamento_diario_anos_letivos_encerrados: null, utiliza_regra_diferenciada: $this->bloquear_lancamento_diario_anos_letivos_encerrados);
        }

        $obj->situacao_funcionamento = $this->situacao_funcionamento;
        $obj->dependencia_administrativa = $this->dependencia_administrativa;
        $obj->orgao_vinculado_escola = $this->orgao_vinculado_escola;
        $obj->latitude = $this->latitude;
        $obj->longitude = $this->longitude;
        $obj->regulamentacao = $this->regulamentacao;
        $obj->ref_idpes_gestor = $this->gestor_id;
        $obj->cargo_gestor = $this->cargo_gestor;
        $obj->email_gestor = $this->email_gestor;
        $obj->local_funcionamento = $this->local_funcionamento;
        $obj->condicao = $this->condicao;
        $obj->predio_compartilhado_outra_escola = $this->predio_compartilhado_outra_escola;
        $obj->codigo_inep_escola_compartilhada = $this->codigo_inep_escola_compartilhada;
        $obj->codigo_inep_escola_compartilhada2 = $this->codigo_inep_escola_compartilhada2;
        $obj->codigo_inep_escola_compartilhada3 = $this->codigo_inep_escola_compartilhada3;
        $obj->codigo_inep_escola_compartilhada4 = $this->codigo_inep_escola_compartilhada4;
        $obj->codigo_inep_escola_compartilhada5 = $this->codigo_inep_escola_compartilhada5;
        $obj->codigo_inep_escola_compartilhada6 = $this->codigo_inep_escola_compartilhada6;
        $obj->agua_potavel_consumo = $this->agua_potavel_consumo;
        $obj->abastecimento_agua = $this->abastecimento_agua;
        $obj->abastecimento_energia = $this->abastecimento_energia;
        $obj->esgoto_sanitario = $this->esgoto_sanitario;
        $obj->destinacao_lixo = $this->destinacao_lixo;
        $obj->tratamento_lixo = $this->tratamento_lixo;
        $obj->alimentacao_escolar_alunos = $this->alimentacao_escolar_alunos;
        $obj->compartilha_espacos_atividades_integracao = $this->compartilha_espacos_atividades_integracao;
        $obj->usa_espacos_equipamentos_atividades_regulares = $this->usa_espacos_equipamentos_atividades_regulares;
        $obj->salas_funcionais = $this->salas_funcionais;
        $obj->salas_gerais = $this->salas_gerais;
        $obj->banheiros = $this->banheiros;
        $obj->laboratorios = $this->laboratorios;
        $obj->salas_atividades = $this->salas_atividades;
        $obj->dormitorios = $this->dormitorios;
        $obj->areas_externas = $this->areas_externas;
        $obj->recursos_acessibilidade = $this->recursos_acessibilidade;
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
        $obj->orgaos_colegiados = $this->orgaos_colegiados;
        $obj->exame_selecao_ingresso = $this->exame_selecao_ingresso;
        $obj->reserva_vagas_cotas = $this->reserva_vagas_cotas;
        $obj->acao_area_ambiental = $this->acao_area_ambiental;
        $obj->acoes_area_ambiental = $this->acoes_area_ambiental;
        $obj->projeto_politico_pedagogico = $this->projeto_politico_pedagogico;
        $obj->localizacao_diferenciada = $this->localizacao_diferenciada;
        $obj->educacao_indigena = $this->educacao_indigena;
        $obj->lingua_ministrada = $this->lingua_ministrada;
        $obj->codigo_lingua_indigena = $this->codigo_lingua_indigena;
        $obj->equipamentos = $this->equipamentos;
        $obj->uso_internet = $this->uso_internet;
        $obj->rede_local = $this->rede_local;
        $obj->equipamentos_acesso_internet = $this->equipamentos_acesso_internet;
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
        $obj->mantenedora_escola_privada = $this->mantenedora_escola_privada;
        $obj->cnpj_mantenedora_principal = idFederal2int($this->cnpj_mantenedora_principal);
        $obj->esfera_administrativa = $this->esfera_administrativa;
        $obj->nao_ha_funcionarios_para_funcoes = $this->nao_ha_funcionarios_para_funcoes !== null;
        $obj->iddis = (int) $this->district_id;
        $obj->poder_publico_parceria_convenio = $this->poder_publico_parceria_convenio;
        $obj->formas_contratacao_parceria_escola_secretaria_estadual = $this->formas_contratacao_parceria_escola_secretaria_estadual;
        $obj->formas_contratacao_parceria_escola_secretaria_municipal = $this->formas_contratacao_parceria_escola_secretaria_municipal;

        foreach ($this->inputsRecursos as $key => $value) {
            $obj->{$key} = $this->{$key};
        }

        return $obj;
    }

    private function processaTelefones($idpes)
    {
        $objTelefone = new clsPessoaTelefone($idpes);
        $objTelefone->excluiTodos();

        $this->cadastraTelefone(idpes: $idpes, tipo: 1, telefone: str_replace(search: '-', replace: '', subject: $this->p_telefone_1), ddd: $this->p_ddd_telefone_1);
        $this->cadastraTelefone(idpes: $idpes, tipo: 2, telefone: str_replace(search: '-', replace: '', subject: $this->p_telefone_2), ddd: $this->p_ddd_telefone_2);
        $this->cadastraTelefone(idpes: $idpes, tipo: 3, telefone: str_replace(search: '-', replace: '', subject: $this->p_telefone_mov), ddd: $this->p_ddd_telefone_mov);
        $this->cadastraTelefone(idpes: $idpes, tipo: 4, telefone: str_replace(search: '-', replace: '', subject: $this->p_telefone_fax), ddd: $this->p_ddd_telefone_fax);

    }

    private function cadastraTelefone($idpes, $tipo, $telefone, $ddd)
    {
        return (new clsPessoaTelefone(int_idpes: $idpes, int_tipo: $tipo, str_fone: $telefone, str_ddd: $ddd, idpes_cad: $this->pessoa_logada))->cadastra();
    }

    public function cadastraEscola(int $pessoaj_id_oculto)
    {
        $escola = $this->constroiObjetoEscola($pessoaj_id_oculto);

        $cod_escola = $escola->cadastra();

        if ($cod_escola === false) {
            $this->mensagem = 'Cadastro não realizado<br>';

            return false;
        }

        return $cod_escola;
    }

    /**
     * Coloca os dados disponíveis no objeto da classe para serem lidos no @method cadastraEscola()
     */
    public function preparaDados()
    {
        $this->orgao_vinculado_escola = $this->transformArrayInString($this->orgao_vinculado_escola);
        $this->mantenedora_escola_privada = $this->transformArrayInString($this->mantenedora_escola_privada);
        $this->local_funcionamento = $this->transformArrayInString($this->local_funcionamento);
        $this->abastecimento_agua = $this->transformArrayInString($this->abastecimento_agua);
        $this->abastecimento_energia = $this->transformArrayInString($this->abastecimento_energia);
        $this->esgoto_sanitario = $this->transformArrayInString($this->esgoto_sanitario);
        $this->destinacao_lixo = $this->transformArrayInString($this->destinacao_lixo);
        $this->tratamento_lixo = $this->transformArrayInString($this->tratamento_lixo);
        $this->salas_funcionais = $this->transformArrayInString($this->salas_funcionais);
        $this->salas_gerais = $this->transformArrayInString($this->salas_gerais);
        $this->banheiros = $this->transformArrayInString($this->banheiros);
        $this->laboratorios = $this->transformArrayInString($this->laboratorios);
        $this->salas_atividades = $this->transformArrayInString($this->salas_atividades);
        $this->dormitorios = $this->transformArrayInString($this->dormitorios);
        $this->areas_externas = $this->transformArrayInString($this->areas_externas);
        $this->recursos_acessibilidade = $this->transformArrayInString($this->recursos_acessibilidade);
        $this->equipamentos = $this->transformArrayInString($this->equipamentos);
        $this->uso_internet = $this->transformArrayInString($this->uso_internet);
        $this->rede_local = $this->transformArrayInString($this->rede_local);
        $this->equipamentos_acesso_internet = $this->transformArrayInString($this->equipamentos_acesso_internet);
        $this->organizacao_ensino = $this->transformArrayInString($this->organizacao_ensino);
        $this->instrumentos_pedagogicos = $this->transformArrayInString($this->instrumentos_pedagogicos);
        $this->orgaos_colegiados = $this->transformArrayInString($this->orgaos_colegiados);
        $this->reserva_vagas_cotas = $this->transformArrayInString($this->reserva_vagas_cotas);
        $this->acoes_area_ambiental = $this->transformArrayInString($this->acoes_area_ambiental);
        $this->codigo_lingua_indigena = $this->transformArrayInString($this->codigo_lingua_indigena);
        $this->poder_publico_parceria_convenio = $this->transformArrayInString($this->poder_publico_parceria_convenio);
        $this->formas_contratacao_parceria_escola_secretaria_estadual = $this->transformArrayInString($this->formas_contratacao_parceria_escola_secretaria_estadual);
        $this->formas_contratacao_parceria_escola_secretaria_municipal = $this->transformArrayInString($this->formas_contratacao_parceria_escola_secretaria_municipal);
    }

    private function transformArrayInString($value): ?string
    {
        return is_array($value) ? implode(separator: ',', array: array_filter($value)) : null;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_escola_lst.php');
        $this->pesquisaPessoaJuridica = false;

        if (!$this->validaCaracteresPermitidosComplemento()) {
            return false;
        }

        if (!$this->validaCnpjMantenedora()) {
            return false;
        }

        if (!$this->validaDigitosInepEscola(inep: $this->escola_inep_id, nomeCampo: 'Código INEP')) {
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

        if ($this->nao_ha_funcionarios_para_funcoes === null &&
            $this->validaRecursos() === false) {
            return false;
        }

        $this->validateManagersRules();

        if (!$this->validaDigitosInepEscolaCompartilhada()) {
            return false;
        }

        if (!$this->validaOpcoesUnicasMultipleSearch()) {
            return false;
        }

        $this->preparaDados();

        $this->bloquear_lancamento_diario_anos_letivos_encerrados = is_null($this->bloquear_lancamento_diario_anos_letivos_encerrados) ? 0 : 1;
        $this->utiliza_regra_diferenciada = !is_null($this->utiliza_regra_diferenciada);

        DB::beginTransaction();

        $obj = new clsPmieducarEscola(cod_escola: $this->cod_escola, ref_usuario_cad: null, ref_usuario_exc: $this->pessoa_logada, ref_cod_instituicao: $this->ref_cod_instituicao, zona_localizacao: $this->zona_localizacao, ref_idpes: $this->ref_idpes, sigla: $this->sigla, data_cadastro: null, data_exclusao: null, ativo: 1, bloquear_lancamento_diario_anos_letivos_encerrados: $this->bloquear_lancamento_diario_anos_letivos_encerrados, utiliza_regra_diferenciada: $this->utiliza_regra_diferenciada);

        $escola = $this->constroiObjetoEscola(pessoaj_id_oculto: $this->ref_idpes, escola: $obj);

        $edita = $escola->edita();

        if ($edita === false) {
            $this->mensagem = 'Edição não efetuada.<br>';

            return false;
        }

        $this->processaTelefones($this->ref_idpes);

        $this->saveAddress($this->ref_idpes);

        if (!$this->cadastraEscolaCurso(cod_escola: $this->cod_escola, excluirEscolaCursos: true)) {
            return false;
        }

        $this->storeManagers($this->cod_escola);

        $this->saveInep($this->cod_escola);

        $this->atualizaNomePessoaJuridica($this->ref_idpes);

        $this->atualizaEspacoEscolares($this->cod_escola);

        DB::commit();

        $this->mensagem = 'Edição efetuada com sucesso.<br>';

        throw new HttpResponseException(
            new RedirectResponse('educar_escola_lst.php')
        );
    }

    private function atualizaNomePessoaJuridica($idpes)
    {
        (new clsJuridica(idpes: $idpes, cnpj: null, fantasia: $this->fantasia))->edita();
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_escola_lst.php');
        $obj = new clsPmieducarEscola(cod_escola: $this->cod_escola, ref_usuario_cad: null, ref_usuario_exc: $this->pessoa_logada, ref_cod_instituicao: null, zona_localizacao: null, ref_idpes: null, sigla: null, data_cadastro: null, data_exclusao: null, ativo: null, bloquear_lancamento_diario_anos_letivos_encerrados: 0);
        $obj->detalhe();
        $excluiu = $obj->excluir();

        if ($excluiu === false) {
            $this->mensagem = 'Exclusão não realizada.<br>';

            return false;
        }

        $this->mensagem = 'Exclusão efetuada com sucesso.<br>';

        throw new HttpResponseException(
            new RedirectResponse('educar_escola_lst.php')
        );
    }

    protected function inputTelefone($type, $typeLabel = '')
    {
        if (!$typeLabel) {
            $typeLabel = "Telefone {$type}";
        }

        // ddd
        $options = [
            'required' => false,
            'label' => "(DDD) / {$typeLabel}",
            'placeholder' => 'DDD',
            'value' => $this->{"p_ddd_telefone_{$type}"},
            'max_length' => 3,
            'size' => 4,
            'inline' => true,
        ];
        $this->inputsHelper()->integer(attrName: "p_ddd_telefone_{$type}", inputOptions: $options);

        // telefone
        $options = [
            'required' => false,
            'label' => '',
            'placeholder' => $typeLabel,
            'value' => $this->{"p_telefone_{$type}"},
            'max_length' => 9,
        ];
        $this->inputsHelper()->integer(attrName: "p_telefone_{$type}", inputOptions: $options);
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
            $this->validaDigitosInepEscola(inep: $this->inep_escola_sede, nomeCampo: 'Código escola sede') &&
            $this->inepEscolaSedeDiferenteDaEscolaPrincipal() &&
            $this->validateCensusManagerRules() &&
            $this->validaEscolaCompartilhaPredio() &&
            $this->validaSalasUtilizadasDentroEscola() &&
            $this->validaSalasUtilizadasForaEscola() &&
            $this->validaSalasClimatizadas() &&
            $this->validaSalasAcessibilidade() &&
            $this->validaEquipamentosAcessoInternet() &&
            $this->validaQuantidadeComputadoresAlunos() &&
            $this->validaQuantidadeEquipamentosEnsino() &&
            $this->validaLinguasIndigenas() &&
            $this->validaFormasContratacaoParceriaEscolaSecretariaEstadual() &&
            $this->validaFormasContratacaoParceriaEscolaSecretariaMunicipal();
    }

    protected function validaFormasContratacaoParceriaEscolaSecretariaEstadual(): bool
    {
        $formasDeContratacao = $this->formas_contratacao_parceria_escola_secretaria_estadual;

        $acceptDependenciaAdministrativa = [DependenciaAdministrativaEscola::FEDERAL, DependenciaAdministrativaEscola::ESTADUAL, DependenciaAdministrativaEscola::MUNICIPAL];
        $notAcceptFormasDeContratoInDependenciaAdministrativa = [1, 2, 3, 4];
        if (is_array($formasDeContratacao) && in_array(needle: (int) $this->dependencia_administrativa, haystack: $acceptDependenciaAdministrativa, strict: true)) {

            $data = array_filter(array: $formasDeContratacao,
                callback: static fn ($forma) => in_array(needle: (int) $forma, haystack: $notAcceptFormasDeContratoInDependenciaAdministrativa, strict: true)
            );

            if (count($data) !== 0) {
                $this->mensagem = 'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria estadual de educação</b> foi preenchido incorretamente.';

                return false;
            }
        }

        $categoriaEscolaPrivadaLista = [2, 3, 4];
        $notAcceptFormasDeContratoInDependenciaAdministrativa = [5, 6];
        if (is_array($formasDeContratacao) && in_array(needle: (int) $this->categoria_escola_privada, haystack: $categoriaEscolaPrivadaLista, strict: true)) {
            $data = array_filter(array: $formasDeContratacao,
                callback: static fn ($forma) => in_array(needle: (int) $forma, haystack: $notAcceptFormasDeContratoInDependenciaAdministrativa, strict: true)
            );

            if (count($data) !== 0) {
                $this->mensagem = 'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria estadual de educação</b> foi preenchido incorretamente.';

                return false;
            }
        }

        if ($formasDeContratacao && (int) $this->categoria_escola_privada === 1) {

            if ($formasDeContratacao === null || !in_array(needle: 4, haystack: $formasDeContratacao)) {
                $this->mensagem = 'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"';

                return false;
            }

            if (count($formasDeContratacao) > 1) {
                $this->mensagem = 'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"';

                return false;
            }
        }

        return true;
    }

    protected function validaFormasContratacaoParceriaEscolaSecretariaMunicipal(): bool
    {
        $formasDeContratacao = $this->formas_contratacao_parceria_escola_secretaria_municipal;

        $acceptDependenciaAdministrativa = [DependenciaAdministrativaEscola::FEDERAL, DependenciaAdministrativaEscola::ESTADUAL, DependenciaAdministrativaEscola::MUNICIPAL];
        $notAcceptFormasDeContratoInDependenciaAdministrativa = [1, 2, 3, 4];
        if (is_array($formasDeContratacao) && in_array(needle: (int) $this->dependencia_administrativa, haystack: $acceptDependenciaAdministrativa, strict: true)) {

            $data = array_filter(array: $formasDeContratacao,
                callback: static fn ($forma) => in_array(needle: (int) $forma, haystack: $notAcceptFormasDeContratoInDependenciaAdministrativa, strict: true)
            );

            if (count($data) !== 0) {
                $this->mensagem = 'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria municipal de educação</b> foi preenchido incorretamente.';

                return false;
            }
        }

        $categoriaEscolaPrivadaLista = [2, 3, 4];
        $notAcceptFormasDeContratoInDependenciaAdministrativa = [5, 6];
        if (is_array($formasDeContratacao) && in_array(needle: (int) $this->categoria_escola_privada, haystack: $categoriaEscolaPrivadaLista, strict: true)) {
            $data = array_filter(array: $formasDeContratacao,
                callback: static fn ($forma) => in_array(needle: (int) $forma, haystack: $notAcceptFormasDeContratoInDependenciaAdministrativa, strict: true)
            );

            if (count($data) !== 0) {
                $this->mensagem = 'O campo <b>Forma(s) de contratação da parceria ou convênio entre a escola e a Secretaria municipal de educação</b> foi preenchido incorretamente.';

                return false;
            }
        }

        if ($formasDeContratacao && (int) $this->categoria_escola_privada === 1) {

            if ($formasDeContratacao === null || !in_array(needle: 4, haystack: $formasDeContratacao)) {
                $this->mensagem = 'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"';

                return false;
            }

            if (count($formasDeContratacao) > 1) {
                $this->mensagem = 'Quando o campo "Categoria da escola privada" for igual à "Particular" só é possível cadastrar "Contrato de prestação de serviço"';

                return false;
            }
        }

        return true;
    }

    protected function validaOcupacaoPredio()
    {
        if (is_array($this->local_funcionamento) && in_array(needle: LocalFuncionamento::PREDIO_ESCOLAR, haystack: $this->local_funcionamento) && empty($this->condicao)) {
            $this->mensagem = 'O campo: Forma de ocupação do prédio, deve ser informado quando o Local de funcionamento for prédio escolar.';

            return false;
        }

        return true;
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
        $cidyId = $this->city_id;

        if (empty($cidyId)) {
            $this->mensagem = 'Cidade não informada';

            return false;
        }

        $cityIBGE = City::query()
            ->whereKey($cidyId)
            ->get()
            ->pluck('ibge_code')
            ->first();

        $esferaAdministrativaValidator = (new AdministrativeDomainValidator(
            administrativeDomain: $this->esfera_administrativa,
            regulations: $this->regulamentacao,
            administrativeDependence: $this->dependencia_administrativa,
            cityIbgeCode: $cityIBGE
        ));

        if (!$esferaAdministrativaValidator->isValid()) {
            $this->mensagem = $esferaAdministrativaValidator->getMessage();

            return false;
        }

        return true;
    }

    protected function validaEscolaPrivada()
    {
        if ($this->dependencia_administrativa != '4' || $this->situacao_funcionamento != 1) {
            return true;
        }

        if (empty($this->categoria_escola_privada)) {
            $this->mensagem = 'O campo categoria da escola privada é obrigatório para escolas em atividade de administração privada.';

            return false;
        }

        if (empty($this->mantenedora_escola_privada) ||
            (is_array($this->mantenedora_escola_privada) &&
                count($this->mantenedora_escola_privada) == 1 &&
                empty($this->mantenedora_escola_privada[0]))) {
            $this->mensagem = 'O campo mantenedora da escola privada é obrigatório para escolas em atividade de administração privada.';

            return false;
        }

        return true;
    }

    protected function validaDadosTelefones()
    {
        return $this->validaDDDTelefone(valorDDD: $this->p_ddd_telefone_1, valorTelefone: $this->p_telefone_1, nomeCampo: 'Telefone 1') &&
            $this->validaTelefone(telefone: $this->p_telefone_1, nomeCampo: 'Telefone 1') &&
            $this->validaDDDTelefone(valorDDD: $this->p_ddd_telefone_2, valorTelefone: $this->p_telefone_2, nomeCampo: 'Telefone 2') &&
            $this->validaTelefone(telefone: $this->p_telefone_2, nomeCampo: 'Telefone 2') &&
            $this->validaDDDTelefone(valorDDD: $this->p_ddd_telefone_mov, valorTelefone: $this->p_telefone_mov, nomeCampo: 'Celular') &&
            $this->validaDDDTelefone(valorDDD: $this->p_ddd_telefone_fax, valorTelefone: $this->p_telefone_fax, nomeCampo: 'Fax') &&
            $this->validaTelefones(telefone1: $this->p_telefone_1, telefone2: $this->p_telefone_2);
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

    protected function validaDDDTelefone($valorDDD, $valorTelefone, $nomeCampo)
    {
        $msgRequereTelefone = "O campo: {$nomeCampo}, deve ser preenchido quando o DDD estiver preenchido.";
        $msgRequereDDD = "O campo: DDD, deve ser preenchido quando o {$nomeCampo} estiver preenchido.";
        $msgDDDInvalido = 'O campo: DDD, possui um valor inválido';
        $listDDDInvalidos = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 23, 25, 26, 29, 30, 36, 39, 40, 50, 52, 56, 57, 58, 59, 60, 70, 72, 76, 78, 80, 90];

        if (!empty($valorDDD) && empty($valorTelefone)) {
            $this->mensagem = $msgRequereTelefone;

            return false;
        }

        if (empty($valorDDD) && !empty($valorTelefone)) {
            $this->mensagem = $msgRequereDDD;

            return false;
        }

        if (!empty($valorDDD) && (strlen((int) $valorDDD) !== 2 || in_array(needle: (int) $valorDDD, haystack: $listDDDInvalidos))) {
            $this->mensagem = $msgDDDInvalido;

            return false;
        }

        return true;
    }

    protected function validaTelefone($telefone, $nomeCampo)
    {
        if (empty($telefone)) {
            return true;
        }

        $telefoneValidator = new Telefone(nomeCampo: $nomeCampo, valor: $telefone);
        if (!$telefoneValidator->isValid()) {
            $this->mensagem = implode(separator: '<br>', array: $telefoneValidator->getMessage());

            return false;
        }

        return true;
    }

    protected function validaDigitosInepEscola($inep, $nomeCampo)
    {
        if (str_starts_with(haystack: $inep, needle: '0')) {
            $this->mensagem = "O campo: {$nomeCampo} não pode iniciar com 0.";

            return false;
        }

        if (!empty($inep) && strlen($inep) != 8) {
            $this->mensagem = "O campo: {$nomeCampo} deve conter 8 dígitos.";

            return false;
        }

        return true;
    }

    protected function inepEscolaSedeDiferenteDaEscolaPrincipal()
    {
        if ($this->inep_escola_sede == $this->escola_inep_id) {
            $this->mensagem = 'O campo: Código da escola sede deve ser diferente do campo: Código INEP';

            return false;
        }

        return true;
    }

    protected function geraCamposCodigoInepEscolaCompartilhada()
    {
        $options = ['label_hint' => 'Caso compartilhe o prédio escolar com outra escola preencha com o código INEP',
            'required' => false, 'size' => 8, 'max_length' => 8, 'placeholder' => ''];

        for ($i = 1; $i <= 6; $i++) {
            $seq = $i == 1 ? '' : $i;
            $options['label'] = 'Código da escola que compartilha o prédio ' . $i;
            $campo = 'codigo_inep_escola_compartilhada' . $seq;
            $options['value'] = $this->$campo;
            $this->inputsHelper()->integer(attrName: 'codigo_inep_escola_compartilhada' . $seq, inputOptions: $options);
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
                $rows[] = $this->makeRowManagerTable(key: $key, schoolManager: $manager);
            }
        }

        $this->campoTabelaInicio(
            nome: 'gestores',
            titulo: 'Gestores escolares',
            arr_campos: [
                'INEP',
                'Nome do(a) gestor(a)',
                'Cargo do(a) gestor(a)',
                'Detalhes',
                'Principal',
            ],
            arr_valores: $rows
        );

        $this->campoTexto(nome: 'managers_inep_id', campo: null, valor: null, tamanhomaximo: 12);

        $this->inputsHelper()->simpleSearchServidor(attrName: null, inputOptions: ['required' => false]);
        $options = [
            'resources' => SelectOptions::schoolManagerRoles(),
            'required' => false,
        ];
        $this->inputsHelper()->select(attrName: 'managers_role_id', inputOptions: $options);
        $this->campoRotulo(nome: 'detalhes', campo: 'Detalhes', valor: '<a class="btn-detalhes" onclick="modalOpen(this)">Dados adicionais do(a) gestor(a)</a>');
        $this->campoOculto(nome: 'managers_access_criteria_id', valor: null);
        $this->campoOculto(nome: 'managers_link_type_id', valor: null);
        $this->campoOculto(nome: 'managers_email', valor: null);

        $resources = [
            0 => 'Não',
            1 => 'Sim',
        ];
        $options =
            [
                'resources' => $resources,
                'required' => false,
            ];
        $this->inputsHelper()->select(attrName: 'managers_chief', inputOptions: $options);

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
            $this->managers_chief[$key] ?? (int) $schoolManager->chief,
            $this->servidor_id[$key] ?? $schoolManager->employee_id,
            $this->managers_access_criteria_id[$key] ?? $schoolManager->access_criteria_id,
            $this->managers_link_type_id[$key] ?? $schoolManager->link_type_id,
            $this->managers_email[$key] ?? $schoolManager->individual->person->email,
        ];
    }

    /**
     * Salva os gestores da escola
     */
    protected function storeManagers($schoolId)
    {
        /** @var SchoolManagerService $schoolService */
        $schoolService = app(SchoolManagerService::class);
        $schoolService->deleteAllManagers($schoolId);
        foreach ($this->servidor_id as $key => $employeeId) {
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

            if ($this->managers_email[$key]) {
                $this->storeManagerEmail(employeeId: $employeeId, email: $this->managers_email[$key]);
            }

            if ($this->managers_inep_id[$key]) {
                $this->storeInepCode(employeeId: $employeeId, inepCode: $this->managers_inep_id[$key]);
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
                'managers_inep_id.*.size' => 'O campo: Código INEP do gestor(a) deve conter 12 dígitos',
            ]
        );
    }

    /**
     * Valida as regras do censo referentes aos gestores da escola
     *
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

        $managersValidator = new SchoolManagers(valueObject: $managers, administrativeDependency: $this->dependencia_administrativa, operatingSituation: $this->situacao_funcionamento);

        if (!$managersValidator->isValid()) {
            $this->mensagem = implode(separator: '<br>', array: $managersValidator->getMessage());

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

        if (in_array(needle: $this->escola_inep_id, haystack: $arrayCampos)) {
            $this->mensagem = 'O campo: Código da escola que compartilha o prédio 1, 2, 3, 4, 5 ou 6, deve ser diferente do Código INEP da escola atual.';

            return false;
        }

        $arrayCamposSemNulos = array_filter($arrayCampos);
        if (count(array_unique($arrayCamposSemNulos)) < count($arrayCamposSemNulos)) {
            $this->mensagem = 'Os códigos Inep\'s das escolas compartilhadas devem ser diferentes entre si.';

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
        if ($this->local_funcionamento != LocalFuncionamento::PREDIO_ESCOLAR) {
            return true;
        }

        if ($this->numero_salas_utilizadas_dentro_predio == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> não pode ser preenchido com 0';

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

        return true;
    }

    protected function validaSalasClimatizadas()
    {
        if ($this->numero_salas_climatizadas == '0') {
            $this->mensagem = 'O campo: <b>Número de salas de aula climatizadas</b> não pode ser preenchido com 0';

            return false;
        }

        $totalSalas = (int) $this->numero_salas_utilizadas_dentro_predio + (int) $this->numero_salas_utilizadas_fora_predio;
        if ((int) $this->numero_salas_climatizadas > $totalSalas) {
            $this->mensagem = 'O campo: <b>Número de salas de aula climatizadas</b> não pode ser maior que a soma dos campos: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> e <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b>';

            return false;
        }

        return true;
    }

    protected function validaCaracteresPermitidosComplemento()
    {
        if (empty($this->complement)) {
            return true;
        }
        $pattern = '/^[a-zA-Z0-9ªº\/–\ .,-]+$/';

        if (!preg_match(pattern: $pattern, subject: $this->complement)) {
            $this->mensagem = 'O campo foi preenchido com valor não permitido. O campo Complemento só permite os caracteres: ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789 ª º – / . ,';

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

        $totalSalas = (int) $this->numero_salas_utilizadas_dentro_predio + (int) $this->numero_salas_utilizadas_fora_predio;
        if ((int) $this->numero_salas_acessibilidade > $totalSalas) {
            $this->mensagem = 'O campo: <b>Número de salas de aula com acessibilidade para pessoas com deficiência ou mobilidade reduzida</b> não pode ser maior que a soma dos campos: <b>Número de salas de aula utilizadas na escola dentro do prédio escolar</b> e <b>Número de salas de aula utilizadas na escola fora do prédio escolar</b>';

            return false;
        }

        return true;
    }

    protected function validaOpcoesUnicasMultipleSearch()
    {
        if (is_array($this->poder_publico_parceria_convenio) && in_array(needle: PoderPublicoConveniado::NAO_POSSUI, haystack: $this->poder_publico_parceria_convenio) && count($this->poder_publico_parceria_convenio) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições</b>, quando a opção: <b>Não possui parceria ou convênio</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->abastecimento_agua) && in_array(needle: AbastecimentoAgua::INEXISTENTE, haystack: $this->abastecimento_agua) && count($this->abastecimento_agua) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Abastecimento de água</b>, quando a opção: <b>Não há abastecimento de água</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->abastecimento_energia) && in_array(needle: FonteEnergia::INEXISTENTE, haystack: $this->abastecimento_energia) && count($this->abastecimento_energia) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Fonte de energia elétrica</b>, quando a opção: <b>Não há energia elétrica</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->esgoto_sanitario) && in_array(needle: EsgotamentoSanitario::INEXISTENTE, haystack: $this->esgoto_sanitario) && count($this->esgoto_sanitario) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Esgotamento sanitário</b>, quando a opção: <b>Não há esgotamento sanitário</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->tratamento_lixo) && in_array(needle: TratamentoLixo::NAO_FAZ, haystack: $this->tratamento_lixo) && count($this->tratamento_lixo) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Tratamento do lixo/resíduos que a escola realiza</b>, quando a opção: <b>Não faz tratamento</b> estiver selecionada';

            return false;
        }

        if (is_array($this->recursos_acessibilidade) && in_array(needle: RecursosAcessibilidade::NENHUM, haystack: $this->recursos_acessibilidade) && count($this->recursos_acessibilidade) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Recursos de acessibilidade</b>, quando a opção: <b>Nenhum dos recursos de acessibilidade</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->equipamentos) && in_array(needle: Equipamentos::NENHUM_EQUIPAMENTO_LISTADO, haystack: $this->equipamentos) && count($this->equipamentos) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Equipamentos da escola</b>, quando a opção: <b>Nenhum dos equipamentos listados</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->rede_local) && in_array(needle: RedeLocal::NENHUMA, haystack: $this->rede_local) && count($this->rede_local) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Rede local de interligação de computadores</b>, quando a opção: <b>Não há rede local interligando computadores</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->uso_internet) && in_array(needle: UsoInternet::NAO_POSSUI, haystack: $this->uso_internet) && count($this->uso_internet) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Acesso à internet</b>, quando a opção: <b>Não possui acesso à internet</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->orgaos_colegiados) && in_array(needle: OrgaosColegiados::NENHUM, haystack: $this->orgaos_colegiados) && count($this->orgaos_colegiados) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Órgãos colegiados em funcionamento na escola</b>, quando a opção: <b>Não há órgãos colegiados em funcionamento</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->reserva_vagas_cotas) && in_array(needle: ReservaVagasCotas::NAO_POSSUI, haystack: $this->reserva_vagas_cotas) && count($this->reserva_vagas_cotas) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Reserva de vagas por sistema de cotas para grupos específicos de alunos(as)</b>, quando a opção: <b>Sem reservas de vagas para sistema de cotas (ampla concorrência)</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->instrumentos_pedagogicos) && in_array(needle: InstrumentosPedagogicos::NENHUM_DOS_INSTRUMENTOS_LISTADOS, haystack: $this->instrumentos_pedagogicos) && count($this->instrumentos_pedagogicos) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino aprendizagem</b>, quando a opção: <b>Nenhum dos instrumentos listados</b> estiver selecionada.';

            return false;
        }

        if (is_array($this->acoes_area_ambiental) && in_array(needle: AcoesAmbientais::NENHUMA_DAS_ACOES_LISTADAS, haystack: $this->acoes_area_ambiental) && count($this->acoes_area_ambiental) > 1) {
            $this->mensagem = 'Não é possível informar mais de uma opção no campo: <b>Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola</b>, quando a opção: <b>Nenhuma das opções listadas</b> estiver selecionada.';

            return false;
        }

        return true;
    }

    protected function validaEquipamentosAcessoInternet()
    {
        if (is_array($this->equipamentos_acesso_internet) && in_array(needle: 2, haystack: $this->equipamentos_acesso_internet) &&
            is_array($this->rede_local) && !in_array(needle: 3, haystack: $this->rede_local)) {
            $this->mensagem = 'O campo: <b>Equipamentos que os aluno(a)s usam para acessar a internet da escola</b> não deve ser preenchido com a opção: <b>Dispositivos pessoais (computadores portáteis, celulares, tablets, etc.)</b> quando o campo: <b>Rede local de interligação de computadores</b> não possuir a opção: <b>Wireless</b> selecionada.';

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
            }

            if ((int) $this->{$key} > 0) {
                $algumCampoPreenchido = true;
            }
        }

        if ($algumCampoPreenchido) {
            return true;
        }

        $this->mensagem = 'Preencha pelo menos um dos campos <b>da seção Quantidade de profissionais</b> da aba Recursos.';

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

        if (is_array($this->equipamentos_acesso_internet) && in_array(needle: EquipamentosAcessoInternet::COMPUTADOR_MESA, haystack: $this->equipamentos_acesso_internet) && $quantidadesNaoPreenchidas) {
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
        DB::table('modules.educacenso_cod_escola')->where(column: 'cod_escola', operator: $schoolId)
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

    private function atualizaEspacoEscolares($cod_escola)
    {
        $espacoEscolares = $this->espaco_escolar_nome ? array_filter($this->espaco_escolar_nome) : null;

        if (!empty($cod_escola)) {
            SchoolSpace::query()
                ->when($this->espaco_escolar_id, fn ($q, $values) => $q->whereNotIn('id', array_filter($values)))
                ->where('school_id', $cod_escola)
                ->delete();
        }

        if (empty($espacoEscolares)) {
            return;
        }

        foreach ($espacoEscolares as $key => $value) {
            $id = $this->espaco_escolar_id[$key];
            if (!empty($id)) {
                SchoolSpace::query()
                    ->whereKey($id)
                    ->where('school_id', $cod_escola)
                    ->update([
                        'name' => $this->espaco_escolar_nome[$key],
                        'size' => $this->espaco_escolar_tamanho[$key],
                    ]);
            } else {
                SchoolSpace::create([
                    'name' => $this->espaco_escolar_nome[$key],
                    'size' => $this->espaco_escolar_tamanho[$key],
                    'school_id' => $cod_escola,
                ]);
            }
        }
    }

    protected function validaLinguasIndigenas()
    {
        if (is_array($this->codigo_lingua_indigena) && count($this->codigo_lingua_indigena) > 3) {
            $this->mensagem = 'O campo: <b>Línguas indígenas</b>, não pode ter mais que 3 opções';

            return false;
        }

        return true;
    }

    public function Formular()
    {
        $this->title = 'Escola';
        $this->processoAp = '561';
    }
};
