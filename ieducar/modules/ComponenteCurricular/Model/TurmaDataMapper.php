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
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'ComponenteCurricular/Model/Turma.php';

/**
 * ComponenteCurricular_Model_TurmaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_TurmaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'ComponenteCurricular_Model_Turma';
  protected $_tableName   = 'componente_curricular_turma';
  protected $_tableSchema = 'modules';

  /**
   * Os atributos anoEscolar e escola estão presentes apenas para
   * fins de desnormalização.
   * @var array
   */
  protected $_attributeMap = array(
    'componenteCurricular' => 'componente_curricular_id',
    'anoEscolar'           => 'ano_escolar_id',
    'escola'               => 'escola_id',
    'turma'                => 'turma_id',
    'cargaHoraria'         => 'carga_horaria'
  );

  protected $_primaryKey = array(
    'componenteCurricular', 'turma'
  );

  /**
   * Realiza uma operação de atualização em todas as instâncias persistidas de
   * ComponenteCurricular_Model_Turma. A atualização envolve criar, atualizar
   * e/ou apagar instâncias persistidas.
   *
   * No exemplo de código a seguir, se uma instância de
   * ComponenteCurricular_Model_Turma com uma referência a componenteCurricular
   * "1" existisse, esta teria seus atributos atualizados e persistidos
   * novamente. Se a referência não existisse, uma nova instância de
   * ComponenteCurricular_Model_Turma seria criada e persistida. Caso uma
   * referência a "2" existisse, esta seria apagada por não estar referenciada
   * no array $componentes.
   *
   * <code>
   * <?php
   * $componentes = array(
   *   array('id' => 1, 'cargaHoraria' => 100)
   * );
   * $mapper->bulkUpdate(1, 1, 1, $componentes);
   * </code>
   *
   *
   *
   * @param  int   $anoEscolar  O código do ano escolar/série.
   * @param  int   $escola      O código da escola.
   * @param  int   $turma       O código da turma.
   * @param  array $componentes (id => integer, cargaHoraria => float|null)
   * @throws Exception
   */
  public function bulkUpdate($anoEscolar, $escola, $turma, array $componentes)
  {
    $update = $insert = $delete = array();

    $componentesTurma = $this->findAll(array(), array('turma'  => $turma));

    $objects = array();
    foreach ($componentesTurma as $componenteTurma) {
      $objects[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
    }

    foreach ($componentes as $componente) {
      $id = $componente['id'];

      if (isset($objects[$id])) {
        $insert[$id] = $objects[$id];
        $insert[$id]->cargaHoraria = $componente['cargaHoraria'];
        continue;
      }

      $insert[$id] = new ComponenteCurricular_Model_Turma(array(
        'componenteCurricular' => $id,
        'anoEscolar'           => $anoEscolar,
        'escola'               => $escola,
        'turma'                => $turma,
        'cargaHoraria'         => $componente['cargaHoraria']
      ));
    }

    $delete = array_diff(array_keys($objects), array_keys($insert));

    foreach ($delete as $id) {
      $this->delete($objects[$id]);
    }

    foreach ($insert as $entry) {
      $this->save($entry);
    }
  }
}