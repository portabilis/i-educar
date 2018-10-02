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
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once __DIR__.'/ParentEntity.php';

/**
 * CoreExt_ParentEntityDataMapperStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_ParentEntityDataMapperStub extends CoreExt_DataMapper
{
  protected $_entityClass = 'CoreExt_ParentEntityStub';
  protected $_tableName   = 'parent';
  protected $_tableSchema = '';

  protected $_attributeMap = array(
    'filho' => 'filho_id'
  );

  /**
   * Cria a tabela "parent" para testes de integração.
   *
   * SQL compatível com SQLite.
   *
   * @param  clsBancoPdo $db
   * @return mixed Retorna FALSE em caso de erro
   */
  public static function createTable(clsBanco $db)
  {
    $sql = "
CREATE TABLE parent(
  id integer primary key,
  filho_id integer,
  nome character varying(100) NOT NULL
);";
    return $db->Consulta($sql);
  }
}