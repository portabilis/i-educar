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

use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;

require_once __DIR__.'/TestCommon.php';

/**
 * Avaliacao_Service_SituacaoTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_SituacaoTest extends Avaliacao_Service_TestCommon
{
  public function testSituacaoAluno()
  {
    $nota  = new stdClass();
    $falta = new stdClass();

    $service = $this->setExcludedMethods(['getSituacaoAluno', 'getSituacaoNotaFalta'])
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $regra = $this->getCleanMock('RegraAvaliacao_Model_Regra');

    $regra->expects($this->any())
        ->method('get')
        ->will($this->returnValue(0));

    $service->expects($this->any())
        ->method('getRegra')
        ->will($this->returnValue($regra));

    $registration = factory(LegacyRegistration::class)->create([
        'ref_ref_cod_serie' => factory(LegacyLevel::class)->create(),
        'dependencia' => true,
    ]);
    $service->expects($this->any())
        ->method('getOption')
        ->will($this->returnValue($registration->toArray()));

    $notaSituacoes = [
      1 => App_Model_MatriculaSituacao::APROVADO,
      2 => App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
      3 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      4 => App_Model_MatriculaSituacao::EM_EXAME,
      5 => App_Model_MatriculaSituacao::REPROVADO
    ];

    $faltaSituacoes = [
      1 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      2 => App_Model_MatriculaSituacao::APROVADO,
      3 => App_Model_MatriculaSituacao::REPROVADO
    ];

    // Possibilidades
    $expected = [
        1 => [  //Aprova Andame Retido Recupe
            1 => [FALSE, TRUE,  FALSE, FALSE],
            2 => [TRUE,  FALSE, FALSE, FALSE],
            3 => [TRUE,  FALSE, TRUE,  FALSE]
        ],
        2 => [
            1 => [FALSE, TRUE,  FALSE, TRUE ],
            2 => [TRUE,  FALSE, FALSE, TRUE ],
            3 => [TRUE,  FALSE, TRUE,  TRUE ]
        ],
        3 => [
            1 => [FALSE, TRUE,  FALSE, FALSE],
            2 => [FALSE, TRUE,  FALSE, FALSE],
            3 => [FALSE, FALSE, TRUE,  FALSE]
        ],
        4 => [
            1 => [FALSE, TRUE,  FALSE, TRUE ],
            2 => [FALSE, TRUE,  FALSE, TRUE ],
            3 => [FALSE, TRUE,  TRUE,  TRUE ]
        ],
        5 => [
            1 => [FALSE, TRUE,  FALSE, FALSE],
            2 => [FALSE, FALSE, FALSE, FALSE],
            3 => [FALSE, FALSE, TRUE,  FALSE]
        ]
    ];

    foreach ($notaSituacoes as $i => $notaSituacao) {
      $nota->situacao = $notaSituacao;

      foreach ($faltaSituacoes as $ii => $faltaSituacao) {
        $falta->situacao = $faltaSituacao;

        $service->expects($this->any())
                ->method('getSituacaoFaltas')
                ->will($this->returnValue($falta));

        $service->expects($this->any())
                ->method('getSituacaoNotas')
                ->will($this->returnValue($nota));

        $service->expects($this->any())
                ->method('hasRegraAvaliacaoMediaRecuperacao')
                ->willReturn(true);

        $service->expects($this->any())
                ->method('getRegraAvaliacaoTipoNota')
                ->willReturn(null);

        $situacao = $service->getSituacaoAluno();

        $this->assertEquals($expected[$i][$ii][0], $situacao->aprovado, "Aprovado, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][1], $situacao->andamento, "Andamento, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][2], $situacao->retidoFalta, "Retido por falta, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][3], $situacao->recuperacao, "Recuperação, caso $i - $ii");
      }
    }
  }
}
