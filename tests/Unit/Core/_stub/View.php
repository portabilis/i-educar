<?php


/**
 * Core_ViewStub class.
 *
 * Stub para Core_View, substituindo alguns métodos para evitar conexão com
 * o banco de dados e a geração de código HTML.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Core_View
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Core_ViewStub extends Core_View
{
    /**
     * @see clsBase#MakeAll()
     */
    public function MakeAll()
    {
        $this->Formular();

        return true;
    }
}
