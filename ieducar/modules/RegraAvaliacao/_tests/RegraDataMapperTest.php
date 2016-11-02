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
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

/**
 * RegraDataMapperTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class RegraDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new RegraAvaliacao_Model_RegraDataMapper();
  }

  public function testGetterDeFormulaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('FormulaMedia_Model_FormulaDataMapper', $this->_mapper->getFormulaDataMapper());
  }

  public function testGetterDeTabelaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaDataMapper', $this->_mapper->getTabelaDataMapper());
  }

  public function testFindFormulaMediaFinalDataMapper()
  {
    // Valores de retorno
    $returnValue = array(new FormulaMedia_Model_Formula(
      array(
        'id' => 1,
        'nome' => '1º ao 3º ano',
        'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_FINAL
      )
    ));

    // Mock para área de conhecimento
    $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper->setFormulaDataMapper($mock);
    $formulas = $this->_mapper->findFormulaMediaFinal();

    $this->assertEquals($returnValue, $formulas);
  }

  public function testFindFormulaMediaRecuperacaoDataMapper()
  {
    // Valores de retorno
    $returnValue = array(new FormulaMedia_Model_Formula(
      array(
        'id' => 1,
        'nome' => '1º ao 3º ano',
        'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
      )
    ));

    // Mock para área de conhecimento
    $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper->setFormulaDataMapper($mock);
    $formulas = $this->_mapper->findFormulaMediaRecuperacao();

    $this->assertEquals($returnValue, $formulas);
  }

  public function testFindTabelaArredondamento()
  {
    // Instância de RegraAvaliacao_Model_Regra
    $instance = new RegraAvaliacao_Model_Regra(array('instituicao' => 1));

    // Valores de retorno
    $returnValue = array(new TabelaArredondamento_Model_Tabela(
      array(
        'id' => 1,
        'instituicao' => 1,
        'nome' => 'Tabela geral de notas numéricas',
        'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
      )
    ));

    // Mock para tabela de arredondamento
    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->with(array(), array(
            'instituicao' => 1,
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA))
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padrão pelo mock
    $this->_mapper->setTabelaDataMapper($mock);
    $tabelas = $this->_mapper->findTabelaArredondamento($instance);
  }
}