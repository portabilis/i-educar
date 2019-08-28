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
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once __DIR__.'/TestCommon.php';

/**
 * Avaliacao_Service_InicializacaoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_InicializacaoTest extends Avaliacao_Service_TestCommon
{
  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testInstanciaLancaExcecaoCasoCodigoDeMatriculaNaoSejaInformado()
  {
    new Avaliacao_Service_Boletim();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInstanciaLancaExcecaoComOpcaoNaoAceitaPelaClasse()
  {
    new Avaliacao_Service_Boletim(array('matricula' => 1, 'foo' => 'bar'));
  }

  public function testDadosDeMatriculaInicializados()
  {
      //Método _hydrateComponentes em IedFinder foi alterado. Terá que ser escrito um novo teste
      $this->markTestSkipped();
    $service = $this->_getServiceInstance();
    $options = $service->getOptions();

    $this->assertEquals($this->_getConfigOption('usuario', 'cod_usuario'),
      $options['usuario']);

    $this->assertEquals($this->_getConfigOption('matricula', 'aprovado'),
      $options['aprovado']);

    $this->assertEquals($this->_getConfigOption('curso', 'hora_falta'),
      $options['cursoHoraFalta']);

    $this->assertEquals($this->_getConfigOption('curso', 'carga_horaria'),
      $options['cursoCargaHoraria']);

    $this->assertEquals($this->_getConfigOption('serie', 'carga_horaria'),
      $options['serieCargaHoraria']);

    $this->assertEquals(count($this->_getConfigOptions('anoLetivoModulo')),
      $options['etapas']);

    $expected = $this->_getConfigOptions('componenteCurricular');
    $dispensas = $this->_getDispensaDisciplina();
    foreach($dispensas as $dispensa) {
        unset($expected[$dispensa['ref_cod_disciplina']]);
    }
    $actual = $service->getComponentes();
    $this->assertEquals($expected, $actual);
  }

  public function testInstanciaRegraDeAvaliacaoAtravesDeUmNumeroDeMatricula()
  {
    $service = $this->_getServiceInstance();
    $this->assertInstanceOf('RegraAvaliacao_Model_Regra', $service->getRegra());

    // TabelaArredondamento_Model_Tabela é recuperada através da instância de
    // RegraAvaliacao_Model_Regra
    $this->assertInstanceOf('TabelaArredondamento_Model_Tabela', $service->getRegraAvaliacaoTabelaArredondamento());
  }
}
