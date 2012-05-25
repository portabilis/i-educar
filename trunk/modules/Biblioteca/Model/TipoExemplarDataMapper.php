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
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'Biblioteca/Model/TipoExemplar.php';

/**
 * Usuario_Model_TipoExemplarDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Biblioteca_Model_TipoExemplarDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Biblioteca_Model_TipoExemplar';
  protected $_tableName   = 'exemplar_tipo';
  protected $_tableSchema = 'pmieducar';


  protected $_attributeMap = array(
    'cod_exemplar_tipo'  => 'cod_exemplar_tipo',
    'ref_cod_biblioteca' => 'ref_cod_biblioteca',
    'ref_usuario_exc'    => 'ref_usuario_exc',
    'ref_usuario_cad'    => 'ref_usuario_cad',
    'nm_tipo'            => 'nm_tipo',
    'descricao'          => 'descricao',
    'data_cadastro'      => 'data_cadastro',
    'data_exclusao'      => 'data_exclusao',
    'ativo'              => 'ativo'
  );


  protected $_notPersistable = array();


  protected $_primaryKey = array('cod_exemplar_tipo');


  protected function _getFindStatment($pkey)
  {
    if (!is_array($pkey))
      $pkey = array("cod_exemplar_tipo" => $pkey);

    return parent::_getFindStatment($pkey);
  }
}
