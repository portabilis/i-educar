<?php

require_once 'include/clsBase.inc.php';

class Core_View extends clsBase
{
    /**
     * Uma instância de CoreExt_Controller_Page_Interface.
     *
     * @var CoreExt_Controller_Page_Interface
     */
    protected $_pageController = null;

    /**
     * Construtor.
     *
     * @param Core_Controller_Page_Interface $instance
     */
    public function __construct(Core_Controller_Page_Interface $instance)
    {
        parent::__construct();

        $this->_setPageController($instance);
    }

    /**
     * Setter.
     *
     * @param Core_Controller_Page_Interface $instance
     *
     * @return Core_View Provê interface fluída
     */
    protected function _setPageController(Core_Controller_Page_Interface $instance)
    {
        $this->_pageController = $instance;

        return $this;
    }

    /**
     * Getter.
     *
     * @return CoreExt_Controller_Page_Interface
     */
    protected function _getPageController()
    {
        return $this->_pageController;
    }

    /**
     * Setter
     *
     * @param string $titulo
     *
     * @return Core_View Provê interface fluída
     */
    public function setTitulo($titulo)
    {
        parent::SetTitulo($titulo);

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Setter.
     *
     * @param int $processo
     *
     * @return Core_View Provê interface fluída
     */
    public function setProcessoAp($processo)
    {
        $this->processoAp = (int) $processo;

        return $this;
    }

    /**
     * Getter.
     *
     * @return int
     */
    public function getProcessoAp()
    {
        return $this->processoAp;
    }

    /**
     * Configura algumas variáveis de instância.
     *
     * @see clsBase#Formular()
     */
    public function Formular()
    {
        $instituicao = config('legacy.app.template.vars.instituicao');

        $this->setTitulo($instituicao . ' | ' . $this->_getPageController()->getBaseTitulo())
            ->setProcessoAp($this->_getPageController()->getBaseProcessoAp());
    }

    /**
     * Executa o método de geração de HTML para a classe.
     *
     * @param Core_View $instance
     *
     * @throws Exception
     */
    public static function generate($instance)
    {
        $viewBase = new self($instance);
        $viewBase->addForm($instance);
        $viewBase->MakeAll();
    }
}
