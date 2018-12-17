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
 * @version     $Id: /ieducar/branches/1.1.0-dev/ieducar/tests/unit/CoreExt/_stub/Entity.php 586 2009-10-14T23:26:48.478692Z eriksen  $
 */



/**
 * CoreExt_EntityDataMapperStub class.
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
class CoreExt_EntityDataMapperStub extends CoreExt_DataMapper
{
  protected $_entityClass = 'CoreExt_EntityStub';
  protected $_tableName   = 'pessoa';
  protected $_tableSchema = '';

  protected $_attributeMap = array(
    'estadoCivil' => 'estado_civil'
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
CREATE TABLE pessoa(
  id integer primary key,
  nome character varying(100) NOT NULL,
  estado_civil character varying(20) NOT NULL DEFAULT 'solteiro',
  doador char(1) NULL DEFAULT 't'
);";

    return $db->Consulta($sql);
  }
}