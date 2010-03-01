<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Modules
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

/**
 * CoreExt_AllTests class.
 *
 * Arquivo de definição de suíte para o pacote CoreExt (nova API).
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Modules
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Modules_AllTests extends TestCollector
{
  protected $_name = 'Suíte de testes unitários de Modules';
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
          PHPUnit_Util_Filter::addDirectoryToFilter($path, '.php');
        }
      }
    }
  }
}