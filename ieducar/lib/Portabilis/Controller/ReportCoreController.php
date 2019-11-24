<?php

use iEducar\Modules\ErrorTracking\TrackerFactory;

require_once 'Core/Controller/Page/EditController.php';
require_once 'lib/Portabilis/View/Helper/Inputs.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';

class Portabilis_Controller_ReportCoreController extends Core_Controller_Page_EditController
{
    /**
     * Setado qualquer Data Mapper pois é obrigatório.
     *
     * @var string
     */
    protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';

    /**
     * Código de permissão da página index, por padrão todos usuários tem
     * permissão.
     *
     * @var int
     */
    protected $_processoAp = 624;

    /**
     * @var string
     */
    protected $_titulo = 'Relatório';

    /**
     * @var Portabilis_Report_ReportCore
     */
    protected $report;

    /**
     * Portabilis_Controller_ReportCoreController constructor.
     */
    public function __construct()
    {
        $this->validatesIfUserIsLoggedIn();

        $this->validationErrors = [];

        $this->acao_executa_submit = false;
        $this->acao_enviar = 'printReport()';

        header('Content-Type: text/html; charset=utf-8');

        parent::__construct();
    }

    /**
     * Método padrão da clsCadastro.
     *
     * @see clsCadastro::Gerar()
     *
     * @return bool|void
     *
     * @throws Exception
     */
    public function Gerar()
    {
        if (count($_POST) < 1 && !isset($_GET['print_report_with_get'])) {
            $this->appendFixups();
            $this->renderForm();
        } else {
            $this->report = $this->report();

            $this->beforeValidation();

            $this->report->addArg('database', config('legacy.app.database.dbname'));
            $this->report->addArg('SUBREPORT_DIR', config('legacy.report.source_path'));
            $this->report->addArg('data_emissao', (int) config('legacy.report.header.show_data_emissao'));

            $this->validatesPresenseOfRequiredArgsInReport();
            $this->aftervalidation();

            if (count($this->validationErrors) > 0) {
                $this->onValidationError();
            } else {
                $this->renderReport();
            }
        }
    }

    /**
     * Altera os headers que irão na resposta da requisição.
     *
     * @param string $result
     *
     * @return void
     */
    public function headers($result)
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/pdf;');
        header('Content-Disposition: inline;');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($result));
    }

    /**
     * Renderiza o formulário de filtragem.
     *
     * @return void
     *
     * @throws Exception
     */
    public function renderForm()
    {
        $this->form();
        $this->nome_url_sucesso = 'Exibir';
    }

    /**
     * Renderiza o relatório.
     *
     * @return void
     *
     * @throws Exception
     */
    public function renderReport()
    {
        try {
            $result = $this->report->dumps();

            if (!$result) {
                throw new Exception('No report result to render!');
            }

            $this->headers($result);

            ob_clean();
            flush();

            echo $result;
            die();
        } catch (Exception $e) {
            if (config('legacy.modules.error.track')) {
                $tracker = TrackerFactory::getTracker(config('legacy.modules.error.tracker_name'));
                $tracker->notify($e);
            }

            $nivelUsuario = (new clsPermissoes)->nivel_acesso($this->getSession()->id_pessoa);

            if ((bool) config('legacy.report.show_error_details') === true || (int) $nivelUsuario === 1) {
                $details = 'Detalhes: ' . $e->getMessage();
            } else {
                $details = 'Visualização dos detalhes sobre o erro desativada.';
            }

            $this->renderError($details);
        }
    }

    /**
     * Monta o formulário de filtragem.
     *
     * @return void
     *
     * @throws Exception
     */
    public function form()
    {
        throw new Exception('The method \'form\' must be overridden!');
    }

    /**
     * Método executado após validar com sucesso (antes de imprimir), os
     * argumentos.
     *
     *  <code>
     *      $this->addArg('id', 1);
     *      $this->addArg('id_2', 2);
     *  </code>
     *
     * @return void
     *
     * @throws Exception
     */
    public function beforeValidation()
    {
        throw new Exception('The method \'beforeValidation\' must be overridden!');
    }

    /**
     * Colocar aqui as validacoes serverside, exemplo se histórico possui todos
     * os campos. Retornar mensagens, se não existe nenhuma mensagem, então
     * está validado.
     *
     *  <code>
     *      $this->addValidationError('O cadastro x esta em y status');
     *  </code>
     *
     * @return void
     */
    public function afterValidation()
    {
    }

    /**
     * Valida se o usuário está logado, caso contrário redireciona para a
     * página de logoff.
     *
     * @return void
     */
    protected function validatesIfUserIsLoggedIn()
    {
        if (!$this->getSession()->id_pessoa) {
            $this->simpleRedirect('logof.php');
        }
    }

    /**
     * Adiciona uma mensagem de erro de validação.
     *
     * @param string $message
     *
     * @return void
     */
    public function addValidationError($message)
    {
        $this->validationErrors[] = [
            'message' => $message
        ];
    }

    /**
     * Valida se todos os parâmetros obrigatórios foram informados.
     *
     * @return void
     */
    public function validatesPresenseOfRequiredArgsInReport()
    {
        foreach ($this->report->requiredArgs as $requiredArg) {
            if (!isset($this->report->args[$requiredArg]) || empty($this->report->args[$requiredArg])) {
                $this->addValidationError('Informe um valor no campo "' . $requiredArg . '"');
            }
        }
    }

    /**
     * Exibe mensagem em caso de erro.
     *
     * @return void
     */
    public function onValidationError()
    {
        $msg = Portabilis_String_Utils::toLatin1('O relatório não pode ser emitido, dica(s):') . '\n\n';

        foreach ($this->validationErrors as $e) {
            $error = $e['message'];
            $msg .= '- ' . $error . '\n';
        }

        $msg .= '\n' . Portabilis_String_Utils::toLatin1('Por favor, verifique esta(s) situação(s) e tente novamente.');

        $msg = Portabilis_String_Utils::toLatin1($msg, ['escape' => false]);
        echo "<script type='text/javascript'>alert('$msg'); close();</script> ";
    }

    /**
     * Renderiza uma mensagem de erro.
     *
     * @param string $details
     *
     * @return void
     */
    public function renderError($details = '')
    {
        $details = Portabilis_String_Utils::escape($details);
        $msg = Portabilis_String_Utils::toLatin1('Ocorreu um erro ao emitir o relatório.') . '\n\n' . $details;

        $msg = Portabilis_String_Utils::toLatin1($msg, ['escape' => false]);
        $msg = "<script type='text/javascript'>alert('$msg'); close();</script>";

        echo $msg;
    }

    /**
     * Carrega assets utilizados.
     *
     * @param object $dispatcher
     *
     * @return void
     */
    protected function loadResourceAssets($dispatcher)
    {
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        $controllerName = ucwords($dispatcher->getControllerName());
        $actionName = ucwords($dispatcher->getActionName());

        $style = "/modules/$controllerName/Assets/Stylesheets/$actionName.css";
        $script = "/modules/$controllerName/Assets/Javascripts/$actionName.js";

        if (file_exists($rootPath . $style)) {
            Portabilis_View_Helper_Application::loadStylesheet($this, $style);
        }

        if (file_exists($rootPath . $script)) {
            Portabilis_View_Helper_Application::loadJavascript($this, $script);
        }
    }

    /**
     * Adiciona JavaScript a página.
     *
     * @return void
     */
    public function appendFixups()
    {
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
