<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'Portabilis/Utils/CustomLabel.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Customiza&ccedil;&atilde;o de labels');
    $this->processoAp = 9998869;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;
  var $ref_cod_instituicao;
  var $custom_labels;

  function Inicializar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998869, $this->pessoa_logada, 7, 'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $this->breadcrumb('Customização de labels', [
        url('intranet/educar_configuracoes_index.php') => 'Configurações',
    ]);

    return 'Editar';
  }

  function Gerar()
  {


    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao);
    $configuracoes = $configuracoes->detalhe();

    $this->custom_labels = $configuracoes['custom_labels'];

    $customLabel = new CustomLabel();
    $defaults = $customLabel->getDefaults();
     ksort($defaults);
    $rotulo = null;
    foreach($defaults as $k => $v) {
        $rotulo2 = explode('.', $k)[0];

        if ($rotulo2 != $rotulo) {
            $rotulo2 = ucfirst($rotulo2);
            $this->campoRotulo($rotulo2, '<strong>' . $rotulo2 . '</strong>');
        }
        $this->inputsHelper()->text('custom_labels[' . $k . ']', array(
            'label' => $k,
            'size' => 100,
            'required' => false,
            'placeholder' => $v,
            'value' => (!empty($this->custom_labels[$k])) ? $this->custom_labels[$k] : ''
        ));
    }
  }

  function Editar()
  {


    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, array(
        'custom_labels' => $this->custom_labels
    ));

    $detalheAntigo = $configuracoes->detalhe();
    $editou = $configuracoes->edita();

    if ($editou) {
      $detalheAtual = $configuracoes->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("configuracoes_gerais", $this->pessoa_logada, $ref_cod_instituicao ? $ref_cod_instituicao : 'null');
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
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
