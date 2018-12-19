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
 * @package     Core
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'clsCalendario.inc.php';

/**
 * ClsCalendarioTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Ied_Include
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.2.0
 * @todo        Mover para diretório Ied
 * @version     @@package_version@@
 */
class ClsCalendarioTest extends PHPUnit\Framework\TestCase
{
  public function testGenerateFormValues()
  {
    $formValues = array(
      'formFieldKey' => 'formFieldValue'
    );

    $calendario = new clsCalendario();

    // Teste sem permissão de troca de ano
    $html = $calendario->getCalendario(1, 2000, 'testGenerateFormValues', array(), $formValues);

    $this->assertRegExp(
      '/<input id="cal_formFieldKey" name="formFieldKey" type="hidden" value="formFieldValue" \/>/',
      $html, '->getCalendario() gera campos extras de formulário.'
    );
  }
}