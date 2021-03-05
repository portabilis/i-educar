<?php


/**
 * Avaliacao_Service_NotaConceitualSituacaoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Avaliacao_Service_NotaConceitualSituacaoTest extends Avaliacao_Service_NotaSituacaoCommon
{
    protected function setUp(): void
    {
        $this->_setRegraOption('tipoNota', RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL);
        parent::setUp();
    }
}
