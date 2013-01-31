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
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.2.0
 * @version     $Id$
 */

require_once 'Educacenso/Model/Aluno.php';
require_once 'Educacenso/Model/CodigoReferenciaDataMapper.php';

/**
 * Educacenso_Model_AlunoDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_AlunoDataMapper extends Educacenso_Model_CodigoReferenciaDataMapper
{
  protected $_entityClass = 'Educacenso_Model_Aluno';
  protected $_tableName   = 'educacenso_cod_aluno';

  protected $_attributeMap = array(
    'aluno'      => 'cod_aluno',
    'alunoInep'  => 'cod_aluno_inep',
    'nomeInep'   => 'nome_inep',
    'fonte'      => 'fonte',
    'created_at' => 'created_at',
    'updated_at' => 'updated_at'
  );

  // aparentemente o campo alunoInep não deveria fazer parte da chave primaria, pois este pode
  // ser alterado no cadastro de aluno, #TODO criar migracao para remover PK de tal campo ?
  protected $_primaryKey = array(
    'aluno' #, 'alunoInep'
  );

  // fixup para find funcionar em tabelas cujo PK não se chama id
  protected function _getFindStatment($pkey)
  {
    if (! is_array($pkey))
      $pkey = array('cod_aluno' => $pkey);

    return parent::_getFindStatment($pkey);
  }
}