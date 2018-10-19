<?php
require_once 'Portabilis/Controller/Page/ListController.php';

class ReservaController extends Portabilis_Controller_Page_ListController
{
    /**
     * @var Avaliacao_Model_NotaAlunoDataMapper
     */
    protected $_dataMapper = '';

    /**
     * @var string
     */
    protected $_titulo     = 'Reserva';

    /**
     * @var array
     */
    protected $_formMap    = [];

    /**
     * @var int
     */
    protected $_processoAp = 609;

    protected function _preRender()
    {
        parent::_preRender();

        Portabilis_View_Helper_Application::loadStylesheet(
            $this,
            'intranet/styles/localizacaoSistema.css'
        );

        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            'educar_biblioteca_index.php' => 'Biblioteca',
            '' => 'Reserva de exemplares'
        ]);
        $this->enviaLocalizacao($localizacao->montar(), true);
    }

    public function Gerar()
    {
        // inputs
        $inputs = [
            'instituicao',
            'escola',
            'biblioteca',
            'bibliotecaPesquisaCliente',
            'bibliotecaPesquisaObra'
        ];
        
        $this->inputsHelper()->dynamic($inputs);

        // assets
        $this->loadResourceAssets($this->getDispatcher());
    }
}
