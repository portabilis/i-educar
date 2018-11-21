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
 * @package     App_Date
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'App/Date/Utils.php';

/**
 * App_Date_UtilsTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     App_Date
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class App_Date_UtilsTest extends PHPUnit\Framework\TestCase
{
  public function testDatesYearAtLeast()
  {
    $dates = array(
      '01/01/2000',
      '01/02/2000'
    );

    try {
      App_Date_Utils::datesYearAtLeast($dates, 2001, 1);
      $this->fail('::datesYearAtLeast() deveria lançar App_Date_Exception.');
    }
    catch (App_Date_Exception $e) {
      $this->assertEquals(
        'Ao menos "1" das datas informadas deve ser do ano "2001". Datas: "01/01/2000", "01/02/2000".',
        $e->getMessage(),
        ''
      );
    }

    $this->assertTrue(
      App_Date_Utils::datesYearAtLeast($dates, 2000, 2),
      '::datesYearAtLeast() retorna "TRUE" quando uma das datas é do ano esperado.'
    );
  }
}