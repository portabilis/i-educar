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

// Dependências do próprio módulo
require_once 'Avaliacao/Service/Boletim.php';
require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Model/NotaComponenteMediaDataMapper.php';
require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
require_once 'Avaliacao/Model/FaltaComponenteDataMapper.php';

// Dependência de outros módulos
require_once 'AreaConhecimento/Model/AreaDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

// Dependência de classes do namespace Ied_Pmieducar
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';

/**
 * BoletimTest class.
 *
 * Testa a API do service Avaliacao_Service_Boletim. Cria uma interface para
 * a configuração de uma instância de RegraAvaliacao_Model_Regra, com o qual
 * o service é bastante dependente.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @todo        Todos os getters de DataMapper precisam de um teste para
 *   verificar se de fato retornam uma instância padrão
 * @version     @@package_version@@
 */
abstract class Boletim_Common extends UnitBaseTest
{
  /**
   * @var Avaliacao_Service_Boletim
   */
  protected $_service = NULL;

  /**
   * @var Avaliacao_Model_NotaAluno
   */
  protected $_notaAlunoExpected = NULL;

  /**
   * @var Avaliacao_Model_FaltaAluno
   */
  protected $_faltaAlunoExpected = NULL;

  /**
   * @var ComponenteCurricular_Model_ComponenteDataMapper
   */
  protected $_componenteDataMapperMock = NULL;

  /**
   * Configura todos os mocks de depenência de Avaliacao_Service_Boletim
   */
  protected function setUp()
  {
    $this->_setUpLegacyMock();

    // Instancia a classe Service
    $this->_service = new Avaliacao_Service_Boletim(array(
      'matricula'                => 1,
      'usuario'                  => 1,
      'RegraDataMapper'          => $this->_setUpRegraAvaliacao(),
      'ComponenteDataMapper'     => $this->_componenteDataMapperMock,
      'NotaAlunoDataMapper'      => $this->_setUpNotaAlunoDataMapper(),
      'NotaComponenteDataMapper' => $this->_setUpNotaComponenteDataMapper(),
      'NotaComponenteMediaDataMapper' => $this->_setUpNotaComponenteMediaDataMapper(),
      'FaltaAlunoDataMapper'     => $this->_setUpFaltaAlunoDataMapperMock(),
      'FaltaAbstractDataMapper'  => $this->_setUpFaltaAbstractDataMapperMock(),
    ));
  }

  /**
   * Configura um mock de RegraAvaliacao_Model_Regra.
   * @return RegraAvaliacao_Model_Regra
   */
  protected abstract function _setUpRegraAvaliacao();

  /**
   * Configura um mock de Avaliacao_Model_NotaAlunoDataMapper.
   *
   * @return Avaliacao_Model_NotaAlunoDataMapper
   */
  protected abstract function _setUpNotaAlunoDataMapper();

  /**
   * Configura uma instância de Avaliacao_Model_NotaAluno e guarda no atributo
   * $_notaAlunoExpected.
   */
  protected abstract function _setUpNotaAluno();

  /**
   * Configura um mock de Avaliacao_Model_NotaComponenteDataMapper.
   * @return Avaliacao_Model_NotaComponenteDataMapper
   */
  protected abstract function _setUpNotaComponenteDataMapper();

  /**
   * Configura um mock de Avaliacao_Model_NotaMediaComponenteDataMapper.
   * @return Avaliacao_Model_NotaComponenteMediaDataMapper
   */
  protected abstract function _setUpNotaComponenteMediaDataMapper();

  /**
   * Configura um mock de Avaliacao_Model_FaltaAlunoDataMapper e guarda uma
   * instância (a que for salva pelo mock) no atributo $_faltaAlunoExpected.
   *
   * @return Avaliacao_Model_FaltaAlunoDataMapper
   */
  protected abstract function _setUpFaltaAlunoDataMapperMock();

  protected abstract function _setUpFaltaAluno();

  /**
   * Configura um mock de Avaliacao_Model_FataAbstractDataMapper (Componente
   * ou Geral).
   *
   * @return unknown_type
   */
  protected abstract function _setUpFaltaAbstractDataMapperMock();

  /**
   * Configura mocks para as classes legadas (Ied_*).
   */
  protected function _setUpLegacyMock()
  {
    $this->_configuraDadosMatricula()
         ->_configuraDadosDisciplina()
         ->_configuraDadosEtapasCursadas();
  }

  protected function _configuraDadosMatricula()
  {
    // Retorna para matrícula
    $returnMatricula = array(
      'cod_matricula'       => 1,
      'ref_ref_cod_escola'  => 1,
      'ref_ref_cod_serie'   => 1,
      'ref_cod_curso'       => 1,
      'aprovado'            => 1,
      'curso_carga_horaria' => (800 * 9),
      'curso_hora_falta'    => (50 /60),
      'serie_carga_horaria' => 800
    );

    // Mock para clsPmieducarMatricula
    $matriculaMock = $this->getCleanMock('clsPmieducarMatricula');
    $matriculaMock->expects($this->any())
                  ->method('detalhe')
                  ->will($this->returnValue($returnMatricula));

    // Registra a instância no repositório de classes de CoreExt_Entity
    CoreExt_Entity::addClassToStorage('clsPmieducarMatricula',
      $matriculaMock, NULL, TRUE
    );

    // Retorno para clsPmieducarSerie
    $returnSerie = array(
      'cod_serie' => 1,
      'regra_avaliacao_id' => 1,
      'carga_horaria' => 800
    );

    // Mock para clsPmieducarMatricula
    $serieMock = $this->getCleanMock('clsPmieducarSerie');
    $serieMock->expects($this->any())
              ->method('detalhe')
              ->will($this->returnValue($returnSerie));

    // Registra a instância no repositório de classes de CoreExt_Entity
    CoreExt_Entity::addClassToStorage('clsPmieducarSerie',
      $serieMock, NULL, TRUE
    );


    // Retorno para clsPmieducarCurso
    $returnCurso = array(
      'cod_curso' => 1,
      'carga_horaria' => (800 * 9),
      'hora_falta' => (50 / 60),
      'padrao_ano_escolar' => 1
    );

    // Mock para clsPmieducarCurso
    $cursoMock = $this->getCleanMock('clsPmieducarCurso');
    $cursoMock->expects($this->any())
              ->method('detalhe')
              ->will($this->returnValue($returnCurso));

    // Registra a instância no repositório de classes de CoreExt_Entity
    CoreExt_Entity::addClassToStorage('clsPmieducarCurso',
      $cursoMock, NULL, TRUE
    );

    return $this;
  }

  protected function _configuraDadosDisciplina()
  {
    $componentes = array(
      new ComponenteCurricular_Model_Componente(
        array('id' => 1, 'nome' => 'Matemática', 'cargaHoraria' => 100)
      ),
      new ComponenteCurricular_Model_Componente(
        array('id' => 2, 'nome' => 'Português', 'cargaHoraria' => 100)
      ),
      new ComponenteCurricular_Model_Componente(
        array('id' => 3, 'nome' => 'Ciências', 'cargaHoraria' => 60)
      ),
      new ComponenteCurricular_Model_Componente(
        array('id' => 4, 'nome' => 'Física', 'cargaHoraria' => 60)
      )
    );

    $expected = array(
      $componentes[0],
      $componentes[2]
    );

    // Retorna para clsPmieducarEscolaSerieDisciplina
    $returnEscolaSerieDisciplina = array(
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 1, 'carga_horaria' => 100),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 2, 'carga_horaria' => 100),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 3, 'carga_horaria' => 70),
      array('ref_cod_serie' => 1, 'ref_cod_disciplina' => 4, 'carga_horaria' => 100),
    );

    // Mock para clsPmieducarEscolaSerieDisciplina
    $escolaMock = $this->getCleanMock('clsPmieducarEscolaSerieDisciplina');
    $escolaMock->expects($this->any())
               ->method('lista')
               ->will($this->returnValue($returnEscolaSerieDisciplina));

    // Retorna para clsPmieducarDispensaDisciplina
    $returnDispensa = array(
      array('ref_cod_matricula' => 1, 'ref_cod_disciplina' => 2),
      array('ref_cod_matricula' => 1, 'ref_cod_disciplina' => 4),
    );

    // Mock para clsPmieducarDispensaDisciplina
    $dispensaMock = $this->getCleanMock('clsPmieducarDispensaDisciplina');
    $dispensaMock->expects($this->any())
                 ->method('lista')
                 ->with(1, 1, 1)
                 ->will($this->returnValue($returnDispensa));

    // Mock para ComponenteCurricular_Model_ComponenteDataMapper
    $mapperMock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $mapperMock->expects($this->any())
               ->method('findComponenteCurricularAnoEscolar')
               ->will($this->onConsecutiveCalls($expected[0], $expected[1]));

    // Guarda na instância, usado em setUp()
    $this->_componenteDataMapperMock = $mapperMock;

    // Registra mocks
    CoreExt_Entity::addClassToStorage('clsPmieducarEscolaSerieDisciplina',
      $escolaMock, NULL, TRUE);
    CoreExt_Entity::addClassToStorage('clsPmieducarDispensaDisciplina',
      $dispensaMock, NULL, TRUE);

    return $this;
  }

  /**
   * É dependente do mock de clsPmieducarCurso de _configuraDadosMatricula
   * (padrao_ano_escolar).
   */
  protected function _configuraDadosEtapasCursadas()
  {
    $returnEscolaAno = array(
      array('ref_cod_escola' => 1, 'ano' => 2009, 'andamento' => 1, 'ativo' => 1)
    );

    $returnAnoLetivo = array(
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 1),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 2),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 3),
      array('ref_ano' => 2009, 'ref_ref_cod_escola' => 1, 'sequencial' => 4)
    );

    // Mock para escola ano letivo (ano letivo em andamento)
    $escolaAnoMock = $this->getCleanMock('clsPmieducarEscolaAnoLetivo');
    $escolaAnoMock->expects($this->any())
                  ->method('lista')
                  ->with(1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1)
                  ->will($this->returnValue($returnEscolaAno));

    // Mock para o ano letivo (módulos do ano)
    $anoLetivoMock = $this->getCleanMock('clsPmieducarAnoLetivoModulo');
    $anoLetivoMock->expects($this->any())
                  ->method('lista')
                  ->with(2009, 1)
                  ->will($this->returnValue($returnAnoLetivo));

    // Adiciona mocks ao repositório estático
    CoreExt_Entity::addClassToStorage('clsPmieducarEscolaAnoLetivo',
      $escolaAnoMock, NULL, TRUE);
    CoreExt_Entity::addClassToStorage('clsPmieducarAnoLetivoModulo',
      $anoLetivoMock, NULL, TRUE);

    return $this;
  }

  /**
   * @todo Refatorar método para uma classe stub, no diretório do módulo
   *   TabelaArredondamento
   * @todo Está copiado em tests/Unit/App/Model/IedFinderTest.php
   */
  protected function _getTabelaArredondamentoNumerica()
  {
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => -1,
      'valorMaximo'          => 0
    );

    $tabelaValores = array();

    for ($i = 0; $i <= 10; $i++) {
      $data['nome'] = $i;
      $data['valorMinimo'] += 1;
      $data['valorMaximo'] += 1;

      if ($i == 10) {
        $data['valorMinimo'] = 9;
        $data['valorMaximo'] = 10;
      }

      $tabelaValores[$i] = new TabelaArredondamento_Model_TabelaValor($data);
    }

    $mapperMock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mapperMock->expects($this->any())
               ->method('findAll')
               ->will($this->returnValue($tabelaValores));

    $tabelaDataMapper = new TabelaArredondamento_Model_TabelaDataMapper();
    $tabelaDataMapper->setTabelaValorDataMapper($mapperMock);

    $tabela = new TabelaArredondamento_Model_Tabela(array('nome' => 'Numéricas'));
    $tabela->setDataMapper($tabelaDataMapper);
    return $tabela;
  }
}