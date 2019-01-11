<?php

require_once 'CoreExt/Controller/Page/Interface.php';

interface Core_Controller_Page_Interface extends CoreExt_Controller_Page_Interface
{
    /**
     * Setter.
     *
     * @param CoreExt_DataMapper|string $dataMapper
     *
     * @return Core_Controller_Page_Interface Provê interface fluída
     */
    public function setDataMapper($dataMapper);

    /**
     * Retorna uma instância CoreExt_DataMapper.
     *
     * @return CoreExt_DataMapper
     */
    public function getDataMapper();

    /**
     * Setter.
     *
     * @param string $titulo
     *
     * @return Core_Controller_Page_Interface Provê interface fluída
     */
    public function setBaseTitulo($titulo);

    /**
     * Retorna uma string para o título da página.
     *
     * @return string
     */
    public function getBaseTitulo();

    /**
     * Setter.
     *
     * @param int $processoAp
     *
     * @return Core_Controller_Page_Interface Provê interface fluída
     */
    public function setBaseProcessoAp($processoAp);

    /**
     * Retorna o código de processo para autorização de acesso ao usuário.
     *
     * @return int
     */
    public function getBaseProcessoAp();
}
