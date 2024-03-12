<?php

use iEducar\Reports\Contracts\TeacherReportCard;
use Illuminate\Support\Facades\Cache;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $permite_relacionamento_posvendas;

    public $url_novo_educacao;

    public $token_novo_educacao;

    public $mostrar_codigo_inep_aluno;

    public $justificativa_falta_documentacao_obrigatorio;

    public $tamanho_min_rede_estadual;

    public $modelo_boletim_professor;

    public $url_cadastro_usuario;

    public $active_on_ieducar;

    public $ieducar_image;

    public $ieducar_entity_name;

    public $ieducar_login_footer;

    public $ieducar_external_footer;

    public $ieducar_internal_footer;

    public $facebook_url;

    public $twitter_url;

    public $linkedin_url;

    public $ieducar_suspension_message;

    public $bloquear_cadastro_aluno;

    public $situacoes_especificas_atestados;

    public $emitir_ato_autorizativo;

    public $emitir_ato_criacao_credenciamento;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();

        $nivel = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        if ($nivel != 1) {
            $this->simpleRedirect(url: 'educar_index.php');
        }

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 999873,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);

        $this->breadcrumb(currentPage: 'Configurações gerais', breadcrumbs: [
            url(path: 'intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return 'Editar';
    }

    public function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);

        $configuracoes = new clsPmieducarConfiguracoesGerais(ref_cod_instituicao: $ref_cod_instituicao);
        $configuracoes = $configuracoes->detalhe();

        $this->permite_relacionamento_posvendas = $configuracoes['permite_relacionamento_posvendas'];
        $this->bloquear_cadastro_aluno = dbBool(val: $configuracoes['bloquear_cadastro_aluno']);
        $this->situacoes_especificas_atestados = dbBool(val: $configuracoes['situacoes_especificas_atestados']);
        $this->url_novo_educacao = $configuracoes['url_novo_educacao'];
        $this->token_novo_educacao = $configuracoes['token_novo_educacao'];
        $this->mostrar_codigo_inep_aluno = $configuracoes['mostrar_codigo_inep_aluno'];
        $this->justificativa_falta_documentacao_obrigatorio = $configuracoes['justificativa_falta_documentacao_obrigatorio'];
        $this->tamanho_min_rede_estadual = $configuracoes['tamanho_min_rede_estadual'];
        $this->modelo_boletim_professor = $configuracoes['modelo_boletim_professor'];
        $this->url_cadastro_usuario = $configuracoes['url_cadastro_usuario'];
        $this->active_on_ieducar = $configuracoes['active_on_ieducar'];
        $this->ieducar_image = $configuracoes['ieducar_image'];
        $this->ieducar_entity_name = $configuracoes['ieducar_entity_name'];
        $this->ieducar_login_footer = $configuracoes['ieducar_login_footer'];
        $this->ieducar_external_footer = $configuracoes['ieducar_external_footer'];
        $this->ieducar_internal_footer = $configuracoes['ieducar_internal_footer'];
        $this->facebook_url = $configuracoes['facebook_url'];
        $this->twitter_url = $configuracoes['twitter_url'];
        $this->linkedin_url = $configuracoes['linkedin_url'];
        $this->ieducar_suspension_message = $configuracoes['ieducar_suspension_message'];
        $this->emitir_ato_autorizativo = dbBool(val: $configuracoes['emitir_ato_autorizativo']);
        $this->emitir_ato_criacao_credenciamento = dbBool(val: $configuracoes['emitir_ato_criacao_credenciamento']);

        $this->inputsHelper()->checkbox(attrName: 'permite_relacionamento_posvendas', inputOptions: [
            'label' => 'Permite relacionamento direto no pós-venda?',
            'value' => $this->permite_relacionamento_posvendas ? 'on' : '',
        ]);

        $this->inputsHelper()->checkbox(attrName: 'bloquear_cadastro_aluno', inputOptions: [
            'label' => 'Bloquear o cadastro de novos alunos',
            'value' => $this->bloquear_cadastro_aluno ? 'on' : '',
        ]);

        $this->inputsHelper()->checkbox(attrName: 'situacoes_especificas_atestados', inputOptions: [
            'label' => 'Exibir apenas matrículas em situações específicas para os atestados',
            'value' => $this->situacoes_especificas_atestados ? 'on' : '',
        ]);

        $this->inputsHelper()->checkbox(attrName: 'emitir_ato_autorizativo', inputOptions: [
            'label' => 'Emite ato autorizativo nos cabeçalhos de histórico escolar (modelos padrão)',
            'value' => $this->emitir_ato_autorizativo ? 'on' : '',
        ]);

        $this->inputsHelper()->checkbox(attrName: 'emitir_ato_criacao_credenciamento', inputOptions: [
            'label' => 'Emite lei de criação e credenciamento nos cabeçalhos de histórico escolar (modelos padrão)',
            'value' => $this->emitir_ato_criacao_credenciamento ? 'on' : '',
        ]);

        $this->inputsHelper()->text(attrNames: 'url_novo_educacao', inputOptions: [
            'label' => 'URL de integração (API)',
            'size' => 100,
            'max_length' => 100,
            'required' => false,
            'placeholder' => 'Ex: http://cliente.provedor.com.br/api/v1/',
            'value' => $this->url_novo_educacao,
        ]);

        $this->inputsHelper()->text(attrNames: 'token_novo_educacao', inputOptions: [
            'label' => 'Token de integração (API)',
            'size' => 100,
            'max_length' => 100,
            'required' => false,
            'value' => $this->token_novo_educacao,
        ]);

        $options = [
            'label' => 'Mostrar código INEP nas telas de cadastro de aluno?',
            'value' => $this->mostrar_codigo_inep_aluno,
            'required' => true,
        ];
        $this->inputsHelper()->booleanSelect(attrName: 'mostrar_codigo_inep_aluno', inputOptions: $options);

        $options = [
            'label' => 'Campo "Justificativa para a falta de documentação" no cadastro de alunos deve ser obrigatório?',
            'value' => $this->justificativa_falta_documentacao_obrigatorio,
            'required' => true,
        ];
        $this->inputsHelper()->booleanSelect(attrName: 'justificativa_falta_documentacao_obrigatorio', inputOptions: $options);

        $this->inputsHelper()->integer(attrName: 'tamanho_min_rede_estadual', inputOptions: [
            'label' => 'Tamanho mínimo do campo "Código rede estadual" no cadastro de alunos ',
            'label_hint' => 'Deixe vazio no caso de não ter limite mínino',
            'max_length' => 3,
            'required' => false,
            'placeholder' => '',
            'value' => $this->tamanho_min_rede_estadual,
        ]);

        $teacherReporcCard = app(TeacherReportCard::class);
        $options = [
            'label' => 'Modelo do boletim do professor',
            'resources' => $teacherReporcCard->getOptions(),
            'value' => $this->modelo_boletim_professor,
        ];

        $this->inputsHelper()->select(attrName: 'modelo_boletim_professor', inputOptions: $options);

        $this->inputsHelper()->text(attrNames: 'url_cadastro_usuario', inputOptions: [
            'label' => 'URL da ferramenta de cadastro de usuários',
            'label_hint' => 'Deixe vazio para desabilitar a ferramenta',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://login.ieducar.com.br/cliente',
            'value' => $this->url_cadastro_usuario,
        ]);

        $this->inputsHelper()->booleanSelect(attrName: 'active_on_ieducar', inputOptions: [
            'label' => 'Ativo no i-educar?',
            'value' => $this->active_on_ieducar,
            'required' => true,
        ]);

        $this->inputsHelper()->text(attrNames: 'ieducar_suspension_message', inputOptions: [
            'label' => 'Mensagem de suspensão',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_suspension_message,
        ]);

        $this->inputsHelper()->text(attrNames: 'ieducar_image', inputOptions: [
            'label' => 'URL do logo',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_image,
        ]);

        $this->inputsHelper()->text(attrNames: 'ieducar_entity_name', inputOptions: [
            'label' => 'Nome da entidade',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_entity_name,
        ]);

        $this->inputsHelper()->textArea(attrName: 'ieducar_login_footer', inputOptions: [
            'label' => 'Rodapé do login',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_login_footer,
        ]);

        $this->inputsHelper()->textArea(attrName: 'ieducar_external_footer', inputOptions: [
            'label' => 'Rodapé externo',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_external_footer,
        ]);

        $this->inputsHelper()->textArea(attrName: 'ieducar_internal_footer', inputOptions: [
            'label' => 'Rodapé interno',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_internal_footer,
        ]);

        $this->inputsHelper()->text(attrNames: 'facebook_url', inputOptions: [
            'label' => 'Facebook',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://www.facebook.com/nome',
            'value' => $this->facebook_url,
        ]);

        $this->inputsHelper()->text(attrNames: 'twitter_url', inputOptions: [
            'label' => 'Twitter',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://twitter.com/nome',
            'value' => $this->twitter_url,
        ]);

        $this->inputsHelper()->text(attrNames: 'linkedin_url', inputOptions: [
            'label' => 'LinkedIn',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: https://www.linkedin.com/company/nome/',
            'value' => $this->linkedin_url,
        ]);
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $ref_cod_instituicao = $obj_permissoes->getInstituicao(int_idpes_usuario: $this->pessoa_logada);
        $permiteRelacionamentoPosvendas = ($this->permite_relacionamento_posvendas == 'on' ? 1 : 0);
        $bloquearCadastroAluno = $this->bloquear_cadastro_aluno == 'on' ? 1 : 0;
        $situacoesEspecificasAtestados = $this->situacoes_especificas_atestados == 'on' ? 1 : 0;
        $emitir_ato_autorizativo = $this->emitir_ato_autorizativo == 'on' ? 1 : 0;
        $emitir_ato_criacao_credenciamento = $this->emitir_ato_criacao_credenciamento == 'on' ? 1 : 0;

        $configuracoes = new clsPmieducarConfiguracoesGerais(ref_cod_instituicao: $ref_cod_instituicao, campos: [
            'permite_relacionamento_posvendas' => $permiteRelacionamentoPosvendas,
            'bloquear_cadastro_aluno' => $bloquearCadastroAluno,
            'situacoes_especificas_atestados' => $situacoesEspecificasAtestados,
            'url_novo_educacao' => $this->url_novo_educacao,
            'token_novo_educacao' => $this->token_novo_educacao,
            'mostrar_codigo_inep_aluno' => $this->mostrar_codigo_inep_aluno,
            'justificativa_falta_documentacao_obrigatorio' => $this->justificativa_falta_documentacao_obrigatorio,
            'tamanho_min_rede_estadual' => $this->tamanho_min_rede_estadual,
            'modelo_boletim_professor' => $this->modelo_boletim_professor,
            'url_cadastro_usuario' => $this->url_cadastro_usuario,
            'active_on_ieducar' => $this->active_on_ieducar,
            'ieducar_image' => $this->ieducar_image,
            'ieducar_entity_name' => $this->ieducar_entity_name,
            'ieducar_login_footer' => $this->ieducar_login_footer,
            'ieducar_external_footer' => $this->ieducar_external_footer,
            'ieducar_internal_footer' => $this->ieducar_internal_footer,
            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'linkedin_url' => $this->linkedin_url,
            'ieducar_suspension_message' => $this->ieducar_suspension_message,
            'emitir_ato_autorizativo' => $emitir_ato_autorizativo,
            'emitir_ato_criacao_credenciamento' => $emitir_ato_criacao_credenciamento,
        ]);

        $editou = $configuracoes->edita();

        if ($editou) {
            // Reseta o cache de configurações
            Cache::invalidateByTags(['configurations']);

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'index.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Configurações gerais';
        $this->processoAp = 999873;
    }
};
