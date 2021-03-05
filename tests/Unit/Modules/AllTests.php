<?php


/**
 * Avaliacao_AllTests class.
 *
 * Arquivo de definição de suíte para o módulo.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  Tests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Avaliacao_AllTests extends TestCollector
{
    protected $_name = 'Suíte de testes do módulo Avaliacao';
    protected $_file = __FILE__;

    public static function suite()
    {
        $instance = new self;

        return $instance->addDirectoryTests();
    }
}
