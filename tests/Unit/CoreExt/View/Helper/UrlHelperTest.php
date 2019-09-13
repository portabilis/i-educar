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
 * @package     CoreExt_View
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * CoreExt_View_UrlHelperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_View
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_View_UrlHelperTest extends PHPUnit\Framework\TestCase
{
  protected function setUp(): void
  {
    CoreExt_View_Helper_UrlHelper::setBaseUrl('');
  }

  public function testCriaUrlRelativa()
  {
    $expected = 'index.php';
    $this->assertEquals($expected, CoreExt_View_Helper_UrlHelper::url('index.php'));
  }

  public function testCriaUrlRelativaComQuerystring()
  {
    $expected = 'index.php?param1=value1';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array('query' => array('param1' => 'value1'))
      )
    );
  }

  public function testCriaUrlRelativaComFragmento()
  {
    $expected = 'index.php#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array('fragment' => 'fragment')
      )
    );
  }

  public function testCriaUrlRelativaComQuerystringEFragmento()
  {
    $expected = 'index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment'
        )
      )
    );
  }

  public function testCriaUrlAbsolutaComHostnameConfigurado()
  {
    CoreExt_View_Helper_UrlHelper::setBaseUrl('localhost');
    $expected = 'http://localhost/index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment',
          'absolute' => TRUE
        )
      )
    );
  }

  public function testCriaUrlAbsolutaComHostnameImplicito()
  {
    $expected = 'http://localhost/index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://localhost/index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment',
        )
      )
    );
  }

  public function testUrlRetornaApenasSchemeEHost()
  {
    $expected = 'http://www.example.com';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://www.example.com/controller/name',
        array(
          'absolute' => TRUE,
          'components' => CoreExt_View_Helper_UrlHelper::URL_SCHEME +
            CoreExt_View_Helper_UrlHelper::URL_HOST
        )
      )
    );
  }

  public function testUrlRetornaComPath()
  {
    $expected = 'http://www.example.com/controller';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://www.example.com/controller',
        array(
          'absolute' => TRUE,
          'components' => CoreExt_View_Helper_UrlHelper::URL_PATH
        )
      )
    );
  }

  public function testCriaLinkComUrlRelativa()
  {
    $expected = '<a href="index.php?param1=value1">Index</a>';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::l(
        'Index',
        'index.php',
        array('query' => array('param1' => 'value1'))
      )
    );
  }

  public function testCriaLinkComUrlAbsolutaImplicita()
  {
    $expected = '<a href="http://localhost/index.php?param1=value1">Index</a>';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::l(
        'Index',
        'http://localhost/index.php',
        array('query' => array('param1' => 'value1'))
      )
    );
  }
}
