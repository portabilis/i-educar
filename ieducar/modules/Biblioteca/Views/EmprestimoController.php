<?php
/**
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   Biblioteca
 * @subpackage  Modules
 *
 * @since     Arquivo disponível desde a versão ?
 *
 * @version   $Id$
 */

require_once 'Portabilis/Controller/Page/ListController.php';

class EmprestimoController extends Portabilis_Controller_Page_ListController
{
    /**
     * @var string
     */
    protected $_dataMapper = '';

    /**
     * @var string
     */
    protected $_titulo = 'Emprestimo';

    /**
     * @var array
     */
    protected $_formMap = [];

    /**
     * @var int
     */
    protected $_processoAp = 610;

    protected function _preRender()
    {
        @session_start();
        $pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(610, $pessoa_logada, 7, '/intranet/educar_biblioteca_index.php');

        parent::_preRender();

        Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_biblioteca_index.php' => 'Biblioteca',
            '' => 'Empr&eacute;stimo de exemplares'
        ]);
        $this->enviaLocalizacao($localizacao->montar(), true);
    }

    public function Gerar()
    {
        // inputs
        $this->inputsHelper()->dynamic('instituicao', ['id' => 'instituicao_id']);
        $this->inputsHelper()->dynamic('escola', ['id' => 'escola_id']);
        $this->inputsHelper()->dynamic('biblioteca', ['id' => 'biblioteca_id']);
        $this->campoNumero('tombo_exemplar', 'Tombo exemplar', '', 13, 13, true);

        $helperOptions = ['hiddenInputOptions' => ['id' => 'cliente_id']];
        $this->inputsHelper()->dynamic('bibliotecaPesquisaCliente', [], $helperOptions);

        // assets
        $this->loadResourceAssets($this->getDispatcher());
    }
}
