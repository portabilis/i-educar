<?php

require_once "include_paths.php";
require_once "include/clsBase.inc.php";
require_once "include/clsCadastro.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once "portabilis/dal.php";
require_once "lib/Portabilis/View/Helper/DynamicSelectMenus.php";
require_once 'lib/Portabilis/Array/Utils.php';

class clsIndexBase extends clsBase
{
  function __construct()
  {
    /* código usado para verificação de autorização de acesso a página,
       ex, setar em instancia de Report: $instancia->page->processoAp = "123";
       por padrão usa código da página de apresentação (index) do ieducar, para que seja exibido o menu.
    */
		$this->processoAp = "624";

    parent::__construct();
  }
}

class RemoteReportFactory
{

  function __construct($settings)
  {
    $this->settings = $settings;
  }

  function build()
  {
    throw new Exception("The method 'build' from class RemoteReportFactory must be overridden!");
  }

}

class RemoteReportJasperFactory extends RemoteReportFactory
{
  function build($templateName = '', $args = array())
  {
    if (! trim($templateName))
      throw new Exception("The attribute 'templateName' must be defined!");

    require_once 'include/portabilis/libs/XML/RPC2/Client.php';

    if (! count($args))
      $args['fake_arg'] = '';

    $client = XML_RPC2_Client::create($this->settings['url']);
    $result = $client->build_report_jasper($app_name = $this->settings['app_name'],
                                           $template_name = $templateName,
                                           $username = $this->settings['username'],
                                           $password = $this->settings['password'],
                                           $args = $args);

      header('Content-type: application/pdf');
      //header("Content-Disposition: attachment; filename={$templateName}.pdf");
      header("Content-Disposition: inline; filename={$templateName}.pdf");

      return base64_decode($result['report']);
  }
}

class Report extends clsCadastro
{

  function __construct($name, $templateName, $addLogoNameToArgs = True)
  {

		@session_start();
		$this->_user_id = $_SESSION['id_pessoa'];
		@session_write_close();

    if (! $this->_user_id)
      header('Location: logof.php');

    $this->db = new Db();

    $config = $GLOBALS['coreExt']['Config']->report->remote_factory;

    $this->reportFactorySettings = array();
    $this->reportFactorySettings['url'] = $config->url;
    $this->reportFactorySettings['app_name'] = $config->this_app_name;
    $this->reportFactorySettings['username'] = $config->username;
    $this->reportFactorySettings['password'] = $config->password;
    $this->reportFactorySettings['show_exceptions_msg'] = $config->show_exceptions_msg;

    $this->reportFactory = new RemoteReportJasperFactory($settings = $this->reportFactorySettings);

    $this->name = $name;
    $this->templateName = $templateName;
    $this->args = array();

    $this->page = new clsIndexBase();
    $this->validationErrors = array();
    $this->requiredFields = array();

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = True;
    $this->add_onchange_events = True;

    $this->acao_executa_submit = false;
    $this->acao_enviar = 'printReport()';

    if ($addLogoNameToArgs)
    {
      if (! $config->logo_name)
        throw new Exception("Invalid logo_name, please check the ini file");
      $this->addArg('logo_name', $config->logo_name);
    }

    $this->dynamicSelectMenus = new Portabilis_View_Helper_DynamicSelectMenus($this);
  }

  function render()
  {
    if (! count($_POST))
    {
      $this->appendFixups();
      $this->renderForm();
    }
    else
    {
      $this->autoValidate();
      $this->validate();
      if (count($this->validationErrors) > 0)
        $this->onValidationError();
      else
      {
        $this->onValidationSuccess();
        $this->renderReport();
      }
    }
  }

  function renderForm()
  {
    $this->setForm();
    $this->nome_url_sucesso = "Exibir";
    $this->page->SetTitulo('Relat&oacute;rio - ' . $this->name);
    $this->page->addForm($this);
    $this->page->MakeAll();
  }

  function renderReport()
  {
    try
    {
      echo $this->reportFactory->build($templateName = $this->templateName, $args = $this->args);
    }
    catch (Exception $e)
    {

      if ($this->reportFactorySettings['show_exceptions_msg'])
        $details = "<div id='detail'><p><strong>Detalhes:</strong> {$e->getMessage()}</p></div>";
      else
        $details = "<div id='detail'><p>Visualização dos detalhes sobre o erro desativada.</p></div>";

      echo "<html><head><link rel='stylesheet' type='text/css' href='styles/reset.css'><link rel='stylesheet' type='text/css' href='styles/portabilis.css'><link rel='stylesheet' type='text/css' href='styles/min-portabilis.css'></head>";
      echo "<body><div id='error'><h1>Relatório não emitido</h1><p class='explication'>Descupe-nos ocorreu algum erro na geração do relatório, <strong>por favor tente novamente mais tarde</strong></p><ul class='unstyled'><li><a href='/intranet/index.php'>- Voltar para o sistema</a></li><li>- Tentou mais de uma vez e o erro persiste? Por favor, <a target='_blank' href='http://www.portabilis.com.br/site/suporte'>solicite suporte</a> ou envie um email para suporte@portabilis.com.br</li></ul>$details</div></body></html>";
    }
  }

  function setForm()
  {
    throw new Exception("The method 'setForm' from class Report must be overridden!");
  }

  function setTemplateName($name)
  {
    $this->templateName = $name;
  }

  function addArg($name, $value)
  {
    if (is_string($value))
      $value = utf8_encode($value);

    $this->args[$name] = $value;
  }

  function addValidationError($message)
  {
    $this->validationErrors[] = array('message' => utf8_encode($message));
  }

  function addRequiredField($name, $label = '')
  {
    if (! $label)
      $label = $name;

    $this->requiredFields[] = array('name' => $name, 'label' => $label);
  }


  function addRequiredFields($fieldsList)
  {
    //adiciona uma lista (array de arrays) de fields requiridos
    //ex: $this->addRequiredFields(array(array('ref_cod_curso', 'curso'), array('ref_cod_escola', 'escola')));

    if (! is_array($fieldsList))
      throw new Exception("Invalid type for arg 'fieldsList'");

    foreach ($fieldsList as $f)
    {
      if (! isset($f[1]))
        $f[] = $f[0];

      $this->requiredFields[] = array('name' => $f[0], 'label' => $f[1]);
    }
  }


  function autoValidate($method = 'post')
  {

    foreach ($this->requiredFields as $f)
    {
      if ($method == 'post')
        $dict = $_POST;
      elseif($method == 'get')
        $dict = $_GET;
      else
        throw new Exception('Invalid method!');

      if (! isset($dict[$f['name']]) || ! trim($dict[$f['name']]))
        $this->addValidationError('Informe um valor no campo "' . $f['label'] . '"');

    }
  }


  function validate()
  {
    //colocar aqui as validacoes serverside, exemplo se histórico possui todos os campos...
    //retornar dict msgs, se nenhuma msg entao esta validado ex: $this->addValidationError('O cadastro x esta em y status');
  }


  function onValidationSuccess()
  {
    //defina aqui operacoes apos o sucesso da validacao (antes de imprimir) , como os argumentos ex: $this->addArg('id', 1); $this->addArg('id_2', 2);
    throw new Exception("The method 'onValidationSuccess' from class Report must be overridden!");
  }


  function onValidationError()
  {
    $msg = 'O relatório não pode ser emitido, dica(s):\n\n';
    foreach ($this->validationErrors as $e)
    {
      $msg .= '- ' . $e['message'] . '\n';
    }
    $msg .= '\nPor favor, verifique esta(s) situação(s) e tente novamente.';
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


  function appendFixups()
  {
    $js = <<<EOT

<script type="text/javascript">
  function printReport()
  {
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
?>
