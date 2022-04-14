<?php

use Illuminate\Support\Facades\Cache;

return new class extends clsCadastro {
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

        $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel != 1) {
            $this->simpleRedirect('educar_index.php');
        }

        $obj_permissoes->permissao_cadastra(
            999873,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $this->breadcrumb('Configurações gerais', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return 'Editar';
    }

    public function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao);
        $configuracoes = $configuracoes->detalhe();

        $this->permite_relacionamento_posvendas = $configuracoes['permite_relacionamento_posvendas'];
        $this->bloquear_cadastro_aluno = dbBool($configuracoes['bloquear_cadastro_aluno']);
        $this->situacoes_especificas_atestados = dbBool($configuracoes['situacoes_especificas_atestados']);
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
        $this->emitir_ato_autorizativo = dbBool($configuracoes['emitir_ato_autorizativo']);
        $this->emitir_ato_criacao_credenciamento = dbBool($configuracoes['emitir_ato_criacao_credenciamento']);

        $this->inputsHelper()->checkbox('permite_relacionamento_posvendas', [
            'label' => 'Permite relacionamento direto no pós-venda?',
            'value' => $this->permite_relacionamento_posvendas ? 'on' : ''
        ]);

        $this->inputsHelper()->checkbox('bloquear_cadastro_aluno', [
            'label' => 'Bloquear o cadastro de novos alunos',
            'value' => $this->bloquear_cadastro_aluno ? 'on' : ''
        ]);

        $this->inputsHelper()->checkbox('situacoes_especificas_atestados', [
            'label' => 'Exibir apenas matrículas em situações específicas para os atestados',
            'value' => $this->situacoes_especificas_atestados ? 'on' : ''
        ]);

        $this->inputsHelper()->checkbox('emitir_ato_autorizativo', [
            'label' => 'Emite ato autorizativo nos cabeçalhos de histórico escolar (modelos padrão)',
            'value' => $this->emitir_ato_autorizativo ? 'on' : ''
        ]);

        $this->inputsHelper()->checkbox('emitir_ato_criacao_credenciamento', [
            'label' => 'Emite lei de criação e credenciamento nos cabeçalhos de histórico escolar (modelos padrão)',
            'value' => $this->emitir_ato_criacao_credenciamento ? 'on' : ''
        ]);

        $this->inputsHelper()->text('url_novo_educacao', [
            'label' => 'URL de integração (API)',
            'size' => 100,
            'max_length' => 100,
            'required' => false,
            'placeholder' => 'Ex: http://cliente.provedor.com.br/api/v1/',
            'value' => $this->url_novo_educacao
        ]);

        $this->inputsHelper()->text('token_novo_educacao', [
            'label' => 'Token de integração (API)',
            'size' => 100,
            'max_length' => 100,
            'required' => false,
            'value' => $this->token_novo_educacao
        ]);

        $options = [
            'label' => 'Mostrar código INEP nas telas de cadastro de aluno?',
            'value' => $this->mostrar_codigo_inep_aluno,
            'required' => true,
        ];
        $this->inputsHelper()->booleanSelect('mostrar_codigo_inep_aluno', $options);

        $options = [
            'label' => 'Campo "Justificativa para a falta de documentação" no cadastro de alunos deve ser obrigatório?',
            'value' => $this->justificativa_falta_documentacao_obrigatorio,
            'required' => true,
        ];
        $this->inputsHelper()->booleanSelect('justificativa_falta_documentacao_obrigatorio', $options);

        $this->inputsHelper()->integer('tamanho_min_rede_estadual', [
            'label' => 'Tamanho mínimo do campo "Código rede estadual" no cadastro de alunos ',
            'label_hint' => 'Deixe vazio no caso de não ter limite mínino',
            'max_length' => 3,
            'required' => false,
            'placeholder' => '',
            'value' => $this->tamanho_min_rede_estadual
        ]);

        $options = [
            'label' => 'Modelo do boletim do professor',
            'resources' => [
                1 => _cl('report.boletim_professor.modelo_padrao'),
                2 => _cl('report.boletim_professor.modelo_recuperacao_por_etapa'),
                3 => _cl('report.boletim_professor.modelo_recuperacao_paralela'),
            ],
            'value' => $this->modelo_boletim_professor
        ];
        $this->inputsHelper()->select('modelo_boletim_professor', $options);

        $this->inputsHelper()->text('url_cadastro_usuario', [
            'label' => 'URL da ferramenta de cadastro de usuários',
            'label_hint' => 'Deixe vazio para desabilitar a ferramenta',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://login.ieducar.com.br/cliente',
            'value' => $this->url_cadastro_usuario
        ]);

        $this->inputsHelper()->booleanSelect('active_on_ieducar', [
            'label' => 'Ativo no EducaSis?',
            'value' => $this->active_on_ieducar,
            'required' => true,
        ]);

        $this->inputsHelper()->text('ieducar_suspension_message', [
            'label' => 'Mensagem de suspensão',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_suspension_message
        ]);

        $this->inputsHelper()->text('ieducar_image', [
            'label' => 'URL do logo',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_image
        ]);

        $this->inputsHelper()->text('ieducar_entity_name', [
            'label' => 'Nome da entidade',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_entity_name
        ]);

        $this->inputsHelper()->textArea('ieducar_login_footer', [
            'label' => 'Rodapé do login',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_login_footer
        ]);

        $this->inputsHelper()->textArea('ieducar_external_footer', [
            'label' => 'Rodapé externo',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_external_footer
        ]);

        $this->inputsHelper()->textArea('ieducar_internal_footer', [
            'label' => 'Rodapé interno',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_internal_footer
        ]);

        $this->inputsHelper()->text('facebook_url', [
            'label' => 'Facebook',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://www.facebook.com/nome',
            'value' => $this->facebook_url
        ]);

        $this->inputsHelper()->text('twitter_url', [
            'label' => 'Twitter',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://twitter.com/nome',
            'value' => $this->twitter_url
        ]);

        $this->inputsHelper()->text('linkedin_url', [
            'label' => 'LinkedIn',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: https://www.linkedin.com/company/nome/',
            'value' => $this->linkedin_url
        ]);
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        $permiteRelacionamentoPosvendas = ($this->permite_relacionamento_posvendas == 'on' ? 1 : 0);
        $bloquearCadastroAluno = $this->bloquear_cadastro_aluno == 'on' ? 1 : 0;
        $situacoesEspecificasAtestados = $this->situacoes_especificas_atestados == 'on' ? 1 : 0;
        $emitir_ato_autorizativo = $this->emitir_ato_autorizativo == 'on' ? 1 : 0;
        $emitir_ato_criacao_credenciamento = $this->emitir_ato_criacao_credenciamento == 'on' ? 1 : 0;

        $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, [
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

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('index.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Configura&ccedil;&otilde;es gerais';
        $this->processoAp = 999873;
    }
};
