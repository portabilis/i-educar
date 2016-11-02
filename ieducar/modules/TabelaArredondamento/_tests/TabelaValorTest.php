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
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'RegraAvaliacao/Model/Nota/TipoValor.php';

/**
 * TabelaValorTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TabelaValorTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new TabelaArredondamento_Model_TabelaValor();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaValorDataMapper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    $tabelaNumerica = new TabelaArredondamento_Model_Tabela(array(
      'nome' => 'foo',
      'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
    ));

    $tabelaConceitual = new TabelaArredondamento_Model_Tabela(array(
      'nome' => 'bar',
      'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL
    ));

    // Usa a instância recém criaca
    $this->_entity->tabelaArredondamento = $tabelaNumerica;

    // Asserção para nota numérica
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Numeric', $validators['nome']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
    $this->assertTrue(!isset($validators['descricao']));

    // Asserção para nota conceitual
    $this->_entity->tabelaArredondamento = $tabelaConceitual;
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_String',  $validators['nome']);
    $this->assertType('CoreExt_Validate_String',  $validators['descricao']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
  }
}