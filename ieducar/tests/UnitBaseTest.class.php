<?php

/**
 * UnitBaseTest abstract class.
 *
 * Muda o diretório atual para que os testes possam ser facilmente invocados
 * em qualquer subdiretório do sistema.
 *
 * Abstrai o PHPUnit, diminuindo a dependência de seu uso.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @since   1.0.1
 * @version SVN: $Id$
 */

chdir(realpath(dirname(__FILE__) . '/../') . '/intranet');
require_once 'PHPUnit/Framework.php';

abstract class UnitBaseTest extends PHPUnit_Framework_TestCase {}