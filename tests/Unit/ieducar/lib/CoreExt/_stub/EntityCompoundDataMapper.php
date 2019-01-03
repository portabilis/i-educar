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
require_once __DIR__.'/EntityCompound.php';

/**
 * CoreExt_EntityCompoundDataMapperStub class.
 *
 * Entidade para testes de integração do componente CoreExt_DataMapper com
 * o banco de dados.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_EntityCompoundDataMapperStub extends CoreExt_DataMapper
{
  protected $_entityClass = 'CoreExt_EntityCompoundStub';
  protected $_tableName   = 'matricula';
  protected $_tableSchema = '';

  protected $_attributeMap = array(
    'pessoa' => 'pessoa_id',
    'curso' => 'curso_id'
  );

  protected $_primaryKey = array(
    'pessoa' => 'pessoa',
    'curso' => 'curso'
  );

  /**
   * Cria a tabela pessoa para testes de integração.
   *
   * SQL compatível com SQLite.
   *
   * @param  clsBancoPdo $db
   * @return mixed Retorna FALSE em caso de erro
   */
  public static function createTable(clsBanco $db)
  {
    $sql = "
CREATE TABLE matricula(
  pessoa_id integer NOT NULL,
  curso_id integer NOT NULL,
  confirmado char(1) NULL DEFAULT 't',
  PRIMARY KEY(pessoa_id, curso_id)
);";

    return $db->Consulta($sql);
  }
}