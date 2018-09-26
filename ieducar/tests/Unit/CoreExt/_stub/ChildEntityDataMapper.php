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
require_once __DIR__.'/ChildEntity.php';

/**
 * CoreExt_ChildEntityDataMapperStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_ChildEntityDataMapperStub extends CoreExt_DataMapper
{
  protected $_entityClass = 'CoreExt_ChildEntityStub';
  protected $_tableName   = 'child';
  protected $_tableSchema = '';

  protected $_attributeMap = array(
    'tipoSanguineo' => 'tipo_sanguineo'
  );

  /**
   * Cria a tabela "child" para testes de integração.
   *
   * SQL compatível com SQLite.
   *
   * @param  clsBancoPdo $db
   * @return mixed Retorna FALSE em caso de erro
   */
  public static function createTable(clsBanco $db)
  {
    $sql = "
CREATE TABLE child(
  id integer primary key,
  nome character varying(100) NOT NULL,
  sexo integer default '1',
  tipo_sanguineo integer default '1',
  peso real default '0'
);";
    return $db->Consulta($sql);
  }
}