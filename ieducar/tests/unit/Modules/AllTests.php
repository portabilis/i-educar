<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Modules
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

/**
 * CoreExt_AllTests class.
 *
 * Arquivo de defini��o de su�te para o pacote CoreExt (nova API).
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Modules
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Modules_AllTests extends TestCollector
{
  protected $_name = 'Su�te de testes unit�rios de Modules';
  protected $_file = __FILE__;

  public static function suite()
  {
    $instance = new self;
    $instance->blacklistDirectories();
    $instance->addDirectory('../modules/*');
    return $instance->addDirectoryTests();
  }

  public function blacklistDirectories()
  {
    $directories = new DirectoryIterator(PROJECT_ROOT . DS . 'modules');
    foreach ($directories as $directory) {
      if (!$directory->isDot() && $directory->isDir()) {
        $path = $directory->getPathname() . DS . '_tests';
        if (is_dir($path)) {
          PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist($path, '.php');
        }
      }
    }
  }
}