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
require_once 'Avaliacao/Model/NotaComponente.php';

/**
 * Avaliacao_Service_NotaTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_NotaTest extends Avaliacao_Service_TestCommon
{
  public function testInstanciaDeNotaComponenteERegistradaApenasUmaVezNoBoletiom()
  {
    $service = $this->_getServiceInstance();

    $nota = new Avaliacao_Model_NotaComponente(array(
      'componenteCurricular' => 1,
      'nota'                 => "5.1"
    ));

    // Atribuição simples
    $service->addNota($nota)
            ->addNota($nota);

    $this->assertEquals(1, count($service->getNotas()));

    // Via atribuição em lote
    $nota = clone $nota;
    $service->addNotas(array($nota, $nota, $nota));

    $this->assertEquals(2, count($service->getNotas()));
  }

  public function testAdicionaNotaNoBoletim()
  {
      $this->markTestSkipped();
    $service = $this->_getServiceInstance();

    $nota = new Avaliacao_Model_NotaComponente(array(
      'componenteCurricular' => 1,
      'nota'                 => 5.72
    ));

    $notaOriginal = clone $nota;
    $service->addNota($nota);

    $notas = $service->getNotas();
    $serviceNota = array_shift($notas);

    // Valores declarados explicitamente, verificação explícita
    $this->assertEquals($notaOriginal->nota, $serviceNota->nota);
    $this->assertEquals($notaOriginal->get('componenteCurricular'), $serviceNota->get('componenteCurricular'));

    // Valores populados pelo service
    $this->assertNotEquals($notaOriginal->etapa, $serviceNota->etapa);
    $this->assertEquals(1, $serviceNota->etapa);
    $this->assertEquals(5, $serviceNota->notaArredondada);

    // Validadores injetados no objeto
    $validators = $serviceNota->getValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['componenteCurricular']);
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);

    // Opções dos validadores

    // Componentes curriculares existentes para o aluno
    $this->assertEquals(
      array_keys($this->_getConfigOptions('componenteCurricular')),
      array_values($validators['componenteCurricular']->getOption('choices'))
    );

    // Etapas possíveis para o lançamento de nota
    $this->assertEquals(
      array_merge(range(1, count($this->_getConfigOptions('anoLetivoModulo'))), array('Rc')),
      $validators['etapa']->getOption('choices')
    );
  }

  /**
   * Testa o service adicionando notas de apenas um componente curricular,
   * para todas as etapas regulares (1 a 4).
   */
  public function testSalvarNotasDeUmComponenteCurricularNoBoletim()
  {
      $this->markTestSkipped();
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 7.25,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 9.25,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 8,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 8.5,
        'etapa'                => 4
      )),
    );

    $media = new Avaliacao_Model_NotaComponenteMedia(array(
      'notaAluno'            => $notaAluno->id,
      'componenteCurricular' => 1,
      'media'                => 8.25,
      'mediaArredondada'     => 8,
      'etapa'                => 4
    ));

    $media->markOld();

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($notas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($notas[1])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(3))
         ->method('save')
         ->with($notas[2])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(4))
         ->method('save')
         ->with($notas[3])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(5))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notas));

    $this->_setNotaComponenteDataMapperMock($mock);

    // Configura mock para Avaliacao_Model_NotaComponenteMediaDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('find')
         ->with(array($notaAluno->id, $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(NULL));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($media)
         ->will($this->returnValue(TRUE));

    $this->_setNotaComponenteMediaDataMapperMock($mock);

    $service = $this->_getServiceInstance();

    $service->addNotas($notas);
    $service->saveNotas();
  }

  /**
   * Testa o service adicionando novas notas para um componente curricular,
   * que inclusive já tem a nota lançada para a segunda etapa.
   */
  public function testSalvasNotasDeUmComponenteComEtapasLancadas()
  {
      $this->markTestSkipped();
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 7.25,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 9.25,
        'etapa'                => 3
      ))
    );

    $notasPersistidas = array(
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 1,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 8.25,
        'notaArredondada'      => 8,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 2,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 9.5,
        'notaArredondada'      => 9,
        'etapa'                => 2
      ))
    );

    $mediasPersistidas = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 4.4375,
        'mediaArredondada'     => 4,
        'etapa'                => 2
      ))
    );

    $mediasPersistidas[0]->markOld();

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($notas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($notas[1])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(3))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array($notasPersistidas[0], $notas[0], $notas[1])));

    $this->_setNotaComponenteDataMapperMock($mock);

    // Configura mock para Avaliacao_Model_NotaComponenteMediaDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id))
         ->will($this->returnValue($mediasPersistidas));

    $mock->expects($this->at(1))
         ->method('find')
         ->with(array($notaAluno->id, $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue($mediasPersistidas[0]));

    // Valores de média esperados
    $media = clone $mediasPersistidas[0];
    $media->etapa = 3;
    $media->media = 6.1875;
    $media->mediaArredondada = 6;

    $mock->expects($this->at(2))
         ->method('save')
         ->with($media)
         ->will($this->returnValue(TRUE));

    $this->_setNotaComponenteMediaDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addNotas($notas);
    $service->saveNotas();
  }
}