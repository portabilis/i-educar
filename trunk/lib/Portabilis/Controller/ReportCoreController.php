<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Controller
 * @subpackage  Portabilis
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'lib/Portabilis/View/Helper/DynamicSelectMenus.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';

require_once 'include/pmieducar/clsPermissoes.inc.php';

/**
 * Portabilis_Controller_ReportCoreController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Controller
 * @subpackage  Portabilis
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Portabilis_Controller_ReportCoreController extends Core_Controller_Page_EditController
{

  // setado qualquer dataMapper pois é obrigatório.
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp = 624;

  protected $_titulo     = 'Relat&oacute;rio';

  public function __construct() {
    $this->validatesIfUserIsLoggedIn();

    $this->validationErrors   = array();

    // clsCadastro settings
    $this->acao_executa_submit           = false;
    $this->acao_enviar                   = 'printReport()';

    // educar_campo_lista settings
    $this->verificar_campos_obrigatorios = true;
    $this->add_onchange_events           = true;
    // include 'include/pmieducar/educar_campo_lista.php';

    $this->dynamicSelectMenus = new Portabilis_View_Helper_DynamicSelectMenus($this);
    parent::__construct();
  }


  public function Gerar() {
    if (count($_POST) < 1) {
      $this->appendFixups();
      $this->renderForm();
    }
    else {
      $this->report = $this->report();

      $this->beforeValidation();
      $this->validatesPresenseOfRequiredArgsInReport();
      $this->aftervalidation();

      if (count($this->validationErrors) > 0)
        $this->onValidationError();
      else
        $this->renderReport();

    }
  }


  function headers() {
    header('Content-type: application/pdf');
    //header("Content-Disposition: attachment; filename={$templateName}.pdf");
    header("Content-Disposition: inline; filename={$templateName}.pdf");
  }


  function renderForm() {
    $this->form();
    $this->nome_url_sucesso = "Exibir";
  }


  function renderReport() {
    try {
      echo $this->report->dumps();
      $this->headers();
    }
    catch (Exception $e) {
      if ($this->report->reportFactory()->settings['show_exceptions_msg'])
        $details = 'Detalhes: ' . $e->getMessage();
      else
        $details = "Visualização dos detalhes sobre o erro desativada.";

      $this->renderError($details);
    }
  }


  // methods that must be overridden

  function form() {
    throw new Exception("The method 'form' must be overridden!");
  }


  // metodo executado após validação com sucesso (antes de imprimir) , como os argumentos ex: $this->addArg('id', 1); $this->addArg('id_2', 2);
  function beforeValidation() {
    throw new Exception("The method 'beforeValidation' must be overridden!");
  }


  // methods that can be overridden

  function afterValidation() {
    //colocar aqui as validacoes serverside, exemplo se histórico possui todos os campos...
    //retornar dict msgs, se nenhuma msg entao esta validado ex: $this->addValidationError('O cadastro x esta em y status');
  }



  protected function validatesIfUserIsLoggedIn() {
    if (! $this->getSession()->id_pessoa)
      header('Location: logof.php');
  }


  function addValidationError($message) {
    $this->validationErrors[] = array('message' => utf8_encode($message));
  }

  function validatesPresenseOfRequiredArgsInReport() {
    foreach($this->report->requiredArgs as $requiredArg) {

      if (! isset($this->report->args[$requiredArg]) || trim($this->report->args[$requiredArg]) == '')
        $this->addValidationError('Informe um valor no campo "' . $requiredArg . '"');
    }
  }

  function onValidationError() {
    $msg = 'O relatório não pode ser emitido, dica(s):\n\n';
    foreach ($this->validationErrors as $e) {
      $msg .= '- ' . $e['message'] . '\n';
    }
    $msg .= '\nPor favor, verifique esta(s) situação(s) e tente novamente.';
    $msg = "<script type='text/javascript'>alert('$msg'); close();</script> ";
    print utf8_decode($msg);
  }


  function renderError($details = "") {
    $msg = 'Ocorreu um erro ao emitir o relatorio.\n\n' . $details;
    $msg = "<script type='text/javascript'>alert('$msg'); close();</script> ";

    print utf8_decode($msg);
  }


  /* permite adicionar filtros ao formulário de emissão do relatório, sem precisar
     chamar diretamente $this->dynamicSelectMenus->helperFor nem passsar um array
     contendo um array de options.

     ex, subscrever metodo setForm para chamar:

     $this->addFilterFor('instituicao', array('required' => false)); ou
     $this->addFilterFor(array('instituicao', 'escola', 'pesquisaAluno'));
  */
  function addFilterFor($filterNames, $inputOptions = array(), $options = array()) {
    // se receber $inputOptions e $options['options'] ignora $inputOptions e usa $options['options']
    $defaultOptions = array('options' => $inputOptions);
    $options        = Portabilis_Array_Utils::merge($options, $defaultOptions);

    $this->dynamicSelectMenus->helperFor($filterNames, $options);
  }


  function appendFixups() {
    $js = <<<EOT

<script type="text/javascript">
  function printReport() {
    if (validatesPresenseOfValueInRequiredFields()) {
      document.formcadastro.target = '_blank';
      document.formcadastro.submit();
    }

    document.getElementById( 'btn_enviar' ).disabled = false;
  }
</script>

EOT;
    $this->appendOutput($js);
  }
}
