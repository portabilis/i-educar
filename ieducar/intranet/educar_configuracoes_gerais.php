<?php

use Illuminate\Support\Facades\Cache;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Configura&ccedil;&otilde;es gerais');
    $this->processoAp = 999873;
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $permite_relacionamento_posvendas;
  var $url_novo_educacao;
  var $mostrar_codigo_inep_aluno;
  var $justificativa_falta_documentacao_obrigatorio;
  var $tamanho_min_rede_estadual;
  var $modelo_boletim_professor;
  var $url_cadastro_usuario;
  var $active_on_ieducar;
  var $ieducar_image;
  var $ieducar_entity_name;
  var $ieducar_login_footer;
  var $ieducar_external_footer;
  var $ieducar_internal_footer;
  var $facebook_url;
  var $twitter_url;
  var $linkedin_url;
  var $ieducar_suspension_message;
  var $bloquear_cadastro_aluno;

  function Inicializar()
  {
    

    $obj_permissoes = new clsPermissoes();

    $nivel = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel != 1) {
      $this->simpleRedirect('educar_index.php');
    }

    $obj_permissoes->permissao_cadastra(999873, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_configuracoes_index.php"                  => "Configurações",
         ""                                  => "Configura&ccedil;&otilde;es gerais"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return 'Editar';
  }

  function Gerar()
  {
    

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao);
    $configuracoes = $configuracoes->detalhe();

    $this->permite_relacionamento_posvendas = $configuracoes['permite_relacionamento_posvendas'];
    $this->bloquear_cadastro_aluno = dbBool($configuracoes['bloquear_cadastro_aluno']);
    $this->url_novo_educacao = $configuracoes['url_novo_educacao'];
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

    $this->inputsHelper()->checkbox('permite_relacionamento_posvendas', array(
        'label' => 'Permite relacionamento direto no pós-venda?',
        'value' => $this->permite_relacionamento_posvendas
    ));

    $this->inputsHelper()->checkbox('bloquear_cadastro_aluno', array(
        'label' => 'Bloquear o cadastro de novos alunos',
        'value' => $this->bloquear_cadastro_aluno
    ));

    $this->inputsHelper()->text('url_novo_educacao', array(
        'label' => 'URL de integração (API)',
        'size' => 100,
        'max_length' => 100,
        'required' => false,
        'placeholder' => 'Ex: http://cliente.provedor.com.br/api/v1/',
        'value' => $this->url_novo_educacao
    ));

    $options = array('label' => 'Mostrar código INEP nas telas de cadastro de aluno?',
        'value' => $this->mostrar_codigo_inep_aluno,
        'required' => true,
    );
    $this->inputsHelper()->booleanSelect('mostrar_codigo_inep_aluno', $options);

    $options = array('label' => 'Campo "Justificativa para a falta de documentação" no cadastro de alunos deve ser obrigatório?',
        'value' => $this->justificativa_falta_documentacao_obrigatorio,
        'required' => true,
    );
    $this->inputsHelper()->booleanSelect('justificativa_falta_documentacao_obrigatorio', $options);

    $this->inputsHelper()->integer('tamanho_min_rede_estadual',
        array(
          'label' => 'Tamanho mínimo do campo "Código rede estadual" no cadastro de alunos ',
          'label_hint' => 'Deixe vazio no caso de não ter limite mínino',
          'max_length' => 3,
          'required' => false,
          'placeholder' => '',
          'value' => $this->tamanho_min_rede_estadual
        )
    );

    $options = array(
        'label' => 'Modelo do boletim do professor',
        'resources' => array(
            1 => 'Modelo padrão',
            2 => 'Modelo recuperação por etapa',
            3 => 'Modelo recuperação paralela',
        ),
        'value' => $this->modelo_boletim_professor
    );
    $this->inputsHelper()->select('modelo_boletim_professor', $options);

    $this->inputsHelper()->text('url_cadastro_usuario',
        array(
            'label' => 'URL da ferramenta de cadastro de usuários',
            'label_hint' => 'Deixe vazio para desabilitar a ferramenta',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://login.ieducar.com.br/cliente',
            'value' => $this->url_cadastro_usuario
        )
    );

    $this->inputsHelper()->booleanSelect('active_on_ieducar', array(
        'label' => 'Ativo no i-educar?',
        'value' => $this->active_on_ieducar,
        'required' => true,
    ));

    $this->inputsHelper()->text('ieducar_suspension_message',
        array(
            'label' => 'Mensagem de suspensão',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_suspension_message
        )
    );

    $this->inputsHelper()->text('ieducar_image',
        array(
            'label' => 'URL do logo',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_image
        )
    );

    $this->inputsHelper()->text('ieducar_entity_name',
        array(
            'label' => 'Nome da entidade',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'value' => $this->ieducar_entity_name
        )
    );

    $this->inputsHelper()->textArea('ieducar_login_footer',
        array(
            'label' => 'Rodapé do login',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_login_footer
        )
    );

    $this->inputsHelper()->textArea('ieducar_external_footer',
        array(
            'label' => 'Rodapé externo',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_external_footer
        )
    );

    $this->inputsHelper()->textArea('ieducar_internal_footer',
        array(
            'label' => 'Rodapé interno',
            'size' => 100,
            'rows' => 3,
            'required' => false,
            'value' => $this->ieducar_internal_footer
        )
    );

    $this->inputsHelper()->text('facebook_url',
        array(
            'label' => 'Facebook',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://www.facebook.com/nome',
            'value' => $this->facebook_url
        )
    );

    $this->inputsHelper()->text('twitter_url',
        array(
            'label' => 'Twitter',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: http://twitter.com/nome',
            'value' => $this->twitter_url
        )
    );

    $this->inputsHelper()->text('linkedin_url',
        array(
            'label' => 'LinkedIn',
            'size' => 100,
            'max_length' => 255,
            'required' => false,
            'placeholder' => 'Ex: https://www.linkedin.com/company/nome/',
            'value' => $this->linkedin_url
        )
    );
  }

  function Editar()
  {
    

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
    $permiteRelacionamentoPosvendas = ($this->permite_relacionamento_posvendas == 'on' ? 1 : 0);
    $bloquearCadastroAluno = $this->bloquear_cadastro_aluno == 'on' ? 1 : 0;

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, array(
        'permite_relacionamento_posvendas' => $permiteRelacionamentoPosvendas,
        'bloquear_cadastro_aluno' => $bloquearCadastroAluno,
        'url_novo_educacao' => $this->url_novo_educacao,
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
        'ieducar_suspension_message' => $this->ieducar_suspension_message
    ));

    $detalheAntigo = $configuracoes->detalhe();
    $editou = $configuracoes->edita();

    if( $editou )
    {
      $detalheAtual = $configuracoes->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("configuracoes_gerais", $this->pessoa_logada, $ref_cod_instituicao ? $ref_cod_instituicao : 'null');
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

      // Reseta o cache de configurações
      Cache::invalidateByTags(['configurations']);

      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      $this->simpleRedirect('index.php');
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    return false;
  }

}
// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
